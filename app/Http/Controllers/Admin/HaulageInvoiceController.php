<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;

class HaulageInvoiceController extends Controller
{
    public function index()
    {
        return view('admin.pages.haulage.index');
    }

    public function getHaulageInvoiceData()
    {
        $query = BookingRequest::select([
            'BookingRequestID',
            'CreateDateTime',
            'CompanyName',
            'OpportunityName',
        ])
            ->with('booking')
            ->whereHas('booking', function ($q) {
                $q->where('BookingType', 1);
            });


        return DataTables::of($query)
            ->addIndexColumn() // Adds SR. No column
            ->addColumn('action', function ($invoice) {
                return '<a href="' . route('invoice.show', Crypt::encrypt($invoice->BookingRequestID)) . '"
                        class="btn btn-sm btn-primary">View</a>';
            })
            ->rawColumns(['action']) // Ensures HTML is rendered
            ->make(true);
    }
}
