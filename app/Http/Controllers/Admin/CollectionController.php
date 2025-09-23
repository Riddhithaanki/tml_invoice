<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\Booking;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SplitExcelExport;
use App\Models\InvoiceItem;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        // $type = $request->query('type', 'loads');
        // $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'
          $type = $request->query('type', 'withtipticket'); // Default: 'withtipticket'
        $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'
        return view('admin.pages.collection.index', compact('type', 'invoice_type'));
    }
public function newindex(Request $request)
    {
        $update_type = $request->query('update_type', 'loads');
        // $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'
        $type = $request->query('type', 'withtipticket'); // Default: 'withtipticket'
        $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'
        return view('admin.pages.collection.newindex', compact('type', 'update_type', 'invoice_type'));
    }
    // public function getCollectionInvoiceData(Request $request, $type = 'loads')
     public function getCollectionInvoiceData(Request $request)
    {
       
        $type = $request->input('type', 'withtipticket');
        $update_type = $request->input('update_type', 'loads');
        $invoiceType = $request->input('invoice_type', 'preinvoice');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
       //  old Code 
        // $query = Booking::select([
        //     'tbl_booking1.BookingRequestID',
        //     'tbl_booking_request.CreateDateTime',
        //     'tbl_booking_request.CompanyName',
        //     'tbl_booking1.MaterialName',
        //     'tbl_booking_request.OpportunityName',
        //     'tbl_booking_request.InvoiceHold',
        //     \DB::raw('MAX(tbl_booking1.BookingID) as BookingID'),
        //     \DB::raw('MAX(tbl_booking1.BookingType) as BookingType'),
        //     \DB::raw('CASE
        //         WHEN EXISTS (SELECT 1 FROM ready_invoices WHERE BookingRequestID = tbl_booking1.BookingRequestID) THEN "ready"
        //         ELSE "power"
        //     END as invoice_status')
        // ])
        //     ->join('tbl_booking_request', 'tbl_booking1.BookingRequestID', '=', 'tbl_booking_request.BookingRequestID')
        //     ->where('tbl_booking1.BookingType', 1)
        //     ->groupBy([
        //         'tbl_booking1.BookingRequestID',
        //         'tbl_booking_request.CreateDateTime',
        //         'tbl_booking_request.CompanyName',
        //         'tbl_booking1.MaterialName',
        //         'tbl_booking_request.OpportunityName',
        //         'tbl_booking_request.InvoiceHold'
        //     ])
        //     ->orderBy('tbl_booking_request.CreateDateTime', 'desc');


        // New update Query as per discessued
      
        $query = Booking::select([
                    'tbl_booking_request.CompanyName',
                    'tbl_booking_request.OpportunityName',
                    'tbl_booking1.MaterialName',
                    \DB::raw('MAX(tbl_booking1.BookingRequestID) as BookingRequestID'),
                    \DB::raw('MAX(tbl_booking1.BookingID) as BookingID'),
                    \DB::raw('MAX(tbl_booking1.BookingType) as BookingType'),
                    \DB::raw('MAX(tbl_booking_request.CreateDateTime) as CreateDateTime'),
                    \DB::raw('MAX(tbl_booking_request.InvoiceHold) as InvoiceHold'),
                    \DB::raw('CASE
                        WHEN EXISTS (
                            SELECT 1 FROM ready_invoices 
                            WHERE ready_invoices.BookingRequestID = MAX(tbl_booking1.BookingRequestID)
                        ) THEN "ready"
                        ELSE "power"
                    END as invoice_status')
                ])
                ->join('tbl_booking_request', 'tbl_booking1.BookingRequestID', '=', 'tbl_booking_request.BookingRequestID')
                ->where('tbl_booking1.BookingType', 1)
                ->groupBy([
                    'tbl_booking_request.CompanyName',
                    'tbl_booking_request.OpportunityName',
                    'tbl_booking1.MaterialName'
                ])
                  ->orderByDesc(\DB::raw('MAX(tbl_booking_request.CreateDateTime)'));


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
        } elseif ($invoiceType === 'holdinvoice') {
            $query->where('tbl_booking_request.InvoiceHold', 1)
                ->whereNotExists(function ($q) {
                    $q->select(\DB::raw(1))
                        ->from('ready_invoices')
                        ->where('is_hold', 1)
                        ->whereRaw('ready_invoices.BookingRequestID = tbl_booking1.BookingRequestID');
                });
        }
        
         // Filter by type if specified

        if ($type === 'withtipticket') {
            // Show records where there is at least one HAZ (Type=1), Non-HAZ (Type=0), or Inte (Type=2) load with a generated tip ticket ID
            $query->whereExists(function ($q) {
            $q->select(\DB::raw(1))
                ->from('tbl_booking_loads1')
                ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
                ->join('tbl_tipticket', 'tbl_tipticket.LoadID', '=', 'tbl_booking_loads1.LoadID')
                 ->whereIn('tbl_materials.Type', [0, 1, 2])
                ->whereNotNull('tbl_tipticket.LoadID') // TipTicket is not null
                ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        } elseif ($type === 'withouttipticket') {
            // Show records where there is at least one load with a material type NOT in [0, 1, 2]
            $query->whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('tbl_booking_loads1')
                    ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
                    ->whereIn('tbl_materials.Type', [0, 1, 2])
                    ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        } elseif ($type === 'missingtipticket') {
            // Show records where there is at least one HAZ or Non-HAZ load and tip ticket is missing (null or empty)
            $query->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('tbl_booking_loads1')
                    ->join('tbl_materials', 'tbl_materials.MaterialID', '=', 'tbl_booking_loads1.MaterialID')
                    ->whereIn('tbl_materials.Type', [0, 1])
                    ->where(function ($subQ) {
                        $subQ->whereNull('tbl_booking_loads1.TicketID')
                             ->orWhere('tbl_booking_loads1.TicketID', '');
                    })
                    ->whereRaw('tbl_booking_loads1.BookingRequestID = tbl_booking1.BookingRequestID');
            });
        }
        if ($update_type === 'tonnage') {
            $query->where('tbl_booking1.TonBook', 0);
        } elseif ($update_type === 'loads') {
            $query->where(function ($q) {
                $q->whereNull('tbl_booking1.TonBook')
                    ->orWhere('tbl_booking1.TonBook', '!=', 0);
            });
        }
       

//    dd(
//      $query->toSql(),      
//      $query->getBindings()  
//  );
        
        // if ($type === 'tonnage') {
        //     $query->where('tbl_booking1.TonBook', 1);
        // } elseif ($type === 'loads') {
        //     $query->where(function ($q) {
        //         $q->whereNull('tbl_booking1.TonBook')
        //             ->orWhere('tbl_booking1.TonBook', '!=', 1);
        //     });
        // }

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
            ->filterColumn('MaterialName', function ($query, $keyword) {
                $query->where('tbl_booking1.MaterialName', 'like', "%$keyword%");
            })
            ->editColumn('CreateDateTime', function ($row) {
                return Carbon::parse($row->CreateDateTime)->format('d-m-Y');
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
                  //old code  
                    // return '<a href="' . route('invoice.show', Crypt::encrypt($booking->BookingRequestID)) . '"
                    //     class="btn btn-sm btn-primary">View</a>';
                 // new code 
                 return '<a href="' . route('invoice.show', [Crypt::encrypt($booking->BookingRequestID), Crypt::encrypt($booking->MaterialName)]) . '" class="btn btn-sm btn-primary">View</a>';
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

    public function exportSplitExcel(Request $request)
    {
         try {
            $bookingId = $request->booking_id;

            $booking = Booking::with(['loads' => function ($query) {
                $query->select('tbl_booking_loads1.*')
                    ->where(function ($q) {
                        $q->where('Status', '!=', 5)
                            ->orWhereNull('Status');
                    });
            }, 
            'bookingRequest' => function ($query) {
                 $query->select('BookingRequestID', 'CompanyName', 'OpportunityName', 'PostCode');
            } ])->where('BookingID', $bookingId)->first();

            if (!$booking) {
            
                return response()->json(['error' => 'Booking not found'], 404);
            }
    
        $loads = $booking->loads;
        $groupedLoads = $loads->groupBy('MaterialName');
        $invoiceItems = [];

        foreach ($groupedLoads as $materialName => $group) {
           

            $totalLoads = $group->sum('Loads');
            $totalAmount = $group->sum(function ($load) {
                $price = $load->LoadPrice ?? 0;
                $quantity = $load->Loads ?? 1;
                return $price * $quantity;
            });

            $loadDetails = $group->map(function ($load) {
              
                return [
                    'LoadID'          => $load->LoadID,
                    'ConveyanceNo'    => $load->ConveyanceNo,
                    'TicketID'        => $load->TicketID,
                    'JobStartDateTime'=> $load->JobStartDateTime,
                    'DriverName'      => $load->DriverName,
                    'VehicleRegNo'    => $load->VehicleRegNo,
                    'GrossWeight'     => $load->GrossWeight,
                    'Tare'            => $load->Tare,
                    'Net'             => $load->Net,
                    'SiteInDateTime'  => $load->SiteInDateTime,
                    'SiteOutDateTime' => $load->SiteOutDateTime,
                    'Status'          => $load->Status,
                    'LoadPrice'       => $load->LoadPrice ?? 0,
                    'Loads'           => $load->Loads ?? 1,
                    'ReceiptName'     => $load->ReceiptName,
                ];
            })->values()->toArray(); // make sure it's a clean array
         
            $invoiceItems[] = [
                'MaterialName' => $materialName,
                'totalLoads'   => $totalLoads,
                'totalAmount'  => $totalAmount,
                'loads'        => $loadDetails,
            ];
        }
         $uniqueId = mt_rand(100000000, 999999999);
         $timestamp = \Carbon\Carbon::now()->format('YmdHis');
     
            $company = $booking->bookingRequest->CompanyName;
            $address = $booking->bookingRequest->OpportunityName;
            $postcode = $booking->bookingRequest->PostCode;

         $filename = "{$company}@{$address}@{$timestamp}@{$uniqueId}.xls";
      
        return Excel::download(new SplitExcelExport($invoiceItems),  $filename);

    } catch (\Exception $e) {
        
        return response()->json(['error' => 'Something went wrong. Please check logs.'], 500);
    }
                 
                
       }

}
