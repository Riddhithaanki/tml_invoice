<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingLoad;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\BookingRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Models\PriceHistory;
use DB;
use Carbon\Carbon;
class InvoiceController extends Controller
{
    public function index($type = null)
    {
        $type = $type ?? 1;
        return view('customer.pages.invoice.index', compact('type'));
    }

    public function getInvoiceData(Request $request)
    {
        $query = Invoice::query();
        $query->where('isApproved', 1);
        // Date filtering
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('select_all', function ($row) {
                return '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            ->editColumn('CompanyName', function ($row) {
                return $row->CompanyName ?? 'NA';
            })
            ->editColumn('billing_address', function ($row) {
                return $row->billing_address ?? 'NA';
            })
            ->editColumn('ticket_list', function ($row) {
                return $row->ticket_list ?? 'NA';
            })
            ->addColumn('tickets', function ($row) {
                $documentGuids = \DB::connection('mysql_second')
                    ->table('fd_documents')
                    ->where('Invoice_No', $row->id)
                    ->pluck('GUID');

                $ticketCount = \DB::connection('mysql_second')
                    ->table('fd_images')
                    ->whereIn('DocGUID', $documentGuids)
                    ->count();

                $url = route('customer.tickets.show', $row->id); // Replace with your route

                return '<a href="' . $url . '">' . $ticketCount . ' Ticket(s)</a>';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-m-Y');
            })

            ->rawColumns(['ticket_list', 'tickets', 'select_all'])
            ->make(true);
    }

    public function indexArchive($type = null)
    {
        $type = $type ?? 1;
        return view('customer.pages.invoice.index', compact('type'));
    }



    public function pdflist(Request $request)
    {
        return view('customer.pages.invoice.pdflist');
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
