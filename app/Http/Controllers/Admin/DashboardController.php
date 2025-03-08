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
        $recentInvoice = BookingRequest::with('loads')->where('BookingRequestID', 52634)->get();
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('dashboard', compact('recentInvoice', 'readyHoldInvoiceCount', 'completedInvoice'));
    }

    public function getInvoiceData($id)
    {
        $id = Crypt::decrypt($id);

        $item = BookingInvoice::with('booking', 'invoice_items')->where('BookingRequestID', $id)->get();
        $booking = $item->first()->booking ?? null;
        return view('admin.pages.invoice.invoice_details', compact('item', 'booking'));
    }

    public function getInvoiceItems(Request $request)
    {
        $invoiceId = $request->invoice_id;
        // dd($invoiceId);
        $invoice = BookingInvoice::where('InvoiceId', $invoiceId)->first();
        $invoiceItems = Booking::with('loads')
            ->where('BookingRequestID', $invoice->BookingRequestID)
            ->get();

        return response()->json(['invoice_items' => $invoiceItems]);
    }

    public function splitInvoice(Request $request)
    {
        foreach ($request->loads as $load) {
            $loads = BookingLoad::where('LoadID', $load['LoadID'])->first();

            if (!$loads) {
                return response()->json(['error' => 'Load not found'], 404);
            }

            $bookingRequest = BookingRequest::where('BookingRequestID', $loads->BookingRequestID)->first();
            if (!$bookingRequest) {
                return response()->json(['error' => 'Booking request not found'], 404);
            }

            $lastInvoice = BookingInvoice::orderBy('CreateDateTime', 'DESC')->first();
            // dd($bookingRequest);
            // Generate a new Invoice Number (ensure it's numeric)
            $newInvoiceNumber = $lastInvoice ? ((int) $lastInvoice->InvoiceNumber + 1) : 1001;

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
            $invoice->CreatedUserID = auth()->id(); // Assign the logged-in user's ID
            $invoice->CreateDateTime = now();
            $invoice->UpdateDateTime = now();

            $invoice->save();

            return response()->json(['success' => 'Invoice created successfully', 'invoice' => $invoice]);
        }
    }

}
