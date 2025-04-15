<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Log;
use Symfony\Component\Console\Helper\ProgressBar;

class FetchInvoicesCommand extends Command
{
    protected $signature = 'invoices:fetch';
    protected $description = 'Fetch invoices and their items from Sage and store them in the database';

    protected $baseUrl = 'http://193.117.210.98:5495/sdata/accounts50/GCRM/%7B6447298A-F14B-48EA-9EAC-A3955968B321%7D';
    protected $username = 'act';
    protected $password = 'act';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        set_time_limit(300); // Avoid timeout for large fetches

        try {
            $this->info('Fetching invoices from Sage...');

            // Fetch invoices with postal addresses (adjust $filter as required)
            $invoiceUrl = $this->baseUrl . "/salesInvoices?\$filter=date ge datetime'2024-04-01'&\$orderby=reference desc&count=20&include=postalAddresses&format=json";
            $invoiceResponse = Http::withBasicAuth($this->username, $this->password)
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

            // Get companies data once from Sage
            $companyUrl = $this->baseUrl . '/tradingAccounts?count=40&format=json';
            $companyResponse = Http::withBasicAuth($this->username, $this->password)
                ->accept('application/json')
                ->withHeaders(['User-Agent' => 'LaravelApp/1.0'])
                ->timeout(3000)
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

            // Initialize progress bar for invoices
            $this->info('Processing invoices...');
            $progressBar = $this->output->createProgressBar(count($invoices));
            $progressBar->start();

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
                    $lineUrl = $this->baseUrl . "/salesInvoices(" . urlencode($invoice['$uuid']) . ")/salesInvoiceLines?format=json";

                    // Fetch invoice lines
                    $linesResponse = Http::withBasicAuth($this->username, $this->password)
                        ->accept('application/json')
                        ->withHeaders(['User-Agent' => 'LaravelApp/1.0'])
                        ->timeout(3000)
                        ->get($lineUrl);

                    // Log the response for debugging
                    Log::info("Invoice Lines Response: " . $linesResponse->body());
                    $linesData = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $linesResponse->body()), true);

                    $invoiceItems = $linesData['$resources'] ?? [];

                    // Optionally: Store raw lines JSON for reference
                    Storage::disk('local')->put("invoices/items_{$invoice['reference']}.json", json_encode($invoiceItems, JSON_PRETTY_PRINT));

                    // --- Save each invoice line in invoice_items table ---
                    foreach ($invoiceItems as $item) {
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

                // Increment progress bar for each invoice processed
                $progressBar->advance();
            }

            // Finish progress bar
            $progressBar->finish();

            $this->info('Invoices and their items fetched and stored successfully.');

        } catch (\Exception $e) {
            Log::error('Sage fetch error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Something went wrong: ' . $e->getMessage());
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
