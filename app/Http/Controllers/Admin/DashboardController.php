<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLoad;
use App\Models\BookingRequest;
use App\Models\ReadyInvoice;
use App\Models\ReadyInvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BookingInvoice;
use App\Models\BookingInvoiceItem;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceDifference;
use App\Models\Invoice;
use App\Models\BookingRelationship;

class DashboardController extends Controller
{
    public function index()
    {
        $recentInvoice = ReadyInvoice::with('booking')->orderBy('CreateDateTime', "DESC")->limit(20)->get();

        $readyHoldInvoiceCount = BookingRequest::where('InvoiceHold', '1')->count();
        $readyInvoiceCount = ReadyInvoice::count();
        $completedInvoice = InvoiceDifference::where('status', '1')->count();
        $bookingCount = Booking::count();
        return view('dashboard', compact('recentInvoice', 'bookingCount','readyHoldInvoiceCount', 'completedInvoice','readyInvoiceCount'));
    }

    public function getInvoiceData($id)
    {
        try {
            $id = Crypt::decrypt($id);

            // First try to find the booking
            $booking = Booking::where('BookingRequestID', $id)
                ->first();

            if (!$booking) {
                return abort(404, 'Booking not found');
            }

            $bookingRequestId = $booking->BookingRequestID;

            // Check if this BookingRequestID exists in the ready_invoice table
            $readyInvoice = ReadyInvoice::where('BookingRequestID', $bookingRequestId)->first();
            if ($readyInvoice) {
                $invoice = $readyInvoice->load(['items', 'booking']);
                $user = User::where('userId', '=', $invoice->CreatedUserID)->first();
                return view('admin.pages.invoice.viewinvoice', compact('invoice', 'user'));
            }

            // If not in ready_invoice, check BookingRequest
            $invoice = BookingRequest::with(['booking', 'invoice_items', 'invoice'])
                ->where('BookingRequestID', $bookingRequestId)
                ->first();

            if (!$invoice) {
                return abort(404, 'Invoice not found');
            }

            // Check if this is a split invoice
            $isSplitInvoice = !is_null($invoice->parent_invoice_id);

            // Get all related bookings
            $bookings = Booking::withCount('loads')
                ->with(['loads' => function($query) {
                    $query->addSelect([
                        'tbl_booking_loads1.*',
                        DB::raw('COALESCE(tbl_booking_loads1.LoadPrice, 0) as LoadPrice')
                    ]);
                }])
                ->where('BookingRequestID', $invoice->BookingRequestID)
                ->when($isSplitInvoice, function ($query) use ($invoice) {
                    return $query->where('parent_invoice_id', $invoice->InvoiceID);
                })
                ->when(!$isSplitInvoice, function ($query) {
                    return $query->whereNull('parent_invoice_id');
                })
                ->get()
                ->each(function ($booking) {
                    // Set the total number of loads
                    $booking->Loads = $booking->loads_count;
                    
                    // Calculate total amount by summing individual load prices
                    $totalAmount = 0;
                    $totalLoads = 0;
                    
                    foreach ($booking->loads as $load) {
                        $loadPrice = $load->LoadPrice ?? 0;
                        $loadCount = $load->Loads ?? 1;
                        $totalAmount += ($loadPrice * $loadCount);
                        $totalLoads += $loadCount;
                    }
                    
                    // Get the price per load from the first load
                    $pricePerLoad = $booking->loads->first() ? $booking->loads->first()->LoadPrice : 0;
                    
                    $booking->TotalAmount = $totalAmount;
                    $booking->Price = $pricePerLoad;
                });

            return view('admin.pages.invoice.invoice_details', compact('invoice', 'bookings', 'booking', 'isSplitInvoice'));

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return abort(404, 'Invalid ID');
        }
    }

