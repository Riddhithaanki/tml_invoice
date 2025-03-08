<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\Booking;

class CollectionInvoicesController extends Controller
{
    //
    public function index($type = null)
    {
        $invoices = BookingInvoice::with('booking')->orderBy('InvoiceID', 'desc')->get();
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('admin.pages.collection_invoice.index', compact('invoices', 'readyHoldInvoiceCount', 'completedInvoice'));
    }

    public function details()
    {
        $recentInvoice = BookingInvoice::with('booking')->orderBy('InvoiceID', 'desc')->limit(2)->get();
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('admin.pages.collection_invoice.details', compact('recentInvoice', 'readyHoldInvoiceCount', 'completedInvoice'));
    }
}
