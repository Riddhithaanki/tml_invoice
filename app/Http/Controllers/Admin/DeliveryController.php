<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\Booking;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        // Get parameters with default values
        $type = $request->query('type', 'withtipticket'); // Default: 'withtipticket'
        $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'

        return view('admin.pages.delivery.index', compact('type', 'invoice_type'));
    }

    public function getDeliveryInvoiceData(Request $request)
    {
        $type = $request->input('type', 'withtipticket');
        $invoiceType = $request->input('invoice_type', 'preinvoice');

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
            ->where('tbl_booking1.BookingType', 2)
            ->orderBy('tbl_booking_request.CreateDateTime', 'desc');

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
                if ($booking->BookingID) {
                    return '<a href="' . route('invoice.show', Crypt::encrypt($booking->BookingID)) . '"
            class="btn btn-sm btn-primary">View</a>';
                }
                return 'No Booking Found';
            })
            ->rawColumns(['action']) // Ensures HTML is rendered
            ->make(true);

    }

}
