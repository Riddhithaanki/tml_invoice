<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingLoad;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\PriceHistory;
use DB;
class InvoiceController extends Controller
{
    public function index($type = null)
    {
        return view('customer.pages.invoice.index', compact('type'));
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
            ->rawColumns(['ticket_list', 'select_all', 'tickets', 'action']) // Ensures HTML is rendered
            ->make(true);
    }

    public function updateLoadPrice(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'new_price' => 'required|numeric|min:0',
        ]);

        // Find the load by TicketID
        $load = BookingLoad::where('TicketID', $request->ticket_id)->first();

        if (!$load) {
            return response()->json(['error' => 'Load not found'], 404);
        }

        // Store old price for history (optional)
        PriceHistory::create([
            'TicketID' => $load->TicketID,
            'OldPrice' => $load->LoadPrice,
            'NewPrice' => $request->new_price,
            'ChangedBy' => auth()->id(),
            'ChangedAt' => now()
        ]);

        // Update the price
        $load->LoadPrice = $request->new_price;
        $load->save();

        return response()->json(['success' => 'Price updated successfully', 'new_total' => $this->calculateNewTotals($load->BookingID)]);
    }

    private function calculateNewTotals($bookingId)
    {
        $loads = BookingLoad::where('BookingID', $bookingId)->get();

        $subTotal = $loads->sum('LoadPrice');
        $vat = $subTotal * 0.2; // Assuming VAT is 20%
        $total = $subTotal + $vat;

        return [
            'sub_total' => number_format($subTotal, 2),
            'vat' => number_format($vat, 2),
            'total' => number_format($total, 2),
        ];
    }

    public function getPriceHistory(Request $request)
    {
        $ticketId = $request->ticket_id;

        // Fetch price history with username from users table
        $history = DB::table('price_histories')
            ->join('tbl_users', 'price_histories.ChangedBy', '=', 'tbl_users.userId')
            ->where('price_histories.TicketID', $ticketId)
            ->orderBy('price_histories.ChangedAt', 'desc')
            ->select('price_histories.*', 'tbl_users.name as ChangedByName') // Get username
            ->get();

        // Check if history exists
        if ($history->isEmpty()) {
            return response()->json(['error' => 'No price history found.'], 200);
        }

        return response()->json(['history' => $history]);
    }


}
