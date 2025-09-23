<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\Booking;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        // Get parameters with default values
        // $type = $request->query('type', 'withtipticket'); // Default: 'withtipticket'
        // $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'
        // $update_type = $request->query('update_type', 'loads');
        //  $type = $request->query('type', 'loads');
        //  $invoice_type = $request->query('invoice_type', 'preinvoice');
        $type = $request->query('update_type', 'loads');
       // $type = $request->query('type', 'withtipticket'); 
        $invoice_type = $request->query('invoice_type', 'preinvoice'); 
        return view('admin.pages.delivery.index', compact('type', 'invoice_type'));
    }

    public function getDeliveryInvoiceData(Request $request)
    {
        // ################   New Code #####################

        //$type = $request->input('type', 'withtipticket');
        $type = $request->input('type', 'loads');
        $invoiceType = $request->input('invoice_type', 'preinvoice');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        // ############### OLD Code ##############
        // $type = $request->input('type', 'loads');
        // $invoiceType = $request->input('invoice_type', 'preinvoice');
        // $startDate = $request->input('start_date');
        // $endDate = $request->input('end_date');

        // $type = $request->input('type', 'withtipticket');
        // $invoiceType = $request->input('invoice_type', 'preinvoice');
        // $startDate = $request->input('start_date');
        // $endDate = $request->input('end_date');

        $query = Booking::select([
            'tbl_booking1.BookingRequestID',
            'tbl_booking_request.CreateDateTime',
            'tbl_booking_request.CompanyName',
            'tbl_booking_request.InvoiceHold',
            'tbl_booking_request.OpportunityName',
            'tbl_booking1.MaterialName',
            \DB::raw('MAX(tbl_booking1.BookingID) as BookingID'),
            \DB::raw('MAX(tbl_booking1.BookingType) as BookingType'),
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
            ->groupBy([
                'tbl_booking1.BookingRequestID',
                'tbl_booking_request.CreateDateTime',
                'tbl_booking_request.CompanyName',
                'tbl_booking_request.InvoiceHold',
                'tbl_booking_request.OpportunityName',
                 'tbl_booking1.MaterialName',
            ])
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
        }elseif ($invoiceType === 'holdinvoice') {
            $query->where('tbl_booking_request.InvoiceHold', 1)
                ->whereNotExists(function ($q) {
                    $q->select(\DB::raw(1))
                        ->from('ready_invoices')
                        ->where('is_hold', 1)
                        ->whereRaw('ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID');
                });
        }
         // Filter by type if specified

        //  if ($type === 'withtipticket') {
        //     $query->whereExists(function ($q) {
        //         $q->select(\DB::raw(1))
        //             ->from('tbl_booking_loads1')
        //             ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
        //             ->where('tbl_materials.Type', '=', 1)
        //             ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
        //     });
        // } elseif ($type === 'withouttipticket') {
        //     $query->whereNotExists(function ($q) {
        //         $q->select(\DB::raw(1))
        //             ->from('tbl_booking_loads1')
        //             ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
        //             ->where('tbl_materials.Type', '=', 0)
        //             ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
        //     });
        // }elseif($type === 'missingtipticket')
        // {
        //     $query->whereNotExists(function ($q) {
        //         $q->select(\DB::raw(1))
        //             ->from('tbl_booking_loads1')
        //             ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
        //             ->where('tbl_booking_loads1.Status', '=', 0)
        //             ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
        //     });
        // }
        
         if ($type === 'tonnage') {
            $query->where('tbl_booking1.TonBook', 1);
        } elseif ($type === 'loads') {
            $query->where(function ($q) {
                $q->whereNull('tbl_booking1.TonBook')
                    ->orWhere('tbl_booking1.TonBook', '!=', 1);
            });
        }
        //dd($type, $update_type,$invoiceType);

        // if ($type === 'withtipticket') {
        //     $query->whereExists(function ($q) {
        //         $q->select(\DB::raw(1))
        //             ->from('tbl_booking_loads1')
        //             ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
        //             ->where('tbl_materials.Type', '=', 1)
        //             ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
        //     });
        // } elseif ($type === 'withouttipticket') {
        //     $query->whereNotExists(function ($q) {
        //         $q->select(\DB::raw(1))
        //             ->from('tbl_booking_loads1')
        //             ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
        //             ->where('tbl_materials.Type', '=', 1)
        //             ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
        //     });
        // }

        return DataTables::of($query)
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
             ->filterColumn('MaterialName', function ($query, $keyword) {
                $query->where('tbl_booking1.MaterialName', 'like', "%$keyword%");
            })
           ->editColumn('CreateDateTime', function ($row) {
                return Carbon::parse($row->CreateDateTime)->format('d-m-Y');
            })
            ->addColumn('CompanyName', fn($booking) => $booking->CompanyName ?? 'N/A')
            ->addColumn('OpportunityName', fn($booking) => $booking->OpportunityName ?? 'N/A')
            ->addColumn('CreateDateTime', fn($booking) => $booking->CreateDateTime ?? 'N/A')
             ->addColumn('MaterialName', function ($booking) {
                return $booking->MaterialName ?? 'N/A';
            })
            ->filterColumn('InvoiceHold', function($query, $keyword) {
                if (stripos($keyword, 'yes') !== false) {
                    $query->where('tbl_booking_request.InvoiceHold', 1);
                } elseif (stripos($keyword, 'no') !== false) {
                    $query->where('tbl_booking_request.InvoiceHold', 0);
                }
            })
            ->editColumn('InvoiceHold', function ($row) {
                return $row->InvoiceHold == 1 ? 'Yes' : 'No';
            })
            ->addColumn('action', function ($booking) {
                if ($booking->BookingRequestID) {
                    
                //   return '<a href="' . route('invoice.show', Crypt::encrypt($booking->BookingRequestID)) . '"
                //         class="btn btn-sm btn-primary">View</a>';
                // new code 
                  return '<a href="' . route('invoice.show', [Crypt::encrypt($booking->BookingRequestID), Crypt::encrypt($booking->MaterialName)]) . '" class="btn btn-sm btn-primary">View</a>';
                }
                return 'No Booking Found';
            })
            ->rawColumns(['action', 'invoice_status'])
            ->make(true);
    }
}
