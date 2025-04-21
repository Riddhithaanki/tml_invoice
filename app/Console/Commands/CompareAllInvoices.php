<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReadyInvoice;
use App\Models\InvoiceDifference;
use App\Http\Controllers\Admin\SageController;
use Illuminate\Support\Facades\Log;

class CompareAllInvoices extends Command
{
    protected $signature = 'invoices:compare-all';
    protected $description = 'Compare all ReadyInvoices with Sage and store differences';

    public function handle()
    {
        $this->info('Starting comparison of all invoices with Sage...');

        $invoices = ReadyInvoice::with('items')->get();
        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        foreach ($invoices as $ourInvoice) {
            $formattedInvoiceNumber = $ourInvoice->InvoiceNumber;
            $invoiceNumber = ltrim($formattedInvoiceNumber, '0'); // Convert back for Sage

            try {
                $sageResponse = app(SageController::class)->getInvoiceItemsByReference($invoiceNumber);
                $sageData = $sageResponse->getData();

                if (!$sageData->success) {
                    Log::warning("Sage invoice not found: {$invoiceNumber}");
                    continue;
                }

                $differences = [];

                $basicFields = [
                    'SubTotalAmount' => 'netTotal',
                    'VatAmount' => 'taxTotal',
                    'FinalAmount' => 'grossTotal'
                ];

                foreach ($basicFields as $ourField => $sageField) {
                    $ourValue = $ourInvoice->$ourField;
                    $sageValue = $sageData->invoice->$sageField ?? 0;

                    if (abs($ourValue - $sageValue) > 0.01) {
                        $differences['basic_details'][$ourField] = [
                            'our_system' => $ourValue,
                            'sage' => $sageValue
                        ];
                    }
                }

                $ourItems = $ourInvoice->items->pluck('Qty', 'Description')->toArray();
                $sageItemsMap = collect($sageData->items)->pluck('quantity', 'description')->toArray();

                $allDescriptions = array_unique(array_merge(array_keys($ourItems), array_keys($sageItemsMap)));

                foreach ($allDescriptions as $description) {
                    $ourQty = $ourItems[$description] ?? 0;
                    $sageQty = $sageItemsMap[$description] ?? 0;

                    if (abs($ourQty - $sageQty) > 0.01) {
                        $differences['items'][$description] = [
                            'our_system' => $ourQty,
                            'sage' => $sageQty
                        ];
                    }
                }

                if (!empty($differences['basic_details']) || !empty($differences['items'])) {
                    InvoiceDifference::updateOrCreate(
                        ['invoice_number' => $formattedInvoiceNumber],
                        [
                            'basic_details_differences' => $differences['basic_details'] ?? null,
                            'items_differences' => $differences['items'] ?? null,
                            'status' => 'pending'
                        ]
                    );
                }

            } catch (\Exception $e) {
                dd($e->getMessage());
                Log::error("Error comparing invoice {$formattedInvoiceNumber}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nInvoice comparison completed!");
    }
}
