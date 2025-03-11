<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        //return "test";
        $recentInvoice = BookingInvoice::with('booking')->where('BookingRequestID', 52634)->get();
        // dd($recentInvoice);
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('customer.dashboard', compact('recentInvoice', 'readyHoldInvoiceCount', 'completedInvoice'));
    }

    public function getInvoiceData($id)
    {
        $id = Crypt::decrypt($id);

        // Retrieve the invoice with related booking and invoice items
        $invoice = BookingInvoice::with('booking', 'invoice_items')->where('InvoiceID', $id)->first();

        if (!$invoice) {
            return abort(404, 'Invoice not found');
        }

        // Determine whether to use InvoiceID or BookingRequestID
        $filterColumn = !is_null($invoice->BookingID) ? 'BookingID' : 'BookingRequestID';

        // Fetch bookings based on the determined filter
        $bookings = Booking::where($filterColumn, $invoice->$filterColumn)->get();
        $booking = $bookings->first();
        //return $invoice;
        return view('admin.pages.invoice.invoice_details', compact('invoice', 'bookings','booking'));
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

        $items = BookingInvoice::with('booking', 'invoice_items')->where('BookingRequestID', $id)->get();
        // dd($items);
        return response()->json(['invoice_items' => $items]);
    }

    public function splitInvoice(Request $request)
    {

        foreach ($request->loads as $load) {
            // dd($load);
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
