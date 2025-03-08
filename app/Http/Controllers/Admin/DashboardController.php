<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function index()
    {
        $recentInvoice = BookingInvoice::with('booking')->orderBy('InvoiceID', 'desc')->limit(2)->get();
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('dashboard', compact('recentInvoice', 'readyHoldInvoiceCount', 'completedInvoice'));
    }

    public function getInvoiceData($id)
    {
        $id = Crypt::decrypt($id);

        $item = BookingInvoice::with('booking','invoice_items')->where('InvoiceID', $id)->first();
        // dd($item);
        return view('admin.pages.invoice.invoice_details', compact('item'));
    }

    public function getInvoiceItems(Request $request)
    {
        $invoiceId = $request->invoice_id;
        // dd($invoiceId);
        $invoice = BookingInvoice::where('InvoiceId',$invoiceId)->first();
        $invoiceItems = Booking::with('loads')
               ->where('BookingRequestID',$invoice->BookingRequestID)
               ->get();

        return response()->json(['invoice_items' => $invoiceItems]);
    }

    public function splitInvoice(Request $request){
        foreach ($request->loads as $load) {
            $loads = BookingLoad::where('LoadID',$load['LoadID'])->first();
            dd($loads);
        }
    }

}
