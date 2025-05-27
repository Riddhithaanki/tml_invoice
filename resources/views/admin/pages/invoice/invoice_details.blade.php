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
</style>

@section('content')
    <div class="container">
        <div class="booking-section p-4 bg-light rounded shadow-sm">
            <div class="d-flex align-items-center mb-4">
                <a href="#" class="text-decoration-none text-primary">
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
                                    $totalAmount = $booking->loads->sum(function($load) {
                                        return $load->LoadPrice * $load->Loads;
                                    });
                                    $subtotal += $totalAmount;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary toggle-invoice"
                                            data-booking-id="{{ $booking->BookingID }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->CreateDateTime)->format('d/m/Y H:i') }}</td>
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
                                    <td class="text-center">{{ $booking->Loads }}</td>
                                    <td class="text-end price-per-load" data-price="{{ $booking->Price }}">
                                        £{{ number_format($booking->Price, 2) }}
                                    </td>
                                    <td class="text-end booking-total" data-total="{{ $booking->TotalAmount }}">
                                        £{{ number_format($booking->TotalAmount, 2) }}
                                    </td>
                                    <td class="validation-status"></td>
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
                                $subtotal = $bookings->sum('TotalAmount');
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
                                        <td colspan="12" class="text-center text-warning fw-bold">No details available for this material.</td>
                                    </tr>`;
                                } else {
                                    // Add individual load rows
                                    item.loads.forEach(load => {
                                        let waitTime = calculateWaitTime(load.SiteInDateTime, load.SiteOutDateTime);
                                        let statusText = getStatusText(load.Status);
                                        let statusClass = getStatusClass(load.Status);

                                        html += `<tr>
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
                let conveyanceNo = $row.find('td:first').text().trim();
                
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
                let date = new Date(dateTime);
                return date.toLocaleString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
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
                                            <th>Select</th>
                                            <th>Load ID</th>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        response.invoice_items.forEach(function(item) {
                            html += `
                                <tr>
                                    <td>
                                        <input type="checkbox" class="load-checkbox" data-load-id="${item.BookingID}"
                                               data-material="${item.MaterialName}" data-price="${item.Price}">
                                    </td>
                                    <td>${item.BookingID}</td>
                                    <td>${item.MaterialName}</td>
                                    <td>${item.Loads}</td>
                                    <td>£${item.Price}</td>
                                </tr>`;
                        });

                        html += `</tbody></table></div>`;
                        modal.find('.modal-body').html(html);
                    },
                    error: function(xhr) {
                        modal.find('.modal-body').html(
                            '<div class="alert alert-danger">Error loading invoice items. Please try again.</div>'
                        );
                    }
                });
            });

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
                        if (!response.invoice_items || response.invoice_items.length === 0) {
                            modal.find('.modal-body').html('<div class="alert alert-warning">No bookings found to merge.</div>');
                            return;
                        }

                        let html = `
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Booking ID</th>
                                            <th>Company</th>
                                            <th>Opportunity</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        response.invoice_items.forEach(function(item) {
                            html += `
                                <tr>
                                    <td>
                                        <input type="checkbox" class="booking-checkbox"
                                               data-booking-id="${item.BookingRequestID}">
                                    </td>
                                    <td>${item.BookingRequestID}</td>
                                    <td>${item.CompanyName}</td>
                                    <td>${item.OpportunityName}</td>
                                    <td>${new Date(item.CreateDateTime).toLocaleDateString()}</td>
                                </tr>`;
                        });

                        html += `</tbody></table></div>`;
                        modal.find('.modal-body').html(html);
                    },
                    error: function(xhr) {
                        modal.find('.modal-body').html(
                            '<div class="alert alert-danger">Error loading bookings. Please try again.</div>'
                        );
                    }
                });
            });

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
                            alert('Bookings merged successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error merging bookings. Please try again.');
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
                let conveyanceNo = $row.find('td:first').text().trim();
                let $mainRow = $input.closest('.invoice-items-container').prev('tr');
                
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
                            
                            // Update the main row's price and total
                            let totalLoads = parseInt($mainRow.find('td:eq(6)').text()) || 0;
                            let avgPrice = totalAmount / totalLoads;
                            
                            $mainRow.find('.price-per-load')
                                .data('price', avgPrice)
                                .text('£' + avgPrice.toFixed(2));
                                
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

            function recalculateBookingTotals() {
                let subtotal = 0;
                
                // Calculate new totals for each booking
                $('.table-striped tbody tr:not(.invoice-items-container)').each(function() {
                    let $row = $(this);
                    let bookingId = $row.find('button[data-booking-id]').data('booking-id');
                    
                    if (bookingId) {
                        let totalPrice = parseFloat($row.find('.booking-total').data('total')) || 0;
                        subtotal += totalPrice;
                    }
                });
                
                // Update subtotal
                $('.subtotal-amount').text('£' + subtotal.toFixed(2));
                
                // Calculate VAT
                let vatRate = parseFloat("{{ $invoice->TaxRate ?? 20 }}");
                let vatAmount = (subtotal * vatRate) / 100;
                $('.vat-amount').text('£' + vatAmount.toFixed(2));
                
                // Update total
                let finalAmount = subtotal + vatAmount;
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