    public function getInvoiceItems(Request $request)
    {
        try {
            $bookingId = $request->booking_id;
            
            \Log::info('Fetching invoice items for booking ID: ' . $bookingId);

            // Get the booking with its loads - removing most restrictions
            $booking = Booking::with(['loads' => function($query) use ($bookingId) {
                $query->select('tbl_booking_loads1.*')
                      ->where(function($q) {
                          $q->where('Status', '!=', 5) // Only exclude cancelled loads
                            ->orWhereNull('Status'); // Include loads with null status
                      });
            }])
            ->where('BookingID', $bookingId)
            ->first();

            if (!$booking) {
                \Log::warning('No booking found for ID: ' . $bookingId);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            \Log::info('Found booking with ' . ($booking->loads ? $booking->loads->count() : 0) . ' loads');

            // Group loads by material name without filtering
            $groupedLoads = $booking->loads->groupBy('MaterialName');
            $invoice_items = [];

            foreach ($groupedLoads as $materialName => $loads) {
                \Log::info("Processing material: {$materialName} with " . $loads->count() . " loads");

                // Calculate totals for this material
                $totalLoads = $loads->sum('Loads');
                $totalAmount = $loads->sum(function($load) {
                    $price = $load->LoadPrice ?? 0;
                    $quantity = $load->Loads ?? 1;
                    \Log::info("Load ID: {$load->LoadID}, Price: {$price}, Quantity: {$quantity}");
                    return $price * $quantity;
                });

                // Include all load details regardless of price
                $loadDetails = $loads->map(function($load) {
                    return [
                        'LoadID' => $load->LoadID,
                        'ConveyanceNo' => $load->ConveyanceNo,
                        'TicketID' => $load->TicketID,
                        'JobStartDateTime' => $load->JobStartDateTime,
                        'DriverName' => $load->DriverName,
                        'VehicleRegNo' => $load->VehicleRegNo,
                        'GrossWeight' => $load->GrossWeight,
                        'Tare' => $load->Tare,
                        'Net' => $load->Net,
                        'SiteInDateTime' => $load->SiteInDateTime,
                        'SiteOutDateTime' => $load->SiteOutDateTime,
                        'Status' => $load->Status,
                        'LoadPrice' => $load->LoadPrice ?? 0,
                        'Loads' => $load->Loads ?? 1
                    ];
                });

                $invoice_items[] = [
                    'MaterialName' => $materialName,
                    'totalLoads' => $totalLoads,
                    'totalAmount' => $totalAmount,
                    'loads' => $loadDetails
                ];
            }

            \Log::info('Processed ' . count($invoice_items) . ' materials with loads');
            return response()->json(['invoice_items' => $invoice_items]);

        } catch (\Exception $e) {
            \Log::error('Error in getInvoiceItems: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSplitInvoiceItems(Request $request)
    {
        // Validate input
        $id = $request->invoice_id;
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice number is required'
            ], 400);
        }

        // Fetch booking data
        $bookingData = Booking::with('loads', 'bookingRequest')
            ->where('BookingID', $id)
            ->first();

        if (!$bookingData) {
            return response()->json([
                'success' => false,
                'message' => 'No split invoice found for this booking'
            ], 404);
        }

        // Fetch only split invoice items - exclude already split or merged bookings
        $items = Booking::with('bookingRequest')
            ->where('BookingRequestID', $bookingData->BookingRequestID)
            ->where(function($query) {
                $query->whereNull('parent_invoice_id')
                      ->where('is_merged', 0);
            })
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('booking_relationships')
                      ->whereRaw('(source_booking_id = tbl_booking1.BookingID OR target_booking_id = tbl_booking1.BookingID)')
                      ->where('relationship_type', 'split');
            })
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No split invoice items found'
            ], 404);
        }

        return response()->json(['invoice_items' => $items]);
    }

    public function getMergeBookingItems(Request $request)
    {
        try {
            $id = $request->invoice_id;
            
            \Log::info('Starting getMergeBookingItems for booking ID: ' . $id);

            // Get original booking details
            $originalBooking = DB::table('tbl_booking1 as b')
                ->join('tbl_booking_request as br', 'b.BookingRequestID', '=', 'br.BookingRequestID')
                ->where('b.BookingID', $id)
                ->select([
                    'b.BookingID',
                    'b.BookingType',
                    'br.CompanyName',
                    'br.OpportunityName',
                    'b.parent_invoice_id',
                    'b.is_merged',
                    'b.BookingRequestID'
                ])
                ->first();

            if (!$originalBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original booking not found'
                ], 404);
            } 

            \Log::info('Original booking details:', (array)$originalBooking);

            // Get all eligible bookings with the same company and opportunity
            $query = DB::table('tbl_booking1 as b')
                ->join('tbl_booking_request as br', 'b.BookingRequestID', '=', 'br.BookingRequestID')
                ->leftJoin('booking_relationships as brel', function($join) {
                    $join->on('b.BookingID', '=', 'brel.source_booking_id')
                        ->orOn('b.BookingID', '=', 'brel.target_booking_id');
                })
                ->select([
                    'b.BookingID as booking_id',
                    'b.BookingRequestID as booking_request_id',
                    'br.CompanyName as company_name',
                    'br.OpportunityName as opportunity_name',
                    'b.BookingType as booking_type',
                    'b.parent_invoice_id',
                    'b.is_merged',
                    'br.CreateDateTime',
                    DB::raw('(SELECT COUNT(bl.LoadID) FROM tbl_booking_loads1 bl WHERE bl.BookingID = b.BookingID AND bl.Status != 5) as total_loads'),
                    DB::raw('(SELECT COALESCE(SUM(bl.LoadPrice), 0) FROM tbl_booking_loads1 bl WHERE bl.BookingID = b.BookingID AND bl.Status != 5) as total_amount'),
                    'brel.relationship_type',
                    'brel.source_booking_id',
                    'brel.target_booking_id',
                    DB::raw('(
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                "load_id", bl.LoadID,
                                "material_name", bl.MaterialID,
                                "conveyance_no", bl.ConveyanceNo,
                                "quantity", 1,
                                "price", bl.LoadPrice,
                                "ticket_id", bl.TicketID,
                                "driver_name", bl.DriverName,
                                "vehicle_reg_no", bl.VehicleRegNo,
                                "gross_weight", bl.GrossWeight,
                                "net_weight", bl.Net,
                                "status", bl.Status,
                                "job_start_date", bl.JobStartDateTime
                            )
                        )
                        FROM tbl_booking_loads1 bl 
                        WHERE bl.BookingID = b.BookingID 
                        AND bl.Status != 5
                        ORDER BY bl.JobStartDateTime DESC
                    ) as loads_json')
                ])
                ->where('br.CompanyName', $originalBooking->CompanyName)
                ->where('br.OpportunityName', $originalBooking->OpportunityName)
                ->where('b.BookingID', '!=', $id)
                ->where(function($query) {
                    $query->where(function($q) {
                        // Include non-merged bookings
                        $q->where('b.is_merged', 0);
                    })
                    ->orWhere(function($q) {
                        // Include split bookings
                        $q->whereNotNull('b.parent_invoice_id');
                    })
                    ->orWhere(function($q) {
                        // Include bookings that are part of a split relationship
                        $q->whereNotNull('brel.relationship_type')
                          ->where('brel.relationship_type', 'split');
                    });
                })
                ->where('br.is_delete', 0)  // Only include non-deleted bookings
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tbl_booking_loads1 as bl')
                        ->whereRaw('bl.BookingID = b.BookingID')
                        ->where('bl.Status', '!=', 5)
                        ->where('bl.LoadPrice', '>', 0);
                })
                ->groupBy([
                    'b.BookingID',
                    'b.BookingRequestID',
                    'br.CompanyName',
                    'br.OpportunityName',
                    'b.BookingType',
                    'b.parent_invoice_id',
                    'b.is_merged',
                    'br.CreateDateTime',
                    'brel.relationship_type',
                    'brel.source_booking_id',
                    'brel.target_booking_id'
                ])
                ->orderBy('br.CreateDateTime', 'DESC');

            \Log::info('Generated SQL:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $eligibleBookings = $query->get();

            \Log::info('Found eligible bookings count: ' . $eligibleBookings->count());

            if ($eligibleBookings->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eligible bookings found for merge'
                ], 404);
            }

            // Transform the loads_json string to actual JSON for each booking
            $eligibleBookings = $eligibleBookings->map(function($booking) {
                $booking->loads = json_decode($booking->loads_json);
                unset($booking->loads_json);
                
                // Check if this is a split booking
                $booking->is_split = !is_null($booking->parent_invoice_id) || 
                                   ($booking->relationship_type === 'split');
                
                // Get the original booking details if this is a split
                if ($booking->is_split) {
                    $parentId = $booking->parent_invoice_id ?? 
                               ($booking->source_booking_id === $booking->booking_id ? 
                                $booking->target_booking_id : $booking->source_booking_id);
                    
                    if ($parentId) {
                        $parentBooking = DB::table('tbl_booking1 as b')
                            ->join('tbl_booking_request as br', 'b.BookingRequestID', '=', 'br.BookingRequestID')
                            ->where('b.BookingID', $parentId)
                            ->select('b.BookingID', 'br.CompanyName', 'br.OpportunityName')
                            ->first();
                            
                        if ($parentBooking) {
                            $booking->parent_booking = $parentBooking;
                        }
                    }
                }
                
                return $booking;
            });

            return response()->json([
                'success' => true,
                'original_booking' => [
                    'booking_id' => $originalBooking->BookingID,
                    'booking_request_id' => $originalBooking->BookingRequestID,
                    'company_name' => $originalBooking->CompanyName,
                    'opportunity_name' => $originalBooking->OpportunityName,
                    'booking_type' => $originalBooking->BookingType,
                    'parent_invoice_id' => $originalBooking->parent_invoice_id,
                    'is_merged' => $originalBooking->is_merged
                ],
                'eligible_bookings' => $eligibleBookings
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getMergeBookingItems: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching merge items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function splitInvoice(Request $request)
    {
        try {
            DB::beginTransaction();

            $splitResults = [];
            foreach ($request->loads as $load) {
                // Validate load exists and get booking with its request
                $booking = Booking::with(['bookingRequest', 'loads'])
                    ->findOrFail($load['LoadID']);

                if (!$booking->bookingRequest) {
                    throw new \Exception('Booking request not found for booking ID: ' . $load['LoadID']);
                }

                // Initialize amounts
                $totalAmount = 0;
                $vatRate = 20.00; // Default VAT rate

                // Calculate amounts for this split
                $loads = $booking->loads;
                if ($loads) {
                    $totalAmount = $loads->sum(function($load) {
                        return ($load->LoadPrice ?? 0) * ($load->Loads ?? 1);
                    });
                }

                $vatAmount = $totalAmount * ($vatRate / 100);
                $finalAmount = $totalAmount + $vatAmount;

                // Create new invoice for split
                $splitInvoice = new BookingInvoice([
                    'BookingRequestID' => $booking->BookingRequestID,
                    'InvoiceDate' => now(),
                    'InvoiceType' => 0,
                    'InvoiceNumber' => $this->generateNextInvoiceNumber(),
                    'CompanyID' => $booking->bookingRequest->CompanyID,
                    'CompanyName' => $booking->bookingRequest->CompanyName,
                    'OpportunityID' => $booking->bookingRequest->OpportunityID,
                    'OpportunityName' => $booking->bookingRequest->OpportunityName,
                    'ContactID' => $booking->bookingRequest->ContactID,
                    'ContactName' => $booking->bookingRequest->ContactName,
                    'ContactMobile' => $booking->bookingRequest->ContactMobile,
                    'SubTotalAmount' => $totalAmount,
                    'VatAmount' => $vatAmount,
                    'FinalAmount' => $finalAmount,
                    'TaxRate' => $vatRate,
                    'Status' => 0,
                    'CreatedUserID' => auth()->id(),
                    'is_split' => 1,
                    'CreateDateTime' => now(),
                    'UpdateDateTime' => now()
                ]);

                $splitInvoice->save();

                // Create a new booking entry for the split
                $newBooking = new Booking();
                $newBooking->BookingRequestID = $booking->BookingRequestID;
                $newBooking->BookingType = $booking->BookingType;
                $newBooking->TipID = $booking->TipID ?? 0;
                $newBooking->MaterialID = $booking->MaterialID;
                $newBooking->MaterialName = $booking->MaterialName;
                $newBooking->SICCode = $booking->SICCode;
                $newBooking->PurchaseOrderNo = $booking->PurchaseOrderNo;
                $newBooking->DayWorkType = $booking->DayWorkType;
                $newBooking->TonBook = $booking->TonBook;
                $newBooking->TotalTon = $booking->TotalTon;
                $newBooking->TonPerLoad = $booking->TonPerLoad;
                $newBooking->LoadType = $booking->LoadType;
                $newBooking->LorryType = $booking->LorryType;
                $newBooking->Loads = $booking->Loads;
                $newBooking->Days = $booking->Days;
                $newBooking->Price = $booking->Price;
                $newBooking->PriceApproved = $booking->PriceApproved;
                $newBooking->PriceApprovedBy = $booking->PriceApprovedBy;
                $newBooking->TotalAmount = $booking->TotalAmount;
                $newBooking->Notes = $booking->Notes;
                $newBooking->BookedBy = $booking->BookedBy;
                $newBooking->UpdatedBy = auth()->id();
                $newBooking->parent_invoice_id = $splitInvoice->InvoiceId;
                $newBooking->is_merged = 0;
                $newBooking->merged_at = null;
                $newBooking->merged_from = null;
                $newBooking->merged_to = null;
                $newBooking->OpenPO = $booking->OpenPO ?? 0;
                $newBooking->CreateDateTime = now();
                $newBooking->UpdateDateTime = now();
                $newBooking->save();

                // Move selected loads to the new booking
                DB::table('tbl_booking_loads1')
                    ->where('BookingID', $booking->BookingID)
                    ->update(['BookingID' => $newBooking->BookingID]);

                // Create relationship record
                BookingRelationship::create([
                    'source_booking_id' => $booking->BookingID,
                    'target_booking_id' => $newBooking->BookingID,
                    'relationship_type' => 'split',
                    'metadata' => json_encode([
                        'original_invoice_id' => $booking->parent_invoice_id,
                        'split_reason' => $request->input('split_reason', 'Manual split'),
                        'load_details' => $load,
                        'total_amount' => $totalAmount,
                        'vat_amount' => $vatAmount,
                        'final_amount' => $finalAmount
                    ]),
                    'relationship_date' => now(),
                    'created_by' => auth()->id()
                ]);

                // Create audit log
                $this->createAuditLog($booking->BookingID, 'split', [
                    'original_state' => $booking->getOriginal(),
                    'split_invoice_id' => $splitInvoice->InvoiceId,
                    'new_booking_id' => $newBooking->BookingID,
                    'split_details' => [
                        'total_amount' => $totalAmount,
                        'vat_amount' => $vatAmount,
                        'final_amount' => $finalAmount
                    ]
                ]);

                $splitResults[] = [
                    'booking_id' => $newBooking->BookingID,
                    'new_invoice_id' => $splitInvoice->InvoiceId,
                    'invoice_number' => $splitInvoice->InvoiceNumber,
                    'total_amount' => $totalAmount,
                    'vat_amount' => $vatAmount,
                    'final_amount' => $finalAmount
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Split invoices created successfully',
                'results' => $splitResults
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in splitInvoice: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating split invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function mergeBooking(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate target booking exists
            $targetBooking = BookingRequest::with('booking')
                ->findOrFail($request->booking_request_id);

            if (!$targetBooking || !$targetBooking->booking) {
                throw new \Exception('Target booking not found');
            }

            $mergedBookings = [];
            foreach ($request->bookings as $bookingData) {
                $sourceBooking = BookingRequest::with(['booking', 'booking.loads'])
                    ->where('BookingRequestID', $bookingData['BookingID'])
                    ->first();

                if (!$sourceBooking || !$sourceBooking->booking) {
                    continue;
                }

                // Create relationship record
                BookingRelationship::create([
                    'source_booking_id' => $sourceBooking->booking->BookingID,
                    'target_booking_id' => $targetBooking->booking->BookingID,
                    'relationship_type' => 'merge',
                    'metadata' => json_encode([
                        'merge_reason' => $request->input('merge_reason'),
                        'original_booking_data' => $sourceBooking->toArray(),
                        'merge_date' => now()->toDateTimeString()
                    ]),
                    'relationship_date' => now(),
                    'created_by' => auth()->id()
                ]);

                // Update the loads to point to the target booking
                DB::table('tbl_booking_loads1')
                    ->where('BookingID', $sourceBooking->booking->BookingID)
                    ->update([
                        'BookingID' => $targetBooking->booking->BookingID
                    ]);

                // Update source booking
                $sourceBooking->booking->update([
                    'BookingRequestID' => $request->booking_request_id,
                    'is_merged' => true,
                    'merged_at' => now(),
                    'merged_from' => $sourceBooking->BookingRequestID,
                    'merged_to' => $request->booking_request_id
                ]);

                // Create audit log
                $this->createAuditLog($sourceBooking->booking->BookingID, 'merge', [
                    'original_state' => $sourceBooking->getOriginal(),
                    'merged_to_booking_id' => $targetBooking->booking->BookingID,
                    'merge_details' => [
                        'target_booking_id' => $targetBooking->booking->BookingID,
                        'merge_date' => now()->toDateTimeString(),
                        'loads_transferred' => true
                    ]
                ]);

                $mergedBookings[] = $sourceBooking->BookingRequestID;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bookings merged successfully',
                'merged_bookings' => $mergedBookings,
                'target_booking_id' => $request->booking_request_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in mergeBooking: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error merging bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirm(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request data
            $validated = $request->validate([
                'BookingRequestID' => 'required|integer',
                'CompanyID' => 'required|string',
                'CompanyName' => 'required|string',
                'OpportunityID' => 'required|string',
                'OpportunityName' => 'required|string',
                'ContactID' => 'required|string',
                'ContactName' => 'required|string',
                'ContactMobile' => 'required|string',
                'SubTotalAmount' => 'required|numeric|min:0',
                'VatAmount' => 'required|numeric|min:0',
                'FinalAmount' => 'required|numeric|min:0',
                'hold_invoice' => 'nullable|boolean',
                'comment' => 'nullable|string',
                'booking_loads' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $loads = json_decode($value, true);
                        if (!is_array($loads)) {
                            $fail('The booking loads data is invalid.');
                            return;
                        }
                        foreach ($loads as $load) {
                            if (!isset($load['BookingID'], $load['MaterialName'], 
                                     $load['LoadType'], $load['Loads'], 
                                     $load['Price'], $load['TotalAmount'])) {
                                $fail('Each load must contain all required fields.');
                                return;
                            }
                            if ($load['Price'] < 0 || $load['Loads'] <= 0) {
                                $fail('Price and quantity must be greater than or equal to zero for price and greater than zero for loads.');
                                return;
                            }
                        }
                    }
                ],
                'invoice_items' => 'required|string'
            ]);

            // Decode the JSON strings
            $bookingLoads = json_decode($request->booking_loads, true);
            $invoiceItems = json_decode($request->invoice_items, true);

            // Create a new invoice record
            $invoice = new ReadyInvoice();

            // Get the last invoice number and increment it
            $lastInvoice = ReadyInvoice::orderBy('InvoiceNumber', 'desc')->first();
            $nextInvoiceNumber = $lastInvoice ? intval($lastInvoice->InvoiceNumber) + 1 : 1;

            // Format invoice number with leading zeros (6 digits)
            $formattedInvoiceNumber = str_pad($nextInvoiceNumber, 6, '0', STR_PAD_LEFT);

            // Calculate totals from bookingLoads
            $subTotal = collect($bookingLoads)->sum(function($load) {
                return round($load['Price'] * $load['Loads'], 2);
            });
            $taxRate = isset($validated['TaxRate']) ? floatval($validated['TaxRate']) : 20;
            $vatAmount = round($subTotal * $taxRate / 100, 2);
            $finalAmount = $subTotal + $vatAmount;

            $invoice->fill([
                'BookingRequestID' => $validated['BookingRequestID'],
                'CompanyID' => $validated['CompanyID'],
                'InvoiceType' => 0,
                'CompanyName' => $validated['CompanyName'],
                'OpportunityID' => $validated['OpportunityID'],
                'OpportunityName' => $validated['OpportunityName'],
                'ContactID' => $validated['ContactID'],
                'ContactName' => $validated['ContactName'],
                'ContactMobile' => $validated['ContactMobile'],
                'SubTotalAmount' => $subTotal,
                'VatAmount' => $vatAmount,
                'FinalAmount' => $finalAmount,
                'TaxRate' => $taxRate,
                'CreatedUserID' => Auth::user()->userId,
                'Status' => $validated['hold_invoice'] ? 0 : 1, // 0 for hold, 1 for ready
                'Comment' => $validated['comment'],
                'InvoiceDate' => now(),
                'InvoiceNumber' => $formattedInvoiceNumber,
                'is_hold' => $validated['hold_invoice'] ? 1 : 0,
                'CreateDateTime' => now(),
                'UpdateDateTime' => now()
            ]);

            $invoice->save();

            // Store invoice items
            if (!empty($bookingLoads)) {
                $itemNumber = 1;
                foreach ($bookingLoads as $load) {
                    // Get MaterialCode from tbl_materials
                    $material = DB::table('tbl_materials')
                        ->where('MaterialName', $load['MaterialName'])
                        ->first();

                    $totalAmount = round($load['Price'] * $load['Loads'], 2);
                    $taxAmount = round($totalAmount * $taxRate / 100, 2);

                    $invoiceItem = new ReadyInvoiceItem();
                    $invoiceItem->fill([
                        'InvoiceID' => $invoice->id,
                        'InvoiceNumber' => $formattedInvoiceNumber,
                        'ItemNumber' => $itemNumber++,
                        'Qty' => $load['Loads'],
                        'UnitPrice' => $load['Price'],
                        'GrossAmount' => $totalAmount,
                        'TaxAmount' => $taxAmount,
                        'TaxRate' => $taxRate,
                        'NetAmount' => $totalAmount,
                        'NominalCode' => $material ? $material->MaterialCode : null,
                        'Description' => $load['MaterialName'],
                        'Comment1' => $load['LoadType'],
                        'BookingID' => $load['BookingID'],
                        'CreateDateTime' => now(),
                        'UpdateDateTime' => now()
                    ]);
                    $invoiceItem->save();
                }
            }

            // Update the booking request status
            $bookingRequest = BookingRequest::where('BookingRequestID', $validated['BookingRequestID'])->first();
            if ($bookingRequest) {
                $bookingRequest->update([
                    'InvoiceHold' => $validated['hold_invoice'] ? 0 : 1,
                    'invoiceID' => $invoice->id,
                    'UpdateDateTime' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice has been confirmed successfully',
                'redirect_url' => route('invoice.showData', ['invoice' => $invoice->id])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // First try to find the invoice
            $invoice = ReadyInvoice::with(['items' => function($query) {
                    $query->orderBy('ItemNumber', 'asc');
                }, 'booking.loads'])
                ->findOrFail($id);

            // Get the user who created the invoice
            $user = User::where('userId', $invoice->CreatedUserID)->first();

            // Process items to include load details
            foreach ($invoice->items as $item) {
                // Get loads for this material
                $loads = $invoice->booking->loads()
                    ->where('MaterialName', $item->Description)
                    ->get();

                if ($loads->isEmpty()) {
                    continue;
                }

                // Calculate totals for this material
                $totalLoads = $loads->sum('Loads');
                $totalAmount = $loads->sum(function($load) {
                    return ($load->LoadPrice ?? 0) * ($load->Loads ?? 1);
                });

                // Calculate average price per load
                $avgPricePerLoad = $totalLoads > 0 ? $totalAmount / $totalLoads : 0;

                // Update item with calculated values
                $item->Qty = $totalLoads;
                $item->UnitPrice = $avgPricePerLoad;
                $item->NetAmount = $totalAmount;
                $item->GrossAmount = $totalAmount;
                $item->Comment1 = 'Loads';
            }

            // Calculate totals
            $subtotal = $invoice->items->sum('NetAmount');
            $vatRate = $invoice->TaxRate ?? 20;
            $vatAmount = round($subtotal * ($vatRate / 100), 2);
            $finalAmount = $subtotal + $vatAmount;

            // Update invoice amounts
            $invoice->update([
                'SubTotalAmount' => $subtotal,
                'VatAmount' => $vatAmount,
                'FinalAmount' => $finalAmount,
                'UpdateDateTime' => now()
            ]);

            return view('admin.pages.invoice.viewinvoice', compact('invoice', 'user'));

        } catch (\Exception $e) {
            \Log::error('Error in show method: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Error loading invoice: ' . $e->getMessage());
        }
    }

    public function compareWithSage(Request $request)
    {
        try {
            // Get the invoice number from request
            $invoiceNumber = $request->invoiceNumber;

            if (!$invoiceNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice number is required'
                ], 400);
            }

            // Convert Sage format (1) to our format (000001)
            $formattedInvoiceNumber = str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT);

            // Get invoice from our system
            $ourInvoice = ReadyInvoice::where('InvoiceNumber', $formattedInvoiceNumber)
                ->with('items')
                ->first();

            if (!$ourInvoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found in our system',
                    'invoice_number' => $formattedInvoiceNumber
                ], 404);
            }

            // Get invoice from Sage using the getInvoiceItemsByReference method
            $sageResponse = app(SageController::class)->getInvoiceItemsByReference($invoiceNumber);
            $sageData = $sageResponse->getData();

            if (!$sageData->success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found in Sage: ' . ($sageData->error ?? 'Unknown error'),
                    'invoice_number' => $invoiceNumber
                ], 404);
            }

            // Compare the data
            $differences = [];

            // Compare basic invoice details
            $basicFields = [
                'SubTotalAmount' => 'netTotal',
                'VatAmount' => 'taxTotal',
                'FinalAmount' => 'grossTotal'
            ];

            foreach ($basicFields as $ourField => $sageField) {
                $ourValue = $ourInvoice->$ourField;
                $sageValue = $sageData->invoice->$sageField ?? 0;

                if (abs($ourValue - $sageValue) > 0.01) { // Using small tolerance for floating point comparison
                    $differences['basic_details'][$ourField] = [
                        'our_system' => $ourValue,
                        'sage' => $sageValue
                    ];
                }
            }

            // Compare invoice items
            $ourItems = $ourInvoice->items->pluck('Qty', 'Description')->toArray();
            $sageItemsMap = collect($sageData->items)->pluck('quantity', 'description')->toArray();

            // Find items that exist in one system but not the other
            $allDescriptions = array_unique(array_merge(array_keys($ourItems), array_keys($sageItemsMap)));

            foreach ($allDescriptions as $description) {
                $ourQty = $ourItems[$description] ?? 0;
                $sageQty = $sageItemsMap[$description] ?? 0;

                if (abs($ourQty - $sageQty) > 0.01) { // Using small tolerance for floating point comparison
                    $differences['items'][$description] = [
                        'our_system' => $ourQty,
                        'sage' => $sageQty
                    ];
                }
            }

            // Store differences in database if any found
            if (!empty($differences['basic_details']) || !empty($differences['items'])) {
                InvoiceDifference::updateOrCreate(
                    ['invoice_number' => $formattedInvoiceNumber],
                    [
                        'basic_details_differences' => $differences['basic_details'] ?? null,
                        'items_differences' => $differences['items'] ?? null,
                        'status' => 'pending'
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Comparison completed',
                'invoice_number' => $formattedInvoiceNumber,
                'differences' => $differences
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error comparing invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showDifferences()
    {
        // Get invoices with differences
        $differences = InvoiceDifference::orderBy('created_at', 'desc')->get();

        // Get perfect invoices (those not in the differences table)
        $perfectInvoices = ReadyInvoice::whereNotIn('InvoiceNumber', function ($query) {
            $query->select('invoice_number')->from('invoice_differences');
        })->orderBy('InvoiceDate', 'desc')->get();

        return view('admin.pages.invoice.differences', compact('differences', 'perfectInvoices'));
    }

    public function updateDifferenceStatus(Request $request, $id)
    {

        $request->validate([
            'status' => 'required|in:0,1',
            'resolution_notes' => 'nullable|string'
        ]);
        $invoice = Invoice::where('id', $id)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }
        $difference = InvoiceDifference::findOrFail($id);
        $difference->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes
        ]);

        $invoice->update([
            'isApproved' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Difference status updated successfully'
        ]);
    }

    public function getInvoiceByNumber(Request $request)
    {
        try {
            $invoiceNumber = $request->input('invoice_number');

            if (!$invoiceNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice number is required'
                ], 400);
            }

            // Format the invoice number to match our system's format (000001)
            $formattedInvoiceNumber = str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT);

            // Get the invoice with its items
            $invoice = ReadyInvoice::where('InvoiceNumber', $formattedInvoiceNumber)
                ->with('items')
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found',
                    'invoice_number' => $formattedInvoiceNumber
                ], 404);
            }

            // Format the response
            $response = [
                'success' => true,
                'invoice' => [
                    'InvoiceID' => $invoice->id,
                    'InvoiceNumber' => $invoice->InvoiceNumber,
                    'InvoiceDate' => $invoice->InvoiceDate,
                    'CompanyName' => $invoice->CompanyName,
                    'SubTotalAmount' => $invoice->SubTotalAmount,
                    'VatAmount' => $invoice->VatAmount,
                    'FinalAmount' => $invoice->FinalAmount,
                    'TaxRate' => $invoice->TaxRate,
                    'Status' => $invoice->Status,
                    'Comment' => $invoice->Comment,
                    'items' => $invoice->items->map(function ($item) {
                        return [
                            'ItemNumber' => $item->ItemNumber,
                            'Description' => $item->Description,
                            'Qty' => $item->Qty,
                            'UnitPrice' => $item->UnitPrice,
                            'GrossAmount' => $item->GrossAmount,
                            'TaxAmount' => $item->TaxAmount,
                            'NetAmount' => $item->NetAmount,
                            'NominalCode' => $item->NominalCode,
                            'Comment1' => $item->Comment1
                        ];
                    })
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteInvoiceLoad(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get the load details before deletion
            $load = DB::table('tbl_booking_loads1')
                ->where('LoadID', $request->load_id)
                ->first();

            if (!$load) {
                return response()->json([
                    'success' => false,
                    'message' => 'Load not found'
                ]);
            }
            // Store in deleted loads table
            DB::table('tbl_booking_loads_deleted')->insert([
                'LoadID' => $load->LoadID,
                'ConveyanceNo' => $request->conveyance_no,
                'LoadValues' => json_encode($load), // Store all load values as JSON
                'CreateDateTime' => now(),
            ]);

            // Delete the load
            DB::table('tbl_booking_loads1')
                ->where('LoadID', $request->load_id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Load deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting load: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create audit log entry
     */
    private function createAuditLog($bookingId, $action, $data)
    {
        DB::table('booking_audit_logs')->insert([
            'booking_id' => $bookingId,
            'action' => $action,
            'old_data' => json_encode($data['original_state'] ?? null),
            'new_data' => json_encode(array_diff_key($data, ['original_state' => 0])),
            'performed_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Generate next invoice number
     */
    private function generateNextInvoiceNumber()
    {
        $lastInvoice = BookingInvoice::orderBy('InvoiceNumber', 'desc')->first();
        $nextNumber = $lastInvoice ? intval($lastInvoice->InvoiceNumber) + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getMaterialName(Request $request)
    {
        try {
            $materialId = $request->input('material_id');
            
            if (!$materialId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material ID is required'
                ], 400);
            }

            $material = DB::table('tbl_materials')
                ->where('MaterialID', $materialId)
                ->first();

            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'material_name' => $material->MaterialName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching material name: ' . $e->getMessage()
            ], 500);
        }
    }
}
