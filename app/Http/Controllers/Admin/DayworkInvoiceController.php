<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;
use Illuminate\Support\Facades\Crypt;

class DayworkInvoiceController extends Controller
{
    //
    public function index()
    {
        $recentInvoice = BookingInvoice::with('booking')->orderBy('InvoiceID', 'desc')->limit(2)->get();
        $readyHoldInvoiceCount = BookingInvoice::where('Status', '0')->count();
        $completedInvoice = BookingInvoice::where('Status', '1')->count();
        return view('admin.pages.daywork_invoice.index', compact('recentInvoice' , 'readyHoldInvoiceCount', 'completedInvoice'));
    }
}
