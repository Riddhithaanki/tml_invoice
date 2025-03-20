<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        // Get parameters with default values
        $type = $request->query('type', 'withtipticket'); // Default: 'withtipticket'
        $invoice_type = $request->query('invoice_type', 'preinvoice'); // Default: 'preinvoice'

        return view('admin.pages.delivery.index' , compact('type', 'invoice_type') );
    }
    
    public function getDeliveryInvoiceData(Request $request)
    {
        $type = $request->input('type', 'withtipticket');
        $invoiceType = $request->input('invoice_type', 'preinvoice');
        
        $query = BookingRequest::select([
            'BookingRequestID',
            'CreateDateTime',
            'CompanyName',
            'OpportunityName',
        ])
            ->with('booking')
            ->where('is_delete', 0) 
            ->whereHas('booking', function ($q) {
                $q->where('BookingType', 2);
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
