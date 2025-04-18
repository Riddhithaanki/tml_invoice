<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Models\Invoice; // Add the Invoice model

class SageController extends Controller
{
    public function getInvoices()
    {
        set_time_limit(300); // Avoid timeout for large fetches

        $baseUrl = 'http://193.117.210.98:5495/sdata/accounts50/GCRM/%7B6447298A-F14B-48EA-9EAC-A3955968B321%7D';
        $username = 'act';
        $password = 'act';

        try {
            // Fetch invoices with postal addresses (adjust $filter as required)
            $invoiceUrl = $baseUrl . "/salesInvoices?\$filter=date ge datetime'2024-04-01'&\$orderby=reference desc&count=20&include=postalAddresses&format=json";
            $invoiceResponse = Http::withBasicAuth($username, $password)
                ->accept('application/json')
                ->withHeaders(['User-Agent' => 'LaravelApp/1.0'])
                ->timeout(3000)
                ->get($invoiceUrl);

            // Remove UTF-8 BOM if exists
            $rawInvoices = preg_replace('/^\xEF\xBB\xBF/', '', $invoiceResponse->body());

            if (!$invoiceResponse->successful()) {
                throw new \Exception('Invoice API error: ' . $invoiceResponse->status());
            }

            $invoiceData = json_decode($rawInvoices, true);
            if (!isset($invoiceData['$resources'])) {
                throw new \Exception('Invoice response missing $resources');
            }
            $invoices = $invoiceData['$resources'];

            // Get companies data once from Sage, so we can relate invoice to local company if needed.
            $companyUrl = $baseUrl . '/tradingAccounts?count=4000&format=json';
            $companyResponse = Http::withBasicAuth($username, $password)
                ->accept('application/json')
                ->withHeaders(['User-Agent' => 'LaravelApp/1.0'])
                ->timeout(120)
                ->get($companyUrl);

            $rawCompanies = preg_replace('/^\xEF\xBB\xBF/', '', $companyResponse->body());
            if (!$companyResponse->successful()) {
                throw new \Exception('Company API error: ' . $companyResponse->status());
            }

            $companyData = json_decode($rawCompanies, true);
            $companies = $companyData['$resources'];
            $companyMap = [];
            foreach ($companies as $company) {
                if (isset($company['reference'])) {
                    $companyMap[$company['reference']] = $company;
                }
            }

            $storedInvoices = [];

            foreach ($invoices as $invoice) {
                // Parse Sage dates using your parseSageDate() method
                $invoice['date'] = $this->parseSageDate($invoice['date'] ?? '');
                $invoice['taxDate'] = $this->parseSageDate($invoice['taxDate'] ?? '');

                // Match company: find local company based on customerTradingAccountId
                $localCompany = null;
                $uuid = $invoice['customerTradingAccountId']['$uuid'] ?? null;
                if ($uuid) {
                    $localCompany = Company::where('SageUUID', $uuid)->first();
                }

                // Parse addresses (billing and shipping) if available
                $billingAddress = '';
                $shippingAddress = '';
                if (isset($invoice['postalAddresses']['$resources'])) {
                    foreach ($invoice['postalAddresses']['$resources'] as $address) {
                        $fullAddress = implode(', ', array_filter([
                            $address['address1'] ?? '',
                            $address['address2'] ?? '',
                            $address['townCity'] ?? '',
                            $address['stateRegion'] ?? '',
                            $address['zipPostCode'] ?? '',
                            $address['country'] ?? '',
                        ]));
                        if (isset($address['type']) && $address['type'] === 'Billing') {
                            $billingAddress = $fullAddress;
                        } elseif (isset($address['type']) && $address['type'] === 'Shipping') {
                            $shippingAddress = $fullAddress;
                        }
                    }
                }

                // Save the invoice in the database
                $stored = Invoice::create([
                    'uuid' => $invoice['$uuid'], // Store the invoice's UUID from Sage
                    'reference' => $invoice['reference'],
                    'customer_id' => $localCompany ? $localCompany->id : null,
                    'status' => $invoice['status'],
                    'type' => $invoice['type'],
                    'date' => $invoice['date'],
                    'tax_date' => $invoice['taxDate'],
                    'net_total' => $invoice['netTotal'],
                    'discount_total' => $invoice['discountTotal'] ?? 0,
                    'charges_total' => $invoice['chargesTotal'] ?? 0,
                    'tax_total' => $invoice['taxTotal'],
                    'gross_total' => $invoice['grossTotal'],
                    'currency' => $invoice['currency'],
                    'exchange_rate' => $invoice['operatingCompanyCurrencyExchangeRate'] ?? 1,
                    'customer_reference' => $invoice['customerReference'],
                    'sales_order_reference' => $invoice['salesOrderReference'] ?? null,
                    'notes' => $invoice['notes'] ?? '',
                    'billing_address' => $billingAddress,
                    'shipping_address' => $shippingAddress,
                ]);

                // Fetch invoice lines for this invoice using its Sage UUID
                if (isset($invoice['$uuid'])) {
                    // Adjust endpoint if necessary:
                    $lineUrl = $baseUrl . "/salesInvoices('{$invoice['$uuid']}')/invoiceLines?format=json";
                    $linesResponse = Http::withBasicAuth($username, $password)
                        ->accept('application/json')
                        ->withHeaders(['User-Agent' => 'LaravelApp/1.0'])
                        ->timeout(30)
                        ->get($lineUrl);
                    $linesData = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $linesResponse->body()), true);
                    $invoiceItems = $linesData['$resources'] ?? [];

                    // Optionally: Store raw lines JSON for reference
                    Storage::disk('local')->put("invoices/items_{$invoice['reference']}.json", json_encode($invoiceItems, JSON_PRETTY_PRINT));

                    // --- Save each invoice line in invoice_items table ---
                    foreach ($invoiceItems as $item) {
                        // We assume that the JSON includes a product/service description in the field "text"
                        \App\Models\InvoiceItem::create([
                            'uuid' => $item['$uuid'] ?? null,
                            'invoice_uuid' => $invoice['$uuid'],
                            'reference' => $item['reference'] ?? null,
                            'number' => $item['number'] ?? null,
                            'description' => $item['text'] ?? null, // product/service description
                            'quantity' => $item['quantity'] ?? 0,
                            'unit_price' => $item['actualPrice'] ?? 0,
                            'net_amount' => $item['netTotal'] ?? 0,
                            'discount_total' => $item['discountTotal'] ?? 0,
                            'tax_total' => $item['taxTotal'] ?? 0,
                            'gross_total' => $item['grossTotal'] ?? 0,
                        ]);
                    }

                    // Optional: Add invoice and its items to response array
                    $storedInvoices[] = [
                        'invoice' => $stored,
                        'items' => $invoiceItems,
                    ];
                }
            }

