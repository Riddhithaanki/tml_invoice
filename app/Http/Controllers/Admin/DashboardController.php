<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use App\Models\BookingRequest;
use App\Models\ReadyInvoice;
use App\Models\ReadyInvoiceItem;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceDifference;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $recentInvoice = BookingInvoice::with('booking')->orderBy('CreateDateTime', "DESC")->limit(10)->get();
        // dd($recentInvoice);
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('dashboard', compact('recentInvoice', 'readyHoldInvoiceCount', 'completedInvoice'));
    }

    public function getInvoiceData($id)
    {
        $id = Crypt::decrypt($id);

        // Find the booking entry using BookingID
        $bookingData = Booking::with('loads', 'bookingRequest')->where('BookingID', $id)->first();

        if (!$bookingData) {
            return abort(404, 'Booking not found');
        }

        // Fetch the BookingRequestID from the booking
        $bookingRequestId = $bookingData->BookingRequestID;

        // ✅ Check if this BookingRequestID exists in the ready_invoice table
        $readyInvoice = ReadyInvoice::where('BookingRequestID', $bookingRequestId)->first();
        if ($readyInvoice) {
            $invoice = $readyInvoice;
            return view('admin.pages.invoice.viewinvoice', compact('invoice'));

        }

        // Fetch Invoice (Original or Split)
        $invoice = BookingRequest::with('booking', 'invoice_items', 'invoice')
            ->where('BookingRequestID', $bookingRequestId)
            ->first();

        if (!$invoice) {
            return abort(404, 'Invoice not found');
        }

        // Check if this is a split invoice or an original invoice
        $isSplitInvoice = !is_null($invoice->parent_invoice_id);

        // Fetch bookings related to this invoice only
        $bookings = Booking::where('BookingRequestID', $invoice->BookingRequestID);

        if ($isSplitInvoice) {
            $bookings->where('parent_invoice_id', $invoice->InvoiceID);
        } else {
            $bookings->whereNull('parent_invoice_id');
        }

        $bookings = $bookings->get();

        return view('admin.pages.invoice.invoice_details', compact('invoice', 'bookings', 'bookingData', 'isSplitInvoice'));
    }

    public function getInvoiceItems(Request $request)
    {
        // Validate request input
        $bookingId = $request->booking_id;
        if (!$bookingId) {
            return response()->json(['error' => 'Booking ID is required'], 400);
        }

        // Fetch the booking
        $booking = Booking::with('loads')->where('BookingId', $bookingId)->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        return response()->json(['invoice_items' => [$booking]]);
    }

    public function getSplitInvoiceItems(Request $request)
    {
        // Validate input
        $id = $request->invoice_id;
        if (!$id) {
            return response()->json(['error' => 'Invoice ID is required'], 400);
        }

        // Fetch booking data
        $bookingData = Booking::with('loads', 'bookingRequest')
            ->where('BookingID', $id)
            ->first();

        if (!$bookingData) {
            return response()->json(['error' => 'No split invoice found for this booking'], 404);
        }

        // Fetch only split invoice items
        $items = Booking::with('bookingRequest')
            ->where('BookingRequestID', $bookingData->BookingRequestID)
            ->whereNull('parent_invoice_id') // Fetch only original invoices, NOT split ones
            ->get();


        if ($items->isEmpty()) {
            return response()->json(['error' => 'No split invoice items found'], 404);
        }

        return response()->json(['invoice_items' => $items]);
    }


    public function getMergeBookingItems(Request $request)
    {
        $id = $request->invoice_id;
        // dd($id);
        $item = BookingRequest::with('booking', 'invoice_items')
            ->where('BookingRequestID', $id)
            ->first();

        if (!$item) {
            return response()->json(['error' => 'Booking request not found'], 404);
        }

        $CompanyName = $item->CompanyName;
        $OpportunityName = $item->OpportunityName;
        $CreateDate = \Carbon\Carbon::parse($item->CreateDateTime)->toDateString(); // Extract only date (YYYY-MM-DD)

        // Fetch records where CompanyName matches and CreateDate (date part) matches
        $items = BookingRequest::with('booking', 'invoice_items')
            ->where('CompanyName', $CompanyName)
            ->where('OpportunityName', $OpportunityName)
            // ->whereDate('CreateDateTime', $CreateDate) // Compare only the date part
            ->get();

        return response()->json(['invoice_items' => $items]);
    }


    public function splitInvoice(Request $request)
    {
        foreach ($request->loads as $load) {
            $loads = Booking::where('BookingID', $load['LoadID'])->first();
            if (!$loads) {
                return response()->json(['error' => 'Load not found'], 404);
            }

            // Create Split Invoice
            $splitInvoice = new BookingInvoice();
            $splitInvoice->BookingRequestID = $loads->BookingRequestID;
            $splitInvoice->InvoiceDate = today();
            $splitInvoice->InvoiceType = 0;
            $splitInvoice->InvoiceNumber = BookingInvoice::max('InvoiceNumber') + 1;
            $splitInvoice->CompanyID = $loads->bookingRequest->CompanyID;
            $splitInvoice->CompanyName = $loads->bookingRequest->CompanyName;
            $splitInvoice->OpportunityID = $loads->bookingRequest->OpportunityID;
            $splitInvoice->OpportunityName = $loads->bookingRequest->OpportunityName;
            $splitInvoice->ContactID = $loads->bookingRequest->ContactID;
            $splitInvoice->ContactName = $loads->bookingRequest->ContactName;
            $splitInvoice->ContactMobile = $loads->bookingRequest->ContactMobile;
            $splitInvoice->SubTotalAmount = $loads->TotalAmount;
            $splitInvoice->VatAmount = $loads->TotalAmount * 0.2;
            $splitInvoice->FinalAmount = $loads->TotalAmount + $splitInvoice->VatAmount;
            $splitInvoice->TaxRate = 20.00;
            $splitInvoice->Status = 0;
            $splitInvoice->CreatedUserID = auth()->id();
            $splitInvoice->CreateDateTime = now();
            $splitInvoice->UpdateDateTime = now();
            $splitInvoice->is_split = 1;
            $splitInvoice->save();
            // dd($splitInvoice);
            // Insert record in `split_invoices` table
            DB::table('split_invoices')->insert([
                'booking_id' => $loads->BookingID, // Store BookingID
                'split_invoice_id' => $splitInvoice->InvoiceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $loads->parent_invoice_id = $splitInvoice->InvoiceId;
            $loads->save();
        }

        return response()->json(['success' => 'Split invoice created successfully', 'invoice' => $splitInvoice]);
    }


    public function mergeBooking(Request $request)
    {
        $originalBooking = BookingRequest::with('booking', 'invoice_items')
            ->where('BookingRequestID', $request->booking_request_id)
            ->first();

        if (!$originalBooking) {
            return response()->json(['error' => 'Original Booking not found'], 404);
        }

        foreach ($request->bookings as $booking) {
            // $loads = Booking::where('BookingID', $load['LoadID'])->first();
            // // dd($loads);
            // if (!$loads) {
            //     return response()->json(['error' => 'Load not found'], 404);
            // }

            $bookingRequest = BookingRequest::where('BookingRequestID', $booking['BookingID'])->first();
            if (!$bookingRequest) {
                return response()->json(['error' => 'Booking request not found'], 404);
            }

            Booking::where('BookingRequestID', $booking['BookingID'])
                ->update([
                    'BookingRequestID' => $request->booking_request_id,
                    'OriginalBookingRequestID' => $booking['BookingID']
                ]);

            BookingRequest::where('BookingRequestID', $booking['BookingID'])
                ->update(['is_delete' => 1]);

        }

        return response()->json(['success' => 'Booking Merge successfully', 'invoice' => $bookingRequest]);
    }


    public function confirm(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'BookingRequestID' => 'required|integer',
                'CompanyID' => 'required|string',
                'CompanyName' => 'required|string',
                'OpportunityID' => 'required|string',
                'OpportunityName' => 'required|string',
                'ContactID' => 'required|string',
                'ContactName' => 'required|string',
                'ContactMobile' => 'required|string',
                'SubTotalAmount' => 'required|numeric',
                'VatAmount' => 'required|numeric',
                'FinalAmount' => 'required|numeric',
                'hold_invoice' => 'nullable|boolean',
                'comment' => 'nullable|string',
                'booking_loads' => 'required|string',
                'invoice_items' => 'required|string'
            ]);

            // Decode the JSON strings
            $bookingLoads = json_decode($request->booking_loads, true);
            $invoiceItems = json_decode($request->invoice_items, true);

            // Create a new invoice record
            $invoice = new ReadyInvoice();

            // Get the last invoice number and increment it
            $lastInvoice = ReadyInvoice::orderBy('InvoiceNumber', 'desc')->first();
            $nextInvoiceNumber = $lastInvoice ? intval($lastInvoice->InvoiceNumber) + 1 : 1;

            // Format invoice number with leading zeros (6 digits)
            $formattedInvoiceNumber = str_pad($nextInvoiceNumber, 6, '0', STR_PAD_LEFT);

            $invoice->fill([
                'BookingRequestID' => $validated['BookingRequestID'],
                'CompanyID' => $validated['CompanyID'],
                'InvoiceType' => 0,
                'CompanyName' => $validated['CompanyName'],
                'OpportunityID' => $validated['OpportunityID'],
                'OpportunityName' => $validated['OpportunityName'],
                'ContactID' => $validated['ContactID'],
                'ContactName' => $validated['ContactName'],
                'ContactMobile' => $validated['ContactMobile'],
                'SubTotalAmount' => $validated['SubTotalAmount'],
                'VatAmount' => $validated['VatAmount'],
                'FinalAmount' => $validated['FinalAmount'],
                'TaxRate' => $validated['TaxRate'] ?? "0",
                'CreatedUserID' => Auth::user()->userId,
                'Status' => $validated['hold_invoice'] ? 0 : 1, // 0 for hold, 1 for ready
                'Comment' => $validated['comment'],
                'InvoiceDate' => now(),
                'InvoiceNumber' => $formattedInvoiceNumber
            ]);

            $invoice->save();

            // Store invoice items
            if (!empty($bookingLoads)) {
                $itemNumber = 1;
                foreach ($bookingLoads as $load) {
                    // Get MaterialCode from tbl_materials
                    $material = DB::table('tbl_materials')
                        ->where('MaterialName', $load['MaterialName'])
                        ->first();
                    $invoiceItem = new ReadyInvoiceItem();
                    $invoiceItem->fill([
                        'InvoiceID' => $invoice->id,
                        'InvoiceNumber' => $formattedInvoiceNumber,
                        'ItemNumber' => $itemNumber++,
                        'Qty' => $load['Loads'],
                        'UnitPrice' => $load['Price'],
                        'GrossAmount' => $load['TotalAmount'],
                        'TaxAmount' => $load['TotalAmount'] * ($validated['TaxRate'] ?? 0) / 100,
                        'TaxRate' => $validated['TaxRate'] ?? 0,
                        'NetAmount' => $load['TotalAmount'],
                        'NominalCode' => $material ? $material->MaterialCode : null,
                        'Description' => $load['MaterialName'],
                        'Comment1' => $load['LoadType'],
                        'CreateDateTime' => now(),
                        'UpdateDateTime' => now()
                    ]);
                    $invoiceItem->save();
                }
            }

            // Update the booking request status
            $bookingRequest = BookingRequest::where('BookingRequestID', $validated['BookingRequestID'])->first();
            if ($bookingRequest) {
                $bookingRequest->Status = $validated['hold_invoice'] ? 0 : 1;
                $bookingRequest->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice has been confirmed successfully',
                'redirect_url' => route('invoice.showData', ['invoice' => $invoice->id])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $invoice = ReadyInvoice::where('InvoiceID', '=', $id)->first();
        return view('admin.pages.invoice.viewinvoice', compact('invoice'));
    }

    public function compareWithSage(Request $request)
    {
        try {
            // Get the invoice number from request
            $invoiceNumber = $request->invoiceNumber;

            if (!$invoiceNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice number is required'
                ], 400);
            }

            // Convert Sage format (1) to our format (000001)
            $formattedInvoiceNumber = str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT);

            // Get invoice from our system
            $ourInvoice = ReadyInvoice::where('InvoiceNumber', $formattedInvoiceNumber)
                ->with('items')
                ->first();

            if (!$ourInvoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found in our system',
                    'invoice_number' => $formattedInvoiceNumber
                ], 404);
            }

            // Get invoice from Sage using the getInvoiceItemsByReference method
            $sageResponse = app(SageController::class)->getInvoiceItemsByReference($invoiceNumber);
            $sageData = $sageResponse->getData();

            if (!$sageData->success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found in Sage: ' . ($sageData->error ?? 'Unknown error'),
                    'invoice_number' => $invoiceNumber
                ], 404);
            }

            // Compare the data
            $differences = [];

            // Compare basic invoice details
            $basicFields = [
                'SubTotalAmount' => 'netTotal',
                'VatAmount' => 'taxTotal',
                'FinalAmount' => 'grossTotal'
            ];

            foreach ($basicFields as $ourField => $sageField) {
                $ourValue = $ourInvoice->$ourField;
                $sageValue = $sageData->invoice->$sageField ?? 0;

                if (abs($ourValue - $sageValue) > 0.01) { // Using small tolerance for floating point comparison
                    $differences['basic_details'][$ourField] = [
                        'our_system' => $ourValue,
                        'sage' => $sageValue
                    ];
                }
            }

            // Compare invoice items
            $ourItems = $ourInvoice->items->pluck('Qty', 'Description')->toArray();
            $sageItemsMap = collect($sageData->items)->pluck('quantity', 'description')->toArray();

            // Find items that exist in one system but not the other
            $allDescriptions = array_unique(array_merge(array_keys($ourItems), array_keys($sageItemsMap)));

            foreach ($allDescriptions as $description) {
                $ourQty = $ourItems[$description] ?? 0;
                $sageQty = $sageItemsMap[$description] ?? 0;

                if (abs($ourQty - $sageQty) > 0.01) { // Using small tolerance for floating point comparison
                    $differences['items'][$description] = [
                        'our_system' => $ourQty,
                        'sage' => $sageQty
                    ];
                }
            }

            // Store differences in database if any found
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

            return response()->json([
                'success' => true,
                'message' => 'Comparison completed',
                'invoice_number' => $formattedInvoiceNumber,
                'differences' => $differences
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error comparing invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showDifferences()
    {
        // Get invoices with differences
        $differences = InvoiceDifference::orderBy('created_at', 'desc')->get();

        // Get perfect invoices (those not in the differences table)
        $perfectInvoices = ReadyInvoice::whereNotIn('InvoiceNumber', function($query) {
            $query->select('invoice_number')->from('invoice_differences');
        })->orderBy('InvoiceDate', 'desc')->get();

        return view('admin.pages.invoice.differences', compact('differences', 'perfectInvoices'));
    }

    public function updateDifferenceStatus(Request $request, $id)
    {

        $request->validate([
            'status' => 'required|in:0,1',
            'resolution_notes' => 'nullable|string'
        ]);
        $invoice = Invoice::where('id', $id)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }
        $difference = InvoiceDifference::findOrFail($id);
        $difference->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes
        ]);

        $invoice->update([
            'isApproved' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Difference status updated successfully'
        ]);
    }

    public function getInvoiceByNumber(Request $request)
    {
        try {
            $invoiceNumber = $request->input('invoice_number');

            if (!$invoiceNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice number is required'
                ], 400);
            }

            // Format the invoice number to match our system's format (000001)
            $formattedInvoiceNumber = str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT);

            // Get the invoice with its items
            $invoice = ReadyInvoice::where('InvoiceNumber', $formattedInvoiceNumber)
                ->with('items')
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found',
                    'invoice_number' => $formattedInvoiceNumber
                ], 404);
            }

            // Format the response
            $response = [
                'success' => true,
                'invoice' => [
                    'InvoiceID' => $invoice->id,
                    'InvoiceNumber' => $invoice->InvoiceNumber,
                    'InvoiceDate' => $invoice->InvoiceDate,
                    'CompanyName' => $invoice->CompanyName,
                    'SubTotalAmount' => $invoice->SubTotalAmount,
                    'VatAmount' => $invoice->VatAmount,
                    'FinalAmount' => $invoice->FinalAmount,
                    'TaxRate' => $invoice->TaxRate,
                    'Status' => $invoice->Status,
                    'Comment' => $invoice->Comment,
                    'items' => $invoice->items->map(function ($item) {
                        return [
                            'ItemNumber' => $item->ItemNumber,
                            'Description' => $item->Description,
                            'Qty' => $item->Qty,
                            'UnitPrice' => $item->UnitPrice,
                            'GrossAmount' => $item->GrossAmount,
                            'TaxAmount' => $item->TaxAmount,
                            'NetAmount' => $item->NetAmount,
                            'NominalCode' => $item->NominalCode,
                            'Comment1' => $item->Comment1
                        ];
                    })
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
