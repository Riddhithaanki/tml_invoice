<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\ReadyInvoice;
use App\Models\InvoiceDifference;
USE App\Models\BookingInvoice;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SplitInvoice extends Controller
{
   public function index(Request $request)
{
    $invoices = DB::table('tbl_booking_invoice as inv')
        ->join('tbl_booking1 as b', 'inv.BookingID', '=', 'b.BookingID')
        ->select(
            'inv.BookingRequestID',
            'inv.BookingID',
            'inv.CompanyName',
            'inv.OpportunityName',
            'b.MaterialName',         // from tbl_booking1
            'inv.InvoiceDate'         // Date & Time
        )
        ->get();
            // dd($invoices);

    return view('admin.pages.split_invoice', compact('invoices'));
}

// public function viewSplitInvoice($id)
// {
//     $invoice = BookingInvoice::where('BookingRequestID', $id)->first();
//     // dd($invoice); exit;
//     if (!$invoice) {
//         return redirect()->back()->with('error', 'Invoice not found.');
//     }

//     return view('admin.pages.view_split', compact('invoice'));      
// }

public function viewSplitInvoice($id)
    {
        // Fetch the booking info
        $booking = DB::table('tbl_booking1 as b')
            ->join('tbl_booking_invoice as i', 'b.BookingID', '=', 'i.BookingID')
            ->select('b.*', 'i.CompanyName', 'i.OpportunityName', 'i.InvoiceDate', 'i.InvoiceNumber', 'i.TaxRate', 'i.InvoiceID')
            ->where('b.BookingID', $id)
            ->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }
// dd($booking);exit;
        // Fetch the parent invoice linked to this booking
        $invoice = DB::table('tbl_booking_invoice')
            ->where('BookingID', $booking->BookingID)
            ->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found for this booking.');
        }
// dd($invoice);exit;
        // Fetch all split invoices linked to this parent invoice
        $splitInvoices = DB::table('tbl_booking_invoice')
            ->where('parent_invoice_id', $invoice->InvoiceID)
            ->get();

        // Prepare booking loads for each split invoice
        $splitBookings = [];
        foreach ($splitInvoices as $split) {
            $loads = DB::table('tbl_booking1')
                ->where('BookingID', $split->BookingID)
                ->get();

            $splitBookings[$split->InvoiceID] = $loads;
        }

        return view('admin.pages.view_split', [
            'booking' => $booking,
            'invoice' => $invoice,
            'splitInvoices' => $splitInvoices,
            'splitBookings' => $splitBookings,
        ]);
    }

}

