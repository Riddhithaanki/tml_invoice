<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\Booking;
use Carbon\Carbon;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'loads');
        $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'

        return view('admin.pages.collection.index', compact('type', 'invoice_type'));
    }

    public function getCollectionInvoiceData(Request $request, $type = 'loads')
    {

        $type = $request->input('type', 'loads');
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
                WHEN EXISTS (SELECT 1 FROM ready_invoices WHERE BookingRequestID = tbl_booking1.BookingRequestID) THEN "ready"
                ELSE "power"
            END as invoice_status')
        ])
            ->join('tbl_booking_request', 'tbl_booking1.BookingRequestID', '=', 'tbl_booking_request.BookingRequestID')
            ->where('tbl_booking1.BookingType', 1)
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
        if ($type === 'tonnage') {
            $query->where('tbl_booking1.TonBook', 1);
        } elseif ($type === 'load') {
            $query->where(function ($q) {
                $q->whereNull('tbl_booking1.TonBook')
                    ->orWhere('tbl_booking1.TonBook', '!=', 1);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('CreateDateTime', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw("DATE_FORMAT(tbl_booking_request.CreateDateTime, '%d-%m-%Y %H:%i') like ?", ["%{$keyword}%"])
                    ->orWhereRaw("DATE_FORMAT(tbl_booking_request.CreateDateTime, '%d-%m-%Y') like ?", ["%{$keyword}%"])
                    ->orWhereRaw("DATE_FORMAT(tbl_booking_request.CreateDateTime, '%H:%i') like ?", ["%{$keyword}%"]);
                });
            })

            ->filterColumn('CompanyName', function ($query, $keyword) {
                $query->where('tbl_booking_request.CompanyName', 'like', "%$keyword%");
            })
            ->filterColumn('OpportunityName', function ($query, $keyword) {
                $query->where('tbl_booking_request.OpportunityName', 'like', "%$keyword%");
            })
             ->editColumn('CreateDateTime', function ($row) {
                return Carbon::parse($row->CreateDateTime)->format('d-m-Y H:i');
            })
            ->addColumn('CompanyName', function ($booking) {
                return $booking->CompanyName ?? 'N/A';
            })
            ->addColumn('OpportunityName', function ($booking) {
                return $booking->OpportunityName ?? 'N/A';
            })
            ->addColumn('CreateDateTime', function ($booking) {
                return $booking->CreateDateTime ?? 'N/A';
            })
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

    // public function getCollectionInvoiceData(Request $request)
    // {
    //     $type = $request->input('type');
    //     $invoiceType = $request->input('invoice_type', 'preinvoice');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     // Fetch bookings first, as we need BookingID as the main identifier
    //     $query = Booking::select([
    //         'tbl_booking1.BookingID',
    //         'tbl_booking1.BookingRequestID',
    //         'tbl_booking1.BookingType',
    //         'tbl_booking_request.CreateDateTime',
    //         'tbl_booking_request.CompanyName',
    //         'tbl_booking_request.OpportunityName',
    //     ])
    //         ->join('tbl_booking_request', 'tbl_booking1.BookingRequestID', '=', 'tbl_booking_request.BookingRequestID')
    //         ->where('tbl_booking1.BookingType', 1)
    //         ->orderBy('tbl_booking_request.CreateDateTime', 'desc');

    //         // Apply date filters
    //     if (!empty($startDate)) {
    //         $query->whereDate('tbl_booking_request.CreateDateTime', '>=', $startDate);
    //     }
    //     if (!empty($endDate)) {
    //         $query->whereDate('tbl_booking_request.CreateDateTime', '<=', $endDate);
    //     }

    //     // Filter by invoice type if specified
    //     if ($invoiceType === 'readyinvoice') {
    //         $query->whereExists(function ($q) {
    //             $q->select(\DB::raw(1))
    //               ->from('ready_invoices')
    //               ->whereRaw('ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID');
    //         });
    //     } elseif ($invoiceType === 'preinvoice') {
    //         $query->whereNotExists(function ($q) {
    //             $q->select(\DB::raw(1))
    //               ->from('ready_invoices')
    //               ->whereRaw('ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID');
    //         });
    //     }

    //     return DataTables::of($query)
    //         ->addIndexColumn() // Adds SR. No column
    //         ->addColumn('CompanyName', function ($booking) {
    //             return $booking->CompanyName ?? 'N/A';
    //         })
    //         ->addColumn('OpportunityName', function ($booking) {
    //             return $booking->OpportunityName ?? 'N/A';
    //         })
    //         ->addColumn('CreateDateTime', function ($booking) {
    //             return $booking->CreateDateTime ?? 'N/A';
    //         })
    //         ->addColumn('action', function ($booking) {
    //             if ($booking->BookingRequestID) {
    //                 return '<a href="' . route('invoice.show', Crypt::encrypt($booking->BookingRequestID)) . '"
    //         class="btn btn-sm btn-primary">View</a>';
    //             }
    //             return 'No Booking Found';
    //         })
    //         ->rawColumns(['action']) // Ensures HTML is rendered
    //         ->make(true);

    // }

}
