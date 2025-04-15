<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use App\Models\BookingRequest;
use App\Models\ReadyInvoice;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
                'comment' => 'nullable|string'
            ]);


            // Create a new invoice record
            $invoice = new ReadyInvoice();

            // Get the last invoice number and increment it
            $lastInvoice = ReadyInvoice::orderBy('InvoiceNumber', 'desc')->first();
            $nextInvoiceNumber = $lastInvoice ? intval($lastInvoice->InvoiceNumber) + 1 : 1;

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
                'InvoiceNumber' => $nextInvoiceNumber
            ]);

            $invoice->save();

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
}
