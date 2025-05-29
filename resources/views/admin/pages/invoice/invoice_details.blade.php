@extends('layouts.main')

<style>
    .timeline {
        position: relative;
        padding: 20px;
        border-left: 3px solid #007bff;
        margin-left: 10px;
    }

    .timeline-item {
        position: relative;
        padding: 10px 0;
        margin-bottom: 10px;
    }

    .timeline-dot {
        position: absolute;
        left: -8px;
        top: 10px;
        width: 16px;
        height: 16px;
        background-color: #007bff;
        border-radius: 50%;
    }

    .timeline-content {
        margin-left: 20px;
    }

    .timeline-date {
        font-size: 12px;
        color: #6c757d;
    }

    .timeline-text {
        font-size: 14px;
        margin-top: 5px;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
    }
    .table th,
    .table td {
        padding: 12px;
        vertical-align: middle;
    }
    .table tfoot tr:last-child {
        border-top: 2px solid #dee2e6;
    }
    .table tfoot td {
        padding: 12px;
        border: none;
    }
    .table tfoot tr:last-child td {
        padding-top: 16px;
    }
    .text-muted {
        color: #6c757d !important;
    }
    .border-top {
        border-top: 2px solid #dee2e6 !important;
    }
    .invoice-subtotal,
    .invoice-vat,
    .invoice-total {
        white-space: nowrap;
        text-align: right;
        padding-right: 15px !important;
        font-size: 1.1em;
    }
    .table tfoot tr td:last-child {
        text-align: right;
        padding-right: 15px !important;
    }
    .table tfoot tr td span {
        display: inline-block;
    }

    /* Loading state styles */
    .loading-state {
        display: none;
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }

    .processing {
        position: relative;
        pointer-events: none;
    }

    .processing::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: 1000;
    }

    /* SweetAlert2 Custom Styles */
    .swal2-popup {
        border-radius: 15px !important;
        padding: 2rem !important;
    }

    .swal2-title-custom {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        margin-bottom: 1rem !important;
    }

    .swal2-content-custom {
        font-size: 1rem !important;
    }

    .swal2-header-custom {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem !important;
    }

    .delete-load-modal .alert {
        background-color: #fff8e1;
        border-left: 4px solid #ffa000;
        margin: 1rem 0;
    }

    .delete-load-modal .alert i {
        color: #ffa000;
    }

    /* Animation classes */
    .animated {
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }

    .faster {
        animation-duration: 0.3s;
    }

    @keyframes zoomIn {
        from {
            opacity: 0;
            transform: scale3d(0.3, 0.3, 0.3);
        }
        50% {
            opacity: 1;
        }
    }

    .zoomIn {
        animation-name: zoomIn;
    }

    @keyframes zoomOut {
        from {
            opacity: 1;
        }
        50% {
            opacity: 0;
            transform: scale3d(0.3, 0.3, 0.3);
        }
        to {
            opacity: 0;
        }
    }

    .zoomOut {
        animation-name: zoomOut;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -100%, 0);
        }
        to {
            opacity: 1;
            transform: none;
        }
    }

    .fadeInDown {
        animation-name: fadeInDown;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 100%, 0);
        }
        to {
            opacity: 1;
            transform: none;
        }
    }

    .fadeInUp {
        animation-name: fadeInUp;
    }

    /* SweetAlert2 Loading Spinner Custom Style */
    .swal2-loading {
        overflow: hidden;
    }

    .swal2-loading .swal2-loader {
        border-color: #3c8dbc transparent #3c8dbc transparent;
    }
</style>

<!-- Add Notiflix CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css">
<script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>

<!-- Add iziToast CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>

