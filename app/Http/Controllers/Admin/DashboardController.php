<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;

use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function index()
    {
        $recentInvoice = BookingInvoice::with('booking')->where('BookingRequestID', 52634)->get();
        // dd($recentInvoice);
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('dashboard', compact('recentInvoice', 'readyHoldInvoiceCount', 'completedInvoice'));
    }

    public function getInvoiceData($id)
    {
        $id = Crypt::decrypt($id);

        // Fetch Invoice (Original or Split)
        $invoice = BookingRequest::with('booking', 'invoice_items', 'invoice')
            ->where('BookingRequestID', $id)
            ->first();

        if (!$invoice) {
            return abort(404, 'Invoice not found');
        }

        // Check if this is a split invoice or an original invoice
        $isSplitInvoice = !is_null($invoice->parent_invoice_id);

        // Fetch bookings related to this invoice only
        $bookings = Booking::where('BookingRequestID', $invoice->BookingRequestID);

        if ($isSplitInvoice) {
            // If it's a split invoice, fetch only this split invoice
            $bookings->where('parent_invoice_id', $invoice->InvoiceID);
        } else {
            // If it's an original invoice, fetch only unsplit invoices
            $bookings->whereNull('parent_invoice_id');
        }

        $bookings = $bookings->get();
        $booking = $bookings->first();
        
        return view('admin.pages.invoice.invoice_details', compact('invoice', 'bookings', 'booking', 'isSplitInvoice'));
    }

    public function getInvoiceItems(Request $request)
    {
        // dd($request->all());
        $bookingId = $request->booking_id;
        $booking = Booking::where('BookingId', $bookingId)->first();
        $invoiceItems = Booking::with('loads')
            ->where('BookingID', $booking->BookingID)
            ->get();

        return response()->json(['invoice_items' => $invoiceItems]);
    }

    public function getSplitInvoiceItems(Request $request)
    {
        $id = $request->invoice_id;
        // dd($id);
        $items = BookingRequest::with('booking', 'invoice_items')
            ->where('BookingRequestID', $id)
            ->get();

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
            $CreateDate = \Carbon\Carbon::parse($item->CreateDateTime)->toDateString(); // Extract only date (YYYY-MM-DD)
        
            // Fetch records where CompanyName matches and CreateDate (date part) matches
            $items = BookingRequest::with('booking', 'invoice_items')
                ->where('CompanyName', $CompanyName)
                ->whereDate('CreateDateTime', $CreateDate) // Compare only the date part
                ->get();

        return response()->json(['invoice_items' => $items]);
    }

    public function splitInvoice(Request $request)
    {
        // dd($request->all());
        $originalInvoice = BookingInvoice::where('BookingRequestID', $request->booking_request_id)
            ->where('is_split', 0)
            ->first();

        if (!$originalInvoice) {
            return response()->json(['error' => 'Original invoice not found'], 404);
        }

        foreach ($request->loads as $load) {
            $loads = Booking::where('BookingID', $load['LoadID'])->first();
            // dd($loads);
            if (!$loads) {
                return response()->json(['error' => 'Load not found'], 404);
            }

            $bookingRequest = BookingRequest::where('BookingRequestID', $loads->BookingRequestID)->first();
            if (!$bookingRequest) {
                return response()->json(['error' => 'Booking request not found'], 404);
            }

            $lastInvoice = BookingInvoice::orderBy('CreateDateTime', 'DESC')->first();
            $newInvoiceNumber = $lastInvoice ? str_pad((int) $lastInvoice->InvoiceNumber + 1, 7, '0', STR_PAD_LEFT) : '0000001';

            // Create Split Invoice
            $invoice = new BookingInvoice();
            $invoice->BookingRequestID = $loads->BookingRequestID;
            $invoice->InvoiceDate = today();
            $invoice->InvoiceType = 0;
            $invoice->InvoiceNumber = $newInvoiceNumber;
            $invoice->CompanyID = $bookingRequest->CompanyID;
            $invoice->CompanyName = $bookingRequest->CompanyName;
            $invoice->OpportunityID = $bookingRequest->OpportunityID;
            $invoice->OpportunityName = $bookingRequest->OpportunityName;
            $invoice->ContactID = $bookingRequest->ContactID;
            $invoice->ContactName = $bookingRequest->ContactName;
            $invoice->ContactMobile = $bookingRequest->ContactMobile;
            $invoice->SubTotalAmount = $bookingRequest->TotalAmount;
            $invoice->VatAmount = $bookingRequest->TotalAmount * 0.2; // Assuming VAT is 20%
            $invoice->FinalAmount = $bookingRequest->TotalAmount + $invoice->VatAmount;
            $invoice->TaxRate = 20.00;
            $invoice->Status = 0; // Assuming "0" means ready
            $invoice->CreatedUserID = auth()->id();
            $invoice->CreateDateTime = now();
            $invoice->UpdateDateTime = now();

            // Set the parent_invoice_id to link to the original invoice
            $invoice->parent_invoice_id = $originalInvoice->InvoiceID;

            $invoice->save();
            // dd($originalInvoice);
            // Update the load to reference the split invoice
            $loads->parent_invoice_id = $originalInvoice->InvoiceID;
            $loads->save();
        }

        return response()->json(['success' => 'Split invoice created successfully', 'invoice' => $invoice]);
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

            $lastInvoice = BookingInvoice::orderBy('CreateDateTime', 'DESC')->first();
            $newInvoiceNumber = $lastInvoice ? str_pad((int) $lastInvoice->InvoiceNumber + 1, 7, '0', STR_PAD_LEFT) : '0000001';

            // Create Split Invoice
            $invoice = new BookingInvoice();
            $invoice->BookingRequestID = $loads->BookingRequestID;
            $invoice->InvoiceDate = today();
            $invoice->InvoiceType = 0;
            $invoice->InvoiceNumber = $newInvoiceNumber;
            $invoice->CompanyID = $bookingRequest->CompanyID;
            $invoice->CompanyName = $bookingRequest->CompanyName;
            $invoice->OpportunityID = $bookingRequest->OpportunityID;
            $invoice->OpportunityName = $bookingRequest->OpportunityName;
            $invoice->ContactID = $bookingRequest->ContactID;
            $invoice->ContactName = $bookingRequest->ContactName;
            $invoice->ContactMobile = $bookingRequest->ContactMobile;
            $invoice->SubTotalAmount = $bookingRequest->TotalAmount;
            $invoice->VatAmount = $bookingRequest->TotalAmount * 0.2; // Assuming VAT is 20%
            $invoice->FinalAmount = $bookingRequest->TotalAmount + $invoice->VatAmount;
            $invoice->TaxRate = 20.00;
            $invoice->Status = 0; // Assuming "0" means ready
            $invoice->CreatedUserID = auth()->id();
            $invoice->CreateDateTime = now();
            $invoice->UpdateDateTime = now();

            // Set the parent_invoice_id to link to the original invoice
            $invoice->parent_invoice_id = $originalInvoice->InvoiceID;

            $invoice->save();
            // dd($originalInvoice);
            // Update the load to reference the split invoice
            $loads->parent_invoice_id = $originalInvoice->InvoiceID;
            $loads->save();
        }

        return response()->json(['success' => 'Split invoice created successfully', 'invoice' => $invoice]);
    }

}
