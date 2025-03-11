<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;

class CollectionController extends Controller
{
    public function index($type = null)
    {
        return view('admin.pages.collection.index' , compact('type'));
    }

    public function getCollectionInvoiceData(Request $request)
    {
        $type = $request->input('type');
        
        $query = BookingRequest::select([
            'InvoiceID',
            'BookingRequestID',
            'CreateDateTime',
            'CompanyName',
            'OpportunityName',
        ])
            ->with('booking')
            ->whereHas('booking', function ($q) {
                $q->where('BookingType', 2);
            });

        return DataTables::of($query)
            ->addIndexColumn() // Adds SR. No column
            ->addColumn('action', function ($invoice) {
                return '<a href="' . route('invoice.show', Crypt::encrypt($invoice->InvoiceID)) . '"
                        class="btn btn-sm btn-primary">View</a>';
            })
            ->rawColumns(['action']) // Ensures HTML is rendered
            ->make(true);
    }

}
