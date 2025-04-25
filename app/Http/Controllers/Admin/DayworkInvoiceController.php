<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\Booking;

class DayworkInvoiceController extends Controller
{

    public function index(Request $request)
    {
        $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'
        return view('admin.pages.daywork_invoice.index', compact('invoice_type'));
    }

    public function getDayworkInvoiceData(Request $request)
    {
        $invoiceType = $request->input('invoice_type', 'preinvoice');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch bookings first, as we need BookingID as the main identifier
        $query = Booking::select([
            'tbl_booking1.BookingID',
            'tbl_booking1.BookingRequestID',
            'tbl_booking1.BookingType',
            'tbl_booking_request.CreateDateTime',
            'tbl_booking_request.CompanyName',
            'tbl_booking_request.OpportunityName',
        ])
            ->join('tbl_booking_request', 'tbl_booking1.BookingRequestID', '=', 'tbl_booking_request.BookingRequestID')
            ->where('tbl_booking1.BookingType', 3)
            ->orderBy('tbl_booking_request.CreateDateTime', 'desc');

        // Apply date filters
        if (!empty($startDate)) {
            $query->whereDate('tbl_booking_request.CreateDateTime', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('tbl_booking_request.CreateDateTime', '<=', $endDate);
        }

        // Filter by invoice type if specified
        if ($invoiceType === 'readyinvoice') {
            $query->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('ready_invoices')
                  ->whereRaw('ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        } elseif ($invoiceType === 'preinvoice') {
            $query->whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('ready_invoices')
                  ->whereRaw('ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        }

        return DataTables::of($query)
            ->addIndexColumn() // Adds SR. No column
            ->addColumn('CompanyName', function ($booking) {
                return $booking->CompanyName ?? 'N/A';
            })
            ->addColumn('OpportunityName', function ($booking) {
                return $booking->OpportunityName ?? 'N/A';
            })
            ->addColumn('CreateDateTime', function ($booking) {
                return $booking->CreateDateTime ?? 'N/A';
            })
            ->addColumn('action', function ($booking) {
                if ($booking->BookingRequestID) {
                    return '<a href="' . route('invoice.show', Crypt::encrypt($booking->BookingRequestID)) . '"
            class="btn btn-sm btn-primary">View</a>';
                }
                return 'No Booking Found';
            })
            ->rawColumns(['action']) // Ensures HTML is rendered
            ->make(true);
    }
}
