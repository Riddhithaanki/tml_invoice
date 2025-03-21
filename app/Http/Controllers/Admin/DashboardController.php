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

        // Find the booking entry using BookingID
        $bookingData = Booking::with('loads', 'bookingRequest')->where('BookingID', $id)->first();
        // dd($booking);
        if (!$bookingData) {
            return abort(404, 'Booking not found');
        }

        // Fetch the BookingRequestID from the booking
        $bookingRequestId = $bookingData->BookingRequestID;

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
            // If it's a split invoice, fetch only this split invoice
            $bookings->where('parent_invoice_id', $invoice->InvoiceID);
        } else {
            // If it's an original invoice, fetch only unsplit invoices
            $bookings->whereNull('parent_invoice_id');
        }

        $bookings = $bookings->get();
        // $booking = $bookings->first();
        // dd($invoice);
        return view('admin.pages.invoice.invoice_details', compact('invoice', 'bookings', 'bookingData', 'isSplitInvoice'));
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

        // Fetch booking data with parent invoice ID (only if it's a split invoice)
        $bookingData = Booking::with('loads', 'bookingRequest')
            ->where('BookingID', $id)
            ->first();

        // Check if booking data exists
        if (!$bookingData) {
            return response()->json(['error' => 'No split invoice found for this booking'], 404);
        }

        $bookingRequestId = $bookingData->BookingRequestID;

        // Fetch invoice items linked to the same BookingRequestID
        $items = Booking::with('bookingRequest')
            ->where('BookingRequestID', $bookingRequestId)
            ->whereNull('parent_invoice_id')
            ->get();

        // Check if items exist
        if ($items->isEmpty()) {
            return response()->json(['error' => 'No invoice items found'], 404);
        }

        return response()->json(['invoice_items' => $items]);
    }



    public function splitInvoice(Request $request)
    {
        // dd($request->all());

        // Find the original invoice
        $originalInvoice = BookingInvoice::where('BookingRequestID', $request->booking_request_id)
            ->where('is_split', 0)
            ->first();

        // If no original invoice exists, create a new one
        if (!$originalInvoice) {
            $bookingRequest = BookingRequest::where('BookingRequestID', $request->booking_request_id)->first();

            if (!$bookingRequest) {
                return response()->json(['error' => 'Booking request not found'], 404);
            }

            $lastInvoice = BookingInvoice::orderBy('CreateDateTime', 'DESC')->first();
            $newInvoiceNumber = $lastInvoice ? str_pad((int) $lastInvoice->InvoiceNumber + 1, 7, '0', STR_PAD_LEFT) : '0000001';

            // Create a new original invoice
            $originalInvoice = new BookingInvoice();
            $originalInvoice->BookingRequestID = $bookingRequest->BookingRequestID;
            $originalInvoice->InvoiceDate = today();
            $originalInvoice->InvoiceType = 0;
            $originalInvoice->InvoiceNumber = $newInvoiceNumber;
            $originalInvoice->CompanyID = $bookingRequest->CompanyID;
            $originalInvoice->CompanyName = $bookingRequest->CompanyName;
            $originalInvoice->OpportunityID = $bookingRequest->OpportunityID;
            $originalInvoice->OpportunityName = $bookingRequest->OpportunityName;
            $originalInvoice->ContactID = $bookingRequest->ContactID;
            $originalInvoice->ContactName = $bookingRequest->ContactName;
            $originalInvoice->ContactMobile = $bookingRequest->ContactMobile;
            $originalInvoice->SubTotalAmount = $bookingRequest->TotalAmount;
            $originalInvoice->VatAmount = $bookingRequest->TotalAmount * 0.2; // Assuming VAT is 20%
            $originalInvoice->FinalAmount = $bookingRequest->TotalAmount + $originalInvoice->VatAmount;
            $originalInvoice->TaxRate = 20.00;
            $originalInvoice->Status = 0; // Assuming "0" means ready
            $originalInvoice->CreatedUserID = auth()->id();
            $originalInvoice->CreateDateTime = now();
            $originalInvoice->UpdateDateTime = now();
            $originalInvoice->is_split = 1; // Mark it as original

            $originalInvoice->save();
        }

        // Now proceed to split loads into new invoices
        foreach ($request->loads as $load) {
            $loads = Booking::where('BookingID', $load['LoadID'])->first();

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
            $invoice->BookingId = $loads->BookingID;
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
            $invoice->is_split = 1; // Mark it as split
            $invoice->save();

            // Update the load to reference the split invoice
            $loads->parent_invoice_id = $originalInvoice->InvoiceID;
            $loads->save();
        }

        return response()->json(['success' => 'Split invoice created successfully', 'invoice' => $invoice]);
    }




}
