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
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Booking::select([
            'tbl_booking1.BookingID',
            'tbl_booking1.BookingRequestID',
            'tbl_booking1.BookingType',
            'tbl_booking_request.CreateDateTime',
            'tbl_booking_request.CompanyName',
            'tbl_booking_request.OpportunityName',
            \DB::raw('CASE
            WHEN EXISTS (
                SELECT 1 FROM ready_invoices
                WHERE ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID
            ) THEN "ready"
            ELSE "power"
        END as invoice_status')
        ])
            ->join('tbl_booking_request', 'tbl_booking1.BookingRequestID', '=', 'tbl_booking_request.BookingRequestID')
            ->where('tbl_booking1.BookingType', 2)
            ->orderBy('tbl_booking_request.CreateDateTime', 'desc');

        // Apply date filters
        if (!empty($startDate)) {
            $query->whereDate('tbl_booking_request.CreateDateTime', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('tbl_booking_request.CreateDateTime', '<=', $endDate);
        }

        // Filter by invoice type
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

        // ✅ Tip ticket filtering logic
        if ($type === 'withtipticket') {
            $query->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('tbl_booking_loads1')
                    ->join('tbl_tipticket', 'tbl_tipticket.LoadID', '=', 'tbl_booking_loads1.LoadID')
                    ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        } elseif ($type === 'withouttipticket') {
            $query->whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('tbl_booking_loads1')
                    ->join('tbl_tipticket', 'tbl_tipticket.LoadID', '=', 'tbl_booking_loads1.LoadID')
                    ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('CreateDateTime', function ($query, $keyword) {
                $query->where('tbl_booking_request.CreateDateTime', 'like', "%$keyword%");
            })
            ->filterColumn('CompanyName', function ($query, $keyword) {
                $query->where('tbl_booking_request.CompanyName', 'like', "%$keyword%");
            })
            ->filterColumn('OpportunityName', function ($query, $keyword) {
                $query->where('tbl_booking_request.OpportunityName', 'like', "%$keyword%");
            })
            ->addColumn('CompanyName', fn($booking) => $booking->CompanyName ?? 'N/A')
            ->addColumn('OpportunityName', fn($booking) => $booking->OpportunityName ?? 'N/A')
            ->addColumn('CreateDateTime', fn($booking) => $booking->CreateDateTime ?? 'N/A')
            ->addColumn('invoice_status', function ($booking) {
                $status = $booking->invoice_status;
                $badgeClass = $status === 'ready' ? 'badge-success' : 'badge-warning';
                $statusText = $status === 'ready' ? 'Ready Invoice' : 'Power Invoice';
                return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('action', function ($booking) {
                if ($booking->BookingRequestID) {
                    return '<a href="' . route('invoice.show', Crypt::encrypt($booking->BookingRequestID)) . '"
                class="btn btn-sm btn-primary">View</a>';
                }
                return 'No Booking Found';
            })
            ->rawColumns(['action', 'invoice_status'])
            ->make(true);
    }



}