            return response()->json([
                'message' => 'Invoices and their items fetched and stored successfully',
                'data' => $storedInvoices,
            ]);
        } catch (\Exception $e) {
            Log::error('Sage fetch error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getInvoiceItemsByReference($reference)
    {
        set_time_limit(300); // Avoid timeout for large fetches

        $baseUrl = 'http://193.117.210.98:5495/sdata/accounts50/GCRM/%7B6447298A-F14B-48EA-9EAC-A3955968B321%7D';
        $username = 'act';
        $password = 'act';

        try {
            // Step 1: Get invoice by reference - Properly encode the filter and limit to 1 record
            $filter = urlencode("reference eq '$reference'");
            $invoiceUrl = $baseUrl . "/salesInvoices?\$filter=" . $filter . "&count=1&format=json";

            $invoiceResponse = Http::withBasicAuth($username, $password)
                ->accept('application/json')
                ->timeout(90) // Increase timeout to 90 seconds
                ->get($invoiceUrl);

            if (!$invoiceResponse->successful()) {
                throw new \Exception('Failed to fetch invoice. Status: ' . $invoiceResponse->status());
            }

            $invoiceData = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $invoiceResponse->body()), true);

            if (!isset($invoiceData['$resources'][0])) {
                throw new \Exception("No invoice found with reference $reference");
            }

            $uuid = $invoiceData['$resources'][0]['$uuid'];

            // Step 2: Fetch invoice items using UUID - Limit to 1 record
            $itemsUrl = $baseUrl . "/salesInvoices($uuid)/salesInvoiceLines?count=1&format=json";

            $itemsResponse = Http::withBasicAuth($username, $password)
                ->accept('application/json')
                ->timeout(90) // Increase timeout to 90 seconds
                ->get($itemsUrl);

            if (!$itemsResponse->successful()) {
                throw new \Exception('Failed to fetch invoice items. Status: ' . $itemsResponse->status());
            }

            $itemsData = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $itemsResponse->body()), true);

            $items = collect($itemsData['$resources'] ?? [])->map(function ($item) {
                return [
                    'uuid' => $item['$uuid'] ?? null,
                    'description' => $item['text'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'netAmount' => $item['netAmount'] ?? 0,
                    'taxAmount' => $item['taxAmount'] ?? 0,
                    'grossAmount' => $item['grossAmount'] ?? 0,
                    'product' => $item['product']['reference'] ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Invoice items fetched successfully.',
                'reference' => $reference,
                'uuid' => $uuid,
                'items' => $items,
                'invoice' => $invoiceData['$resources'][0] // Include the invoice details
            ]);
        } catch (\Exception $e) {
            \Log::error('Fetch Invoice Items by Reference Error:', [
                'reference' => $reference,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    protected function parseSageDate($dateString)
    {
        if (preg_match('/\/Date\((\d+)([+-]\d+)?\)\//', $dateString, $matches)) {
            $timestamp = $matches[1] / 1000;
            return date('Y-m-d H:i:s', $timestamp);
        }
        return $dateString;
    }
}