@section('content')
    <div class="container">
        <div class="booking-section p-4 bg-light rounded shadow-sm">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ url()->previous() ?? route('dashboard') }}" class="text-decoration-none text-primary">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>
                <h4 class="m-0 fw-bold">Booking No. : {{ $booking->BookingID }}</h4>
            </div>

            <div class="row g-4">
                <!-- Company Information -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="text-white mb-0"><i class="fas fa-building me-2"></i>Company Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Company Name:</strong> {{ $booking->bookingRequest->CompanyName }}
                            </div>
                            <div class="mb-3">
                                <strong>Opportunity Name:</strong> {{ $booking->bookingRequest->OpportunityName }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waiting Time and Charges -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="text-white mb-0"><i class="fas fa-clock me-2"></i>Booking Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Waiting Time:</strong> <span
                                    class="badge bg-light text-dark">{{ $booking->bookingRequest->WaitingTime }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Wait Charges:</strong> <span class="badge bg-light text-dark">
                                    {{ $booking->bookingRequest->WaitingCharge }} </span>
                            </div>
                            <div class="mb-3">
                                <strong>Purchase Order:</strong> <span class="badge bg-light text-dark">
                                    {{ $booking->PurchaseOrderNo }} </span>
                            </div>
                            <div class="mb-3">
                                <strong>Notes:</strong> <span class="text-muted fst-italic">Req: 8 AM Start - 8 AM Onwards -
                                    Booked by Ilir - 07740 862066</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="text-white mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Type Of Payment:</strong> <span class="badge bg-success">CARD</span>
                            </div>
                            <div class="mb-3">
                                <strong>Payment Ref Notes:</strong> <span class="text-muted">No notes available</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Loads List -->
            <div class="loads-section mt-4">
                <h5 class="text-white p-3 rounded-top mb-0" style="background: #3c8dbc">
                    <i class="fas fa-truck me-2"></i> Booking Loads List
                </h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover bg-white">
                        <thead class="table">
                            <tr>
                                <th width="5%">Expand</th>
                                <th>Requested Date</th>
                                <th>Type</th>
                                <th>Material</th>
                                <th>Load Type</th>
                                <th>Lorry Type</th>
                                <th width="8%">Total Loads</th>
                                <th width="10%" class="text-end">Price/Load</th>
                                <th width="10%" class="text-end">Total Price</th>
                                <th class="validation-status"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subtotal = 0;
                            @endphp
                            @foreach ($bookings as $booking)
                                @php
                                    // Check if this booking has any valid loads
                                    $validLoads = $booking->loads->filter(function($load) {
                                        return $load->Status != 5 && ($load->LoadPrice ?? 0) > 0;
                                    });

                                    // Calculate total amount from valid loads
                                    $totalAmount = $validLoads->sum(function($load) {
                                        return ($load->LoadPrice ?? 0) * ($load->Loads ?? 1);
                                    });

                                    // Check if this is a split booking
                                    $isSplitBooking = DB::table('booking_relationships')
                                        ->where(function($query) use ($booking) {
                                            $query->where('source_booking_id', $booking->BookingID)
                                                  ->orWhere('target_booking_id', $booking->BookingID);
                                        })
                                        ->where('relationship_type', 'split')
                                        ->exists();

                                    // Only skip if it's a source booking of a split
                                    if ($isSplitBooking && DB::table('booking_relationships')
                                        ->where('source_booking_id', $booking->BookingID)
                                        ->where('relationship_type', 'split')
                                        ->exists()) {
                                        continue;
                                    }

                                    $subtotal += $totalAmount;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary toggle-invoice"
                                            data-booking-id="{{ $booking->BookingID }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->RequestedDate)->format('d-m-Y H:i') }}</td>
                                    <td><span class="badge bg-primary">{{ $booking->BookingType }}</span></td>
                                    <td>{{ $booking->MaterialName }}</td>
                                    <td>
                                        @if ($booking->LoadType == '1')
                                            <span class="badge bg-success">Loads</span>
                                        @elseif ($booking->LoadType == '2')
                                            <span class="badge bg-warning">Turnaround</span>
                                        @else
                                            <span class="badge bg-info">Both</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->LorryType == '1')
                                            <span class="badge bg-primary">Tipper</span>
                                        @elseif ($booking->LorryType == '2')
                                            <span class="badge bg-info">Grab</span>
                                        @elseif ($booking->LorryType == '3')
                                            <span class="badge bg-success">Bin</span>
                                        @else
                                            <span class="badge bg-warning">NA</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $validLoads->count() }}</td>
                                    <td class="text-end price-per-load" data-price="{{ $booking->Price }}">
                                        £{{ number_format($booking->Price, 2) }}
                                    </td>
                                    <td class="text-end booking-total" data-total="{{ $totalAmount }}">
                                        £{{ number_format($totalAmount, 2) }}
                                    </td>
                                    <td class="validation-status">
                                        @if ($isSplitBooking)
                                            <span class="badge bg-info">Split</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="invoice-items-container d-none" data-booking-id="{{ $booking->BookingID }}">
                                    <td colspan="9" class="p-0">
                                        <div class="invoice-items-content p-3 bg-light">
                                            <!-- Fetched invoice items will be displayed here -->
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $vatRate = $invoice->TaxRate ?? 20;
                                $vatAmount = $subtotal * ($vatRate / 100);
                                $total = $subtotal + $vatAmount;
                            @endphp
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end text-muted">SubTotal</td>
                                <td class="text-end subtotal-amount">£{{ number_format($subtotal, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end text-muted">VAT ({{ $vatRate }}%)</td>
                                <td class="text-end vat-amount">£{{ number_format($vatAmount, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr class="border-top">
                                <td colspan="7"></td>
                                <td class="text-end text-muted">Total</td>
                                <td class="text-end total-amount fw-bold">£{{ number_format($total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


            <!-- Comment and Hold Invoice Section -->
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="holdInvoice">
                        <label class="form-check-label fw-bold text-danger" for="holdInvoice">
                            HOLD Invoice <span class="text-muted">(Check This Box to Put Invoice On HOLD)</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label fw-bold">Comment:</label>
                        <textarea class="form-control border-2" id="comment" rows="4"
                            placeholder="Please Enter Your Any Comment Here."></textarea>
                    </div>
                    <div class="text-end">
                        <form id="confirmInvoiceForm" action="{{ route('invoice.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="BookingRequestID" value="{{ $booking->BookingRequestID }}">
                            <input type="hidden" name="CompanyID"
                                value="{{ $booking->bookingRequest->CompanyID }}">
                            <input type="hidden" name="CompanyName"
                                value="{{ $booking->bookingRequest->CompanyName }}">
                            <input type="hidden" name="OpportunityID"
                                value="{{ $booking->bookingRequest->OpportunityID }}">
                            <input type="hidden" name="OpportunityName"
                                value="{{ $booking->bookingRequest->OpportunityName }}">
                            <input type="hidden" name="ContactID"
                                value="{{ $booking->bookingRequest->ContactID }}">
                            <input type="hidden" name="ContactName"
                                value="{{ $booking->bookingRequest->ContactName }}">
                            <input type="hidden" name="ContactMobile"
                                value="{{ $booking->bookingRequest->ContactMobile }}">
                            <input type="hidden" name="SubTotalAmount" value="{{ $invoice->SubTotalAmount }}">
                            <input type="hidden" name="VatAmount" value="{{ $invoice->VatAmount }}">
                            <input type="hidden" name="FinalAmount" value="{{ $invoice->TotalAmount }}">
                            <input type="hidden" name="TaxRate" value="20">
                            <input type="hidden" name="CreatedUserID" value="{{ auth()->id() }}">
                            <input type="hidden" name="hold_invoice" id="hold_invoice" value="0">
                            <input type="hidden" name="comment" id="comment_value">

                            <button type="button" class="btn btn-secondary btn-lg px-4 me-2" data-bs-toggle="modal"
                                data-bs-target="#mergeBookingModal" data-invoice-id="{{ $booking['BookingRequestID'] }}">
                                <i class="fas fa-random me-2"></i>Merge Booking
                            </button>

                            <button type="button" class="btn btn-secondary btn-lg px-4 me-2" data-bs-toggle="modal"
                                data-bs-target="#splitInvoiceModal" data-invoice-id="{{ $booking->BookingID }}">
                                <i class="fas fa-random me-2"></i>Split Invoice
                            </button>

                            <button type="submit" class="btn btn-primary btn-lg px-4" id="proceedButton">
                                <span class="normal-state">
                                    <i class="fas fa-check-circle me-2"></i> Proceed To Confirm
                                </span>
                                <span class="loading-state d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Invoice Modal -->
    <div class="modal fade" id="splitInvoiceModal" tabindex="-1" aria-labelledby="splitInvoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="splitInvoiceModalLabel">Split Invoice Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-primary">Loading invoice items...</p>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button id="submitSelectedLoads" class="btn btn-primary"
                        data-booking-request-id="{{ $booking->BookingRequestID }}">
                        <i class="fas fa-check me-2"></i>Confirm Split
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Merge Booking Modal -->
    <div class="modal fade" id="mergeBookingModal" tabindex="-1" aria-labelledby="splitInvoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="splitInvoiceModalLabel">Merge Bookings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-primary">Loading bookings...</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="submitSelectedbooking" class="btn btn-primary"
                        data-booking-request-id="{{$booking['BookingRequestID']}}">Confirm Merge</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="priceHistoryModal" tabindex="-1" aria-labelledby="priceHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="priceHistoryModalLabel">Price Change History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Price history will be loaded here -->
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            
            // Initialize iziToast settings
            iziToast.settings({
                timeout: 5000,
                resetOnHover: true,
                transitionIn: 'flipInX',
                transitionOut: 'flipOutX',
                position: 'topRight',
                messageSize: '14px',
                titleSize: '16px',
                maxWidth: 400
            });

            // Helper function to parse currency values
            function parseCurrencyValue(value) {
                return parseFloat(value.replace(/[£,]/g, '')) || 0;
            }

            // Helper function to format currency
            function formatCurrency(value) {
                return '£' + parseFloat(value).toFixed(2);
            }

            // Handle form submission with loading state
            $('#confirmInvoiceForm').on('submit', function(e) {
                e.preventDefault();

                // Get the proceed button and form
                const $form = $(this);
                const $button = $('#proceedButton');
                const $normalState = $button.find('.normal-state');
                const $loadingState = $button.find('.loading-state');

                // Validate totals before submission
                let hasError = false;
                let errorMessage = '';

                // Show loading state
                $button.prop('disabled', true);
                $normalState.addClass('d-none');
                $loadingState.removeClass('d-none');
                $form.addClass('processing');

                // Get all form data
                var formData = $(this).serialize();

                // Collect all booking loads data with proper price handling
                var bookingLoads = [];
                $('.table-striped tbody tr').each(function() {
                    var $row = $(this);
                    var bookingId = $row.find('button[data-booking-id]').data('booking-id');
                    
                    if (bookingId) {
                        // Clean and parse numeric values properly
                        var price = parseCurrencyValue($row.find('td:eq(7)').text());
                        var loads = parseInt($row.find('td:eq(6)').text().trim()) || 0;
                        var totalAmount = price * loads;

                        // Validate the values
                        if (loads <= 0) {
                            hasError = true;
                            errorMessage = 'Number of loads must be greater than zero';
                            return false;
                        }

                        if (price < 0) {
                            hasError = true;
                            errorMessage = 'Price cannot be negative';
                            return false;
                        }

                        var bookingData = {
                            BookingID: bookingId,
                            MaterialName: $row.find('td:eq(3)').text().trim(),
                            LoadType: $row.find('td:eq(4)').text().trim(),
                            Loads: loads,
                            Price: price,
                            TotalAmount: totalAmount
                        };
                        bookingLoads.push(bookingData);
                    }
                });

                if (hasError) {
                    // Reset loading state
                    $button.prop('disabled', false);
                    $normalState.removeClass('d-none');
                    $loadingState.addClass('d-none');
                    $form.removeClass('processing');
                    
                    alert(errorMessage);
                    return;
                }

                // Calculate totals
                var subtotal = bookingLoads.reduce((sum, load) => sum + load.TotalAmount, 0);
                var vatRate = parseFloat("{{ $invoice->TaxRate ?? 20 }}");
                var vatAmount = (subtotal * vatRate) / 100;
                var finalAmount = subtotal + vatAmount;

                // Update hidden fields with proper numeric values
                $('input[name="SubTotalAmount"]').val(subtotal.toFixed(2));
                $('input[name="VatAmount"]').val(vatAmount.toFixed(2));
                $('input[name="FinalAmount"]').val(finalAmount.toFixed(2));

                // Add booking loads to form data
                formData += '&booking_loads=' + encodeURIComponent(JSON.stringify(bookingLoads));

                // Collect all invoice items data
                var invoiceItems = [];
                $('.editable-price').each(function() {
                    var $input = $(this);
                    var ticketId = $input.data('ticket-id');
                    var price = parseCurrencyValue($input.val());
                    
                    if (price < 0) {
                        hasError = true;
                        errorMessage = 'Individual load prices cannot be negative';
                        return false;
                    }

                    invoiceItems.push({
                        ticket_id: ticketId,
                        price: price
                    });
                });

                if (hasError) {
                    // Reset loading state
                    $button.prop('disabled', false);
                    $normalState.removeClass('d-none');
                    $loadingState.addClass('d-none');
                    $form.removeClass('processing');
                    
                    alert(errorMessage);
                    return;
                }

                // Add invoice items to form data
                formData += '&invoice_items=' + encodeURIComponent(JSON.stringify(invoiceItems));

                // Submit form via AJAX
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect_url;
                        } else {
                            // Reset loading state
                            $button.prop('disabled', false);
                            $normalState.removeClass('d-none');
                            $loadingState.addClass('d-none');
                            $form.removeClass('processing');

                            // Show error
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        // Reset loading state
                        $button.prop('disabled', false);
                        $normalState.removeClass('d-none');
                        $loadingState.addClass('d-none');
                        $form.removeClass('processing');

                        // Show error
                        alert('An error occurred while processing your request: ' + 
                              (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                    }
                });
            });

            // Handle hold invoice checkbox
            $('#holdInvoice').on('change', function() {
                $('#hold_invoice').val(this.checked ? '1' : '0');
            });

            // Handle comment textarea
            $('#comment').on('change', function() {
                $('#comment_value').val($(this).val());
            });

            // Toggle invoice details
            $('.toggle-invoice').on('click', function() {
                let bookingId = $(this).data('booking-id');
                let container = $('.invoice-items-container[data-booking-id="' + bookingId + '"]');
                let content = container.find('.invoice-items-content');

                if (container.hasClass('d-none')) {
                    // Show loader
                    content.html(
                        '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-primary">Loading booking details...</p></div>'
                    );
                    container.removeClass('d-none').slideDown(300);

                    $.ajax({
                        url: "{{ route('get.invoice.items') }}",
                        type: "GET",
                        data: { booking_id: bookingId },
                        success: function(response) {
                            if (!response.invoice_items || response.invoice_items.length === 0) {
                                content.html(
                                    '<div class="text-center py-4 text-danger fw-bold">No details found for this booking.</div>'
                                );
                                return;
                            }

                            let html = `<div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Action</th>
                                            <th>Conveyance No</th>
                                            <th>Ticket ID</th>
                                            <th>Date Time</th>
                                            <th>Driver Name</th>
                                            <th>Vehicle Reg</th>
                                            <th>Gross (kg)</th>
                                            <th>Tare (kg)</th>
                                            <th>Net (kg)</th>
                                            <th>Material</th>
                                            <th>Wait Time</th>
                                            <th>Status</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                            response.invoice_items.forEach(item => {
                                if (!item.loads || item.loads.length === 0) {
                                    html += `<tr>
                                        <td colspan="13" class="text-center text-warning fw-bold">No details available for this material.</td>
                                    </tr>`;
                                } else {
                                    // Add individual load rows
                                    item.loads.forEach(load => {
                                        let waitTime = calculateWaitTime(load.SiteInDateTime, load.SiteOutDateTime);
                                        let statusText = getStatusText(load.Status);
                                        let statusClass = getStatusClass(load.Status);

                                        html += `<tr>
                                            <td>
                                                <button class="btn btn-sm btn-danger delete-load" 
                                                    data-load-id="${load.LoadID}"
                                                    data-conveyance="${load.ConveyanceNo || ''}"
                                                    data-ticket="${load.TicketID || ''}"
                                                    data-material="${item.MaterialName}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <td>${load.ConveyanceNo || ''}</td>
                                            <td><span class="badge bg-dark">${load.TicketID || ''}</span></td>
                                            <td>${formatDateTime(load.JobStartDateTime)}</td>
                                            <td>${load.DriverName || ''}</td>
                                            <td>${load.VehicleRegNo || ''}</td>
                                            <td class="text-end">${formatWeight(load.GrossWeight)}</td>
                                            <td class="text-end">${formatWeight(load.Tare)}</td>
                                            <td class="text-end fw-bold">${formatWeight(load.Net)}</td>
                                            <td>${item.MaterialName}</td>
                                            <td>${waitTime}</td>
                                            <td><span class="badge ${statusClass}">${statusText}</span></td>
                                            <td class="text-end fw-bold">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <input type="text" class="form-control editable-price me-2"
                                                        style="width: 100px;"
                                                        data-ticket-id="${load.LoadID}"
                                                        data-old-price="${load.LoadPrice || 0}"
                                                        data-loads="${load.Loads || 1}"
                                                        value="${load.LoadPrice || 0}">
                                                    <button class="btn btn-sm btn-outline-primary price-history-btn"
                                                            data-ticket-id="${load.LoadID}"
                                                            title="View Price History">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>`;
                                    });
                                }
                            });

                            // Add delete load handler
                            $(document).on('click', '.delete-load', function(e) {
                                e.preventDefault();
                                const loadId = $(this).data('load-id');
                                const conveyance = $(this).data('conveyance');
                                const ticket = $(this).data('ticket');
                                const material = $(this).data('material');
                                const $row = $(this).closest('tr');
                                const $mainRow = $row.closest('.invoice-items-container').prev('tr');
                                const loadPrice = parseFloat($row.find('.editable-price').val()) || 0;
                                const loadQuantity = parseInt($row.find('.load-quantity').text()) || 1;
                                const currentTotal = parseFloat($mainRow.find('.booking-total').data('total')) || 0;

                                // Configure SweetAlert2
                                const swalWithBootstrapButtons = Swal.mixin({
                                    customClass: {
                                        confirmButton: 'btn btn-danger ms-2',
                                        cancelButton: 'btn btn-secondary'
                                    },
                                    buttonsStyling: false
                                });

                                swalWithBootstrapButtons.fire({
                                    title: 'Delete Load?',
                                    html: `
                                        <div class="text-start">
                                            <div class="alert alert-warning">
                                                <p class="mb-2"><i class="fas fa-truck me-2"></i><strong>Conveyance:</strong> ${conveyance || 'N/A'}</p>
                                                
                                            </div>
                                            <p class="text-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone!</p>
                                        </div>
                                    `,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes, delete it!',
                                    cancelButtonText: 'Cancel',
                                    reverseButtons: true,
                                    allowOutsideClick: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Show loading state
                                        swalWithBootstrapButtons.fire({
                                            title: 'Deleting...',
                                            html: 'Please wait while we process your request.',
                                            allowOutsideClick: false,
                                            showConfirmButton: false,
                                            willOpen: () => {
                                                Swal.showLoading();
                                            }
                                        });

                                        // Proceed with delete
                                        $.ajax({
                                            url: "{{ route('delete.invoice.load') }}",
                                            type: "POST",
                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                load_id: loadId,
                                                conveyance_no: conveyance
                                            },
                                            success: function(response) {
                                                if (response.success) {
                                                    // Calculate the amount to subtract
                                                    const amountToSubtract = loadPrice * loadQuantity;
                                                    
                                                    // Remove the row with animation
                                                    $row.fadeOut(300, function() {
                                                        $(this).remove();
                                                        
                                                        // Update booking total
                                                        const newTotal = Math.max(0, currentTotal - amountToSubtract);
                                                        $mainRow.find('.booking-total')
                                                            .data('total', newTotal)
                                                            .text('£' + newTotal.toFixed(2));
                                                        
                                                        // Recalculate all totals
                                                        recalculateBookingTotals();
                                                    });

                                                    swalWithBootstrapButtons.fire({
                                                        icon: 'success',
                                                        title: 'Deleted!',
                                                        text: 'Load has been deleted successfully.',
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    });
                                                } else {
                                                    swalWithBootstrapButtons.fire({
                                                        icon: 'error',
                                                        title: 'Error!',
                                                        text: response.message || 'Error deleting load'
                                                    });
                                                }
                                            },
                                            error: function(xhr) {
                                                swalWithBootstrapButtons.fire({
                                                    icon: 'error',
                                                    title: 'Error!',
                                                    text: xhr.responseJSON?.message || 'An error occurred while deleting the load'
                                                });
                                            }
                                        });
                                    }
                                });
                            });

                            html += '</tbody></table></div>';

                            // Animate content replacement
                            setTimeout(() => {
                                content.fadeOut(200, function() {
                                    $(this).html(html).fadeIn(300);
                                });
                            }, 500);
                        },
                        error: function() {
                            content.html(
                                '<div class="text-center py-4 text-danger fw-bold">Error loading details. Please try again.</div>'
                            );
                        }
                    });
                } else {
                    // Hide content
                    container.slideUp(300, function() {
                        $(this).addClass('d-none').css('display', '');
                    });
                }

                // Toggle expand icon
                $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
            });

            // Handle price history button click
            $(document).on('click', '.price-history-btn', function() {
                let ticketId = $(this).data('ticket-id');
                let $row = $(this).closest('tr');
                let conveyanceNo = $row.find('td:eq(1)').text().trim();
                
                $.ajax({
                    url: "{{ route('get.price.history') }}",
                    type: "GET",
                    data: { 
                        ticket_id: ticketId,
                        conveyance_no: conveyanceNo
                    },
                    success: function(response) {
                        if (!response.history || response.history.length === 0) {
                            $('#priceHistoryModal .modal-body').html(
                                '<div class="text-center text-danger">No price history found.</div>'
                            );
                            $('#priceHistoryModal').modal('show');
                            return;
                        }

                        let historyHtml = `<div class="timeline">`;
                        response.history.forEach(record => {
                            historyHtml += `
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <span class="timeline-date">${formatDateTime(record.ChangedAt)}</span>
                                        <div class="timeline-text">
                                            <strong>Changed By:</strong> <span class="text-primary">${record.ChangedByName}</span><br>
                                            <strong>Price Changed:</strong>
                                            <span class="text-danger">£${record.OldPrice}</span> →
                                            <span class="text-success">£${record.NewPrice}</span>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        historyHtml += `</div>`;

                        $('#priceHistoryModal .modal-body').html(historyHtml);
                        $('#priceHistoryModal').modal('show');
                    }
                });
            });

            // Helper functions
            function calculateWaitTime(siteIn, siteOut) {
                if (!siteIn || !siteOut) return "N/A";
                let inTime = new Date(siteIn);
                let outTime = new Date(siteOut);
                let diffMinutes = Math.round((outTime - inTime) / 60000);
                return diffMinutes > 60 ? `${Math.floor(diffMinutes / 60)}h ${diffMinutes % 60}m` : `${diffMinutes} min`;
            }

            function getStatusText(status) {
                const statusMap = {
                    0: "Allocated",
                    1: "Accepted",
                    2: "Arrive Site",
                    3: "Receipt Generated",
                    4: "Finish Load",
                    5: "Cancel Load"
                };
                return statusMap[status] || "Unknown";
            }

            function getStatusClass(status) {
                const classMap = {
                    0: "bg-secondary",
                    1: "bg-info",
                    2: "bg-warning",
                    3: "bg-primary",
                    4: "bg-success",
                    5: "bg-danger"
                };
                return classMap[status] || "bg-secondary";
            }

            function formatDateTime(dateTime) {
                if (!dateTime) return "N/A";
                try {
                    let date = new Date(dateTime);
                    if (isNaN(date.getTime())) return "Invalid Date";
                    return date.toLocaleString('en-GB', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }).replace(',', '');
                } catch (e) {
                    return "Invalid Date";
                }
            }

            function formatWeight(weight) {
                return weight ? parseFloat(weight).toLocaleString('en-GB') : "0";
            }

            // Split Invoice Modal Handler
            $('#splitInvoiceModal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var invoiceId = button.data('invoice-id');
                var modal = $(this);

                // Clear previous content and show loading
                modal.find('.modal-body').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-primary">Loading invoice items...</p>
                    </div>
                `);

                // Fetch split invoice items
                $.ajax({
                    url: "{{ route('get.splitinvoice.items') }}",
                    type: "GET",
                    data: { invoice_id: invoiceId },
                    success: function(response) {
                        if (!response.invoice_items || response.invoice_items.length === 0) {
                            modal.find('.modal-body').html('<div class="alert alert-warning">No items found to split.</div>');
                            return;
                        }

                        let html = `
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">Select</th>
                                            <th width="10%">Booking ID</th>
                                            <th width="12%">Date</th>
                                            <th width="10%">Material</th>
                                            <th width="8%">Quantity</th>
                                            <th width="10%">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        response.invoice_items.forEach(function(item) {
                            html += `
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="load-checkbox" 
                                            data-load-id="${item.BookingID}"
                                            data-material="${item.MaterialName}"
                                            data-price="${item.Price}">
                                    </td>
                                    <td><strong>${item.BookingID}</strong></td>
                                    <td>${formatDateTime(item.CreateDateTime)}</td>
                                    <td>${item.MaterialName}</td>
                                    <td class="text-center">${item.Loads}</td>
                                    <td class="text-end">£${item.Price}</td>
                                </tr>`;
                        });

                        html += `</tbody></table></div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Select the bookings you want to split into a new invoice.
                        </div>`;

                        modal.find('.modal-body').html(html);
                    },
                    error: function(xhr) {
                        modal.find('.modal-body').html(
                            '<div class="alert alert-danger">Error loading invoice items. Please try again.</div>'
                        );
                    }
                });
            });

            // Helper function for Load Type Label
            function getLoadTypeLabel(type) {
                switch(type) {
                    case '1':
                        return '<span class="badge bg-success">Loads</span>';
                    case '2':
                        return '<span class="badge bg-warning">Turnaround</span>';
                    default:
                        return '<span class="badge bg-info">Both</span>';
                }
            }

            // Helper function for Lorry Type Label
            function getLorryTypeLabel(type) {
                switch(type) {
                    case '1':
                        return '<span class="badge bg-primary">Tipper</span>';
                    case '2':
                        return '<span class="badge bg-info">Grab</span>';
                    case '3':
                        return '<span class="badge bg-success">Bin</span>';
                    default:
                        return '<span class="badge bg-warning">NA</span>';
                }
            }

            // Handle Split Invoice Submit
            $('#submitSelectedLoads').on('click', function() {
                var selectedLoads = [];
                $('.load-checkbox:checked').each(function() {
                    selectedLoads.push({
                        LoadID: $(this).data('load-id'),
                        MaterialName: $(this).data('material'),
                        Price: $(this).data('price')
                    });
                });

                if (selectedLoads.length === 0) {
                    alert('Please select at least one load to split.');
                    return;
                }

                $.ajax({
                    url: "{{ route('split.invoice') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        loads: selectedLoads
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Invoice split successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error splitting invoice. Please try again.');
                    }
                });
            });

            // Merge Booking Modal Handler
            $('#mergeBookingModal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var invoiceId = button.data('invoice-id');
                var modal = $(this);

                console.log('Opening merge modal with invoice ID:', invoiceId);

                // Clear previous content and show loading
                modal.find('.modal-body').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-primary">Loading bookings...</p>
                    </div>
                `);

                // Fetch merge booking items
                $.ajax({
                    url: "{{ route('get.mergebookings.items') }}",
                    type: "GET",
                    data: { invoice_id: invoiceId },
                    success: function(response) {
                        console.log('Merge bookings response:', response);

                        if (!response.eligible_bookings || response.eligible_bookings.length === 0) {
                            modal.find('.modal-body').html('<div class="alert alert-warning">No bookings found to merge.</div>');
                            return;
                        }

                        let html = `
                            <div class="alert alert-info mb-3">
                                <strong>Original Booking:</strong><br>
                                Booking ID: ${response.original_booking.booking_id}<br>
                                Company: ${response.original_booking.company_name}<br>
                                Project: ${response.original_booking.opportunity_name}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="5%">Select</th>
                                            <th width="10%">Booking ID</th>
                                            <th width="15%">Date</th>
                                            <th width="10%">Total Loads</th>
                                            <th width="15%">Total Amount</th>
                                            <th width="45%">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        response.eligible_bookings.forEach(function(booking) {
                            console.log('Processing booking:', booking);
                            
                            const loads = booking.loads || [];
                            const totalLoads = booking.total_loads || 0;
                            const totalAmount = booking.total_amount || 0;

                            html += `
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="booking-checkbox form-check-input"
                                               data-booking-id="${booking.booking_request_id}">
                                    </td>
                                    <td>
                                        <strong>${booking.booking_id}</strong>
                                        ${booking.parent_invoice_id ? 
                                            `<br><small class="text-info"><i class="fas fa-code-branch"></i> Split Booking</small>` : ''}
                                        ${booking.is_merged ? 
                                            `<br><small class="text-warning"><i class="fas fa-link"></i> Merged</small>` : ''}
                                    </td>
                                    <td>${formatDateTime(booking.CreateDateTime) || 'N/A'}</td>
                                    <td class="text-center">${totalLoads}</td>
                                    <td class="text-end">£${formatNumber(totalAmount)}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info view-loads-btn"
                                                data-booking-id="${booking.booking_id}">
                                            <i class="fas fa-truck me-1"></i> View Loads
                                        </button>
                                        ${booking.split_info ? `
                                            <div class="mt-2 small text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                ${booking.split_info.source_booking_id === booking.booking_id ? 
                                                    `Split to: ${booking.split_info.target_booking_id}` : 
                                                    `Split from: ${booking.split_info.source_booking_id}`}
                                            </div>
                                        ` : ''}
                                    </td>
                                </tr>
                                <tr class="loads-details d-none" id="loads-${booking.booking_id}">
                                    <td colspan="6">
                                        <div class="loads-content p-3 bg-light">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>Load ID</th>
                                                        <th>Material</th>
                                                        <th>Conveyance</th>
                                                        <th>Driver</th>
                                                        <th>Vehicle</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>`;
                                                
                            if (loads && loads.length > 0) {
                                loads.forEach(load => {
                                    html += `
                                        <tr>
                                            <td>${load.load_id}</td>
                                            <td>${load.material_name}</td>
                                            <td>${load.conveyance_no || 'N/A'}</td>
                                            <td>${load.driver_name || 'N/A'}</td>
                                            <td>${load.vehicle_reg_no || 'N/A'}</td>
                                            <td class="text-end">£${formatNumber(load.price)}</td>
                                        </tr>`;
                                });
                            } else {
                                html += `<tr><td colspan="6" class="text-center">No load details available</td></tr>`;
                            }

                            html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>`;
                        });

                        html += `</tbody></table></div>`;
                        
                        console.log('Generated HTML:', html);
                        modal.find('.modal-body').html(html);

                        // Add click handler for view loads button
                        $('.view-loads-btn').on('click', function() {
                            const bookingId = $(this).data('booking-id');
                            $(`#loads-${bookingId}`).toggleClass('d-none');
                            $(this).find('i').toggleClass('fa-truck fa-chevron-up');
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading merge bookings:', xhr);
                        modal.find('.modal-body').html(
                            '<div class="alert alert-danger">Error loading bookings. Please try again.</div>'
                        );
                    }
                });
            });

            // Helper function to format numbers
            function formatNumber(num) {
                return parseFloat(num).toFixed(2);
            }

            // Handle Merge Booking Submit
            $('#submitSelectedbooking').on('click', function() {
                var selectedBookings = [];
                $('.booking-checkbox:checked').each(function() {
                    selectedBookings.push({
                        BookingID: $(this).data('booking-id')
                    });
                });

                if (selectedBookings.length === 0) {
                    alert('Please select at least one booking to merge.');
                    return;
                }

                var bookingRequestId = $(this).data('booking-request-id');

                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Merging...');

                $.ajax({
                    url: "{{ route('merge.booking') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        booking_request_id: bookingRequestId,
                        bookings: selectedBookings
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Bookings merged successfully',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Error merging bookings'
                            });
                            // Reset button state
                            $button.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Error merging bookings'
                        });
                        // Reset button state
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Handle price updates
            $(document).on('change', '.editable-price', function() {
                let $input = $(this);
                let ticketId = $input.data('ticket-id');
                let oldPrice = parseFloat($input.data('old-price')) || 0;
                let newPrice = parseFloat($input.val()) || 0;
                let $row = $input.closest('tr');
                let conveyanceNo = $row.find('td:eq(1)').text().trim();
                let $mainRow = $input.closest('.invoice-items-container').prev('tr');
                
                // Add validation for empty conveyance
                if (!conveyanceNo) {
                    toastr.error('Conveyance number not found');
                    $input.val(oldPrice);
                    return;
                }

                $.ajax({
                    url: "{{ route('update.invoice.price') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ticket_id: ticketId,
                        conveyance_no: conveyanceNo,
                        old_price: oldPrice,
                        new_price: newPrice
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the data-old-price attribute with new price
                            $input.data('old-price', newPrice);
                            
                            // Calculate new total for this booking
                            let $container = $input.closest('.invoice-items-content');
                            let totalAmount = 0;
                            
                            // Sum up all prices in the container
                            $container.find('.editable-price').each(function() {
                                let price = parseFloat($(this).val()) || 0;
                                totalAmount += price;
                            });
                            
                            // Keep the original price per load
                            let pricePerLoad = parseFloat($mainRow.find('.price-per-load').data('price')) || 0;
                            
                            // Update only the total amount in the main row
                            $mainRow.find('.booking-total')
                                .data('total', totalAmount)
                                .text('£' + totalAmount.toFixed(2));
                            
                            // Recalculate all totals
                            recalculateBookingTotals();
                            
                            toastr.success('Price updated successfully');
                        } else {
                            toastr.error('Error updating price');
                            $input.val(oldPrice);
                        }
                    },
                    error: function() {
                        toastr.error('Error updating price');
                        $input.val(oldPrice);
                    }
                });
            });

            // Function to recalculate booking totals
            function recalculateBookingTotals() {
                let subtotal = 0;
                
                // Calculate new totals for each booking
                $('.booking-total').each(function() {
                    const total = parseFloat($(this).data('total')) || 0;
                    subtotal += total;
                });
                
                // Update subtotal display
                $('.subtotal-amount').text('£' + subtotal.toFixed(2));
                
                // Calculate VAT
                const vatRate = parseFloat("{{ $invoice->TaxRate ?? 20 }}");
                const vatAmount = (subtotal * vatRate) / 100;
                $('.vat-amount').text('£' + vatAmount.toFixed(2));
                
                // Update total
                const finalAmount = subtotal + vatAmount;
                $('.total-amount').text('£' + finalAmount.toFixed(2));
                
                // Update hidden form fields
                $('input[name="SubTotalAmount"]').val(subtotal.toFixed(2));
                $('input[name="VatAmount"]').val(vatAmount.toFixed(2));
                $('input[name="FinalAmount"]').val(finalAmount.toFixed(2));
            }

            // Add input validation for prices
            $(document).on('input', '.editable-price', function() {
                let value = $(this).val();
                // Remove any non-numeric characters except decimal point
                value = value.replace(/[^\d.]/g, '');
                // Ensure only one decimal point
                let parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }
                // Limit to 2 decimal places
                if (parts.length > 1) {
                    value = parts[0] + '.' + parts[1].slice(0, 2);
                }
                $(this).val(value);
            });
        });
    </script>
@endsection
