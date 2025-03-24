<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;

class InvoiceController extends Controller
{
    public function index($type = null)
    {
        return view('customer.pages.invoice.index' , compact('type'));
    }

    public function getInvoiceData(Request $request)
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
            ->addColumn('ticket_list', function ($invoice) {
                return '<a href="https://go.microsoft.com/fwlink/?LinkID=521962" target="_blank" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Download Excel
                        </a>';
            })
            ->addColumn('tickets', function () {
                return rand(1, 100); // Generates a random number for demo
            })
            ->addColumn('select_all', function ($invoice) {
                return '<input type="checkbox" name="select_invoice[]" value="' . $invoice->InvoiceID . '">';
            })
            ->rawColumns(['ticket_list', 'select_all', 'tickets','action']) // Ensures HTML is rendered
            ->make(true);
    }
}
