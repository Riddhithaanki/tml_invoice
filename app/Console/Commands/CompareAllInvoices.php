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
                    
                    // Map all Sage invoices' field values into an array
                    // $sageValues = [];
                    // if (!empty($sageData->invoice) && is_array($sageData->invoice)) {
                    //     foreach ($sageData->invoice as $sageInvoice) {
                    //         $sageValues = $sageInvoice->$sageField ?? 0;
                    //     }
                    // }

                    // Sum all values (or you can use another aggregation if needed)
                   // $sageValue = array_sum($sageValues);
                    // Format values to 2 decimal places for comparison and display
                    $formattedOurValue = number_format((float)$ourValue, 2, '.', '');
                    $formattedSageValue = number_format((float)$sageValue, 2, '.', '');
                    $differences['basic_details'][$ourField] = [];
                    // if (abs((float)$formattedOurValue - (float)$formattedSageValue) >= 0.01) {
                    if ($formattedOurValue == $formattedSageValue) {
                        $differences['basic_details'][$ourField] = [
                            'our_system' => $formattedOurValue,
                            'sage' => $formattedSageValue // append all values as sum
                        ];
                      }else{
                        $differences['basic_details'][$ourField] = [
                            'our_system' => $formattedOurValue,
                            'sage' => 0 // append all values as sum
                        ];
                      }
                    }

                   
                   //}
                        $ourItems = $ourInvoice->items->pluck('Qty', 'Description')->toArray();
                        // $sageItemsMap = collect($sageData->items)->pluck('quantity', 'description')->toArray();
                                $sageItemsMap = collect($sageData->items)
                            ->mapWithKeys(function ($item) {
                                return [
                                    $item->description => number_format((float) $item->quantity, 2)
                                ];
                            })
                            ->toArray();
               
       
                 $allDescriptions = array_unique(array_merge(array_keys($ourItems), array_keys($sageItemsMap)));
                 
                  foreach ($allDescriptions as $description) {
                     $ourQtyn = $ourItems[$description] ?? 0;
                    $sageQtyn = $sageItemsMap[$description] ?? 0;
                    $ourQty = number_format((int)$ourQtyn, 2, '.', '');
                    $sageQty = number_format((int)$sageQtyn, 2, '.', '');
                    $differences['items'] = [];
                    
                    // if (abs($ourQty - $sageQty) > 0.01) {
                    // if (abs((float)$ourQty - (float)$sageQty) >= 0.01){
                       
                        $differences['items'][$description] = [
                            'our_system' => $ourQty,
                            'sage' => $sageQty
                        ];
                    }
                 //}
            
              
                 if (!empty($differences['basic_details']) || !empty($differences['items'])) {
                    // InvoiceDifference::updateOrCreate(
                    //     ['invoice_number' => $formattedInvoiceNumber],
                    //     [
                    //         'basic_details_differences' => $differences['basic_details'] ?? null,
                    //         'items_differences' => $differences['items'] ?? null,
                    //         'status' => 'pending'
                    //     ]
                    // );
                   if ($invoiceNumber != $sageData->invoice->reference) {
                        InvoiceDifference::updateOrCreate(
                            ['invoice_number' => $formattedInvoiceNumber],
                            [
                                'basic_details_differences' => $differences['basic_details'] ?? null,
                                'items_differences' => $differences['items'] ?? null,
                                'status' => 'Not avaible in Sage',
                            ]
                        );
                    }

                }

            } catch (\Exception $e) {
                dd($e);
                Log::error("Error comparing invoice {$formattedInvoiceNumber}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nInvoice comparison completed!");
    }
}
