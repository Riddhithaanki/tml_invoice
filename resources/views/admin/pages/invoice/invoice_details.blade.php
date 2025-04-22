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
                <h4 class="m-0 fw-bold">Booking No. : {{ $bookingData->BookingID }}</h4>
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
                                <strong>Company Name:</strong> {{ $bookingData->bookingRequest->CompanyName }}
                            </div>
                            <div class="mb-3">
                                <strong>Opportunity Name:</strong> {{ $bookingData->bookingRequest->OpportunityName }}
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
                                    class="badge bg-light text-dark">{{ $bookingData->bookingRequest->WaitingTime }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Wait Charges:</strong> <span class="badge bg-light text-dark">
                                    {{ $bookingData->bookingRequest->WaitingCharge }} </span>
                            </div>
                            <div class="mb-3">
                                <strong>Purchase Order:</strong> <span class="badge bg-light text-dark">
                                    {{ $bookingData->PurchaseOrderNo }} </span>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary toggle-invoice"
                                            data-booking-id="{{ $booking->BookingID }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </td>
                                    <td>{{ $invoice->CreateDateTime }}</td>
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
                                    <td class="text-end">£{{ number_format($booking->Price, 2) }}</td>
                                    <td class="text-end">£{{ number_format($booking->TotalAmount, 2) }}</td>
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
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end text-muted">SubTotal</td>
                                <td class="text-end">£{{ number_format($invoice->SubTotalAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end text-muted">VAT ({{ $invoice->TaxRate ?? 20 }}%)</td>
                                <td class="text-end">£{{ number_format($invoice->VatAmount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td colspan="7"></td>
                                <td class="text-end text-muted">Total</td>
                                <td class="text-end fw-bold">£{{ number_format($invoice->TotalAmount, 2) }}</td>
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
                            <input type="hidden" name="BookingRequestID" value="{{ $bookingData->BookingRequestID }}">
                            <input type="hidden" name="CompanyID"
                                value="{{ $bookingData->bookingRequest->CompanyID }}">
                            <input type="hidden" name="CompanyName"
                                value="{{ $bookingData->bookingRequest->CompanyName }}">
                            <input type="hidden" name="OpportunityID"
                                value="{{ $bookingData->bookingRequest->OpportunityID }}">
                            <input type="hidden" name="OpportunityName"
                                value="{{ $bookingData->bookingRequest->OpportunityName }}">
                            <input type="hidden" name="ContactID"
                                value="{{ $bookingData->bookingRequest->ContactID }}">
                            <input type="hidden" name="ContactName"
                                value="{{ $bookingData->bookingRequest->ContactName }}">
                            <input type="hidden" name="ContactMobile"
                                value="{{ $bookingData->bookingRequest->ContactMobile }}">
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
                                data-bs-target="#splitInvoiceModal" data-invoice-id="{{ $bookingData->BookingID }}">
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
                        data-booking-request-id="{{ $bookingData->BookingRequestID }}">
                        <i class="fas fa-check me-2"></i>Confirm Split
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Merge Booking Modal -->
    <div class="modal fade" id="mergeBookingModal" tabindex="-1" aria-labelledby="mergeBookingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mergeBookingModalLabel">Merge Bookings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-primary">Loading booking...</p>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button id="submitSelectedbooking" class="btn btn-primary"
                        data-booking-request-id="{{ $booking['BookingRequestID'] }}">
                        <i class="fas fa-check me-2"></i>Confirm Merge
                    </button>
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




    <!-- jQuery (Ensure it's included in your layout) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
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
                        data: {
                            booking_id: bookingId
                        },
                        success: function(response) {
                            if (!response.invoice_items || response.invoice_items.length ===
                                0) {
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
                                    item.loads.forEach(load => {
                                        let waitTime = calculateWaitTime(load
                                            .SiteInDateTime, load
                                            .SiteOutDateTime);
                                        let statusText = getStatusText(load
                                            .Status);
                                        let statusClass = getStatusClass(load
                                            .Status);

                                        html += `<tr>
                                        <td>${load.ConveyanceNo}</td>
                                        <td><span class="badge bg-dark">${load.TicketID}</span></td>
                                        <td>${formatDateTime(load.JobStartDateTime)}</td>
                                        <td>${load.DriverName}</td>
                                        <td>${load.VehicleRegNo}</td>
                                        <td class="text-end">${formatWeight(load.GrossWeight)}</td>
                                        <td class="text-end">${formatWeight(load.Tare)}</td>
                                        <td class="text-end fw-bold">${formatWeight(load.Net)}</td>
                                        <td>${item.MaterialName}</td>
                                        <td>${waitTime}</td>
                                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                                        <td class="text-end fw-bold">
                                            <div class="d-flex align-items-center">
                                                <input type="text" class="form-control editable-price me-2"
                                                    style="width: 100px;"
                                                    data-ticket-id="${load.TicketID}"
                                                    value="${load.LoadPrice}">
                                                <button class="btn btn-sm btn-outline-primary price-history-btn"
                                                        data-ticket-id="${load.TicketID}"
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

            $(document).on('keydown', '.editable-price', function(event) {
                if (event.key === "Enter" || event.key === "Tab") {
                    event.preventDefault(); // Prevent default tab/enter behavior

                    let input = $(this);
                    let newPrice = parseFloat(input.val()) || 0;
                    let ticketId = input.data('ticket-id');

                    if (confirm(`Are you sure you want to update the price to £${newPrice.toFixed(2)}?`)) {
                        // Update the price field and recalculate totals
                        input.val(newPrice.toFixed(2));

                        // Send AJAX request to save price
                        $.ajax({
                            url: "{{ route('update.invoice.price') }}",
                            type: "POST",
                            data: {
                                ticket_id: ticketId,
                                new_price: newPrice,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                console.log("Price updated successfully.");
                                updateInvoiceTotals
                                    (); // Ensure totals update only on successful response
                            },
                            error: function() {
                                alert("Failed to update price. Try again.");
                                input.val(input.data(
                                    'original-price')); // Reset to original if failed
                            }
                        });
                    } else {
                        input.val(input.data('original-price')); // Reset to original if canceled
                    }
                }
            });



            function updateInvoiceTotals() {
                let subtotal = 0;
                let bookingTotals = {}; // Store totals per booking

                // Sum all price input values and group by booking ID
                $('.editable-price').each(function() {
                    let price = parseFloat($(this).val()) || 0;
                    let bookingId = $(this).data('booking-id'); // Fetch BookingID from the input field

                    subtotal += price;

                    if (!bookingTotals[bookingId]) {
                        bookingTotals[bookingId] = 0;
                    }
                    bookingTotals[bookingId] += price;
                });

                // VAT Calculation (Using the tax rate from the invoice)
                let vatRate = parseFloat("{{ $invoice->TaxRate ?? 20 }}"); // Get VAT rate from invoice or default to 20%
                let vatAmount = (subtotal * vatRate) / 100;
                let total = subtotal + vatAmount;

                // Update the invoice totals dynamically
                $('.invoice-subtotal').text(`£${subtotal.toFixed(2)}`);
                $('.invoice-vat').text(`£${vatAmount.toFixed(2)}`);
                $('.invoice-total').text(`£${total.toFixed(2)}`);

                // Update each booking's total amount dynamically
                for (let bookingId in bookingTotals) {
                    $(`.booking-total[data-booking-id="${bookingId}"]`).text(
                        `£${bookingTotals[bookingId].toFixed(2)}`);
                }
            }

            $(document).on('click', '.price-history-btn', function() {
                let ticketId = $(this).data('ticket-id');

                $.ajax({
                    url: "{{ route('get.price.history') }}",
                    type: "GET",
                    data: {
                        ticket_id: ticketId
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
            </div>
        `;
                        });

                        historyHtml += `</div>`;

                        $('#priceHistoryModal .modal-body').html(historyHtml);
                        $('#priceHistoryModal').modal('show');
                    }

                });

            });


            window.calculateWaitTime = function(siteIn, siteOut) {
                if (!siteIn || !siteOut) return "N/A";
                let inTime = new Date(siteIn);
                let outTime = new Date(siteOut);
                let diffMinutes = Math.round((outTime - inTime) / 60000);
                return diffMinutes > 60 ? `${Math.floor(diffMinutes / 60)}h ${diffMinutes % 60}m` :
                    `${diffMinutes} min`;
            };

            window.getStatusText = function(status) {
                const statusMap = {
                    0: "Allocated",
                    1: "Accepted",
                    2: "Arrive Site",
                    3: "Receipt Generated",
                    4: "Finish Load",
                    5: "Cancel Load"
                };
                return statusMap[status] || "Unknown";
            };

            window.getStatusClass = function(status) {
                const classMap = {
                    0: "bg-secondary",
                    1: "bg-info",
                    2: "bg-warning",
                    3: "bg-primary",
                    4: "bg-success",
                    5: "bg-danger"
                };
                return classMap[status] || "bg-secondary";
            };

            window.formatDateTime = function(dateTime) {
                if (!dateTime) return "N/A";

                const date = new Date(dateTime);

                // Extract date components
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                const day = String(date.getDate()).padStart(2, '0');

                // Extract time components
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');

                // Construct the formatted string
                return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            };

            window.formatWeight = function(weight) {
                return weight ? parseFloat(weight).toLocaleString('en-GB') : "0";
            };

            // Handle hold invoice checkbox
            $('#holdInvoice').on('change', function() {
                $('#hold_invoice').val(this.checked ? '1' : '0');
            });

            // Handle comment textarea
            $('#comment').on('change', function() {
                $('#comment_value').val($(this).val());
            });

            // Handle form submission with loading state
            $('#confirmInvoiceForm').on('submit', function(e) {
                e.preventDefault();

                // Get the proceed button and form
                const $form = $(this);
                const $button = $('#proceedButton');
                const $normalState = $button.find('.normal-state');
                const $loadingState = $button.find('.loading-state');

                // Show loading state
                $button.prop('disabled', true);
                $normalState.addClass('d-none');
                $loadingState.removeClass('d-none');
                $form.addClass('processing');

                // Get all form data
                var formData = $(this).serialize();

                // Collect all booking loads data
                var bookingLoads = [];
                $('.table-striped tbody tr').each(function() {
                    var bookingId = $(this).find('button[data-booking-id]').data('booking-id');
                    if (bookingId) {
                        var bookingData = {
                            BookingID: bookingId,
                            MaterialName: $(this).find('td:eq(3)').text(),
                            LoadType: $(this).find('td:eq(4)').text(),
                            Loads: $(this).find('td:eq(6)').text(),
                            Price: $(this).find('td:eq(7)').text().replace('£', ''),
                            TotalAmount: $(this).find('td:eq(8)').text().replace('£', '')
                        };
                        bookingLoads.push(bookingData);
                    }
                });

                // Add booking loads to form data
                formData += '&booking_loads=' + encodeURIComponent(JSON.stringify(bookingLoads));

                // Collect all invoice items data
                var invoiceItems = [];
                $('.editable-price').each(function() {
                    var ticketId = $(this).data('ticket-id');
                    var price = $(this).val();
                    invoiceItems.push({
                        ticket_id: ticketId,
                        price: price
                    });
                });

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
                        alert('An error occurred while processing your request.');
                    }
                });
            });

            // Handle split invoice button click
            $('button[data-bs-target="#splitInvoiceModal"]').on('click', function(e) {
                e.preventDefault();
                var invoiceId = $(this).data('invoice-id');
                $('#splitInvoiceModal').modal('show');
            });

            // Handle split invoice confirmation
            $('#submitSelectedLoads').on('click', function(e) {
                e.preventDefault();
                var selectedLoads = [];
                var bookingRequestID = $(this).data('booking-request-id');

                // Collect selected loads
                $('.split-checkbox:checked').each(function() {
                    selectedLoads.push({
                        LoadID: $(this).val()
                    });
                });

                if (selectedLoads.length === 0) {
                    alert('Please select at least one load to split.');
                    return;
                }

                // Make AJAX call to split invoice
                $.ajax({
                    url: "{{ route('split.invoice') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        booking_request_id: bookingRequestID,
                        loads: selectedLoads
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Invoice split successfully!');
                            $('#splitInvoiceModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.error || 'An error occurred.');
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

            // Handle merge booking button click
            $('button[data-bs-target="#mergeBookingModal"]').on('click', function(e) {
                e.preventDefault();
                var invoiceId = $(this).data('invoice-id');
                $('#mergeBookingModal').modal('show');
            });

            // Handle merge booking confirmation
            $('#submitSelectedbooking').on('click', function(e) {
                e.preventDefault();
                var selectedBookings = [];
                var bookingRequestID = $(this).data('booking-request-id');

                // Collect selected bookings
                $('.merge-checkbox:checked').each(function() {
                    selectedBookings.push({
                        BookingID: $(this).val()
                    });
                });

                if (selectedBookings.length === 0) {
                    alert('Please select at booking to merge.');
                    return;
                }

                // Make AJAX call to merge bookings
                $.ajax({
                    url: "{{ route('merge.booking') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        booking_request_id: bookingRequestID,
                        bookings: selectedBookings
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Booking Merge successfully!');
                            $('#mergeBookingModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.error || 'An error occurred.');
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

            $(document).ready(function() {
                $('#splitInvoiceModal').on('show.bs.modal', function(e) {
                    var button = $(e.relatedTarget);
                    var invoiceId = button.data('invoice-id');
                    var modal = $(this);
                    var modalBody = modal.find('.modal-body');

                    // Show loader
                    modalBody.html(
                        '<div class="text-center py-4">' +
                        '<div class="spinner-border text-primary" role="status">' +
                        '<span class="visually-hidden">Loading...</span></div>' +
                        '<p class="mt-2 text-primary">Loading invoice items...</p></div>'
                    );

                    // Fetch invoice items via AJAX
                    $.ajax({
                        url: "{{ route('get.splitinvoice.items') }}",
                        type: "GET",
                        data: {
                            invoice_id: invoiceId
                        },
                        success: function(response) {
                            if (!response.invoice_items || response.invoice_items
                                .length === 0) {
                                modalBody.html(
                                    '<div class="text-center py-4 text-danger fw-bold">No details found for this invoice.</div>'
                                );
                                return;
                            }

                            console.log(response); // Debugging the response

                            var html = `<div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Booking ID</th>
                                                    <th>Company Name</th>
                                                    <th>Material</th>
                                                    <th>Load Type</th>
                                                    <th>Total Loads</th>
                                                    <th>Price/Load</th>
                                                    <th>Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                            // Loop through each invoice item (Booking)
                            response.invoice_items.forEach(booking => {
                                let bookingRequest = booking
                                    .booking_request; // Related booking request

                                html += `<tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input split-checkbox" value="${booking.BookingID}">
                                            </td>
                                            <td>${booking.BookingID}</td>
                                            <td>${bookingRequest ? bookingRequest.CompanyName : 'N/A'}</td>
                                            <td>${booking.MaterialName || 'N/A'}</td>
                                            <td>${booking.LoadType || 'N/A'}</td>
                                            <td class="text-center fw-bold">${booking.Loads || 0}</td>
                                            <td class="text-center">£${parseFloat(booking.Price).toFixed(2) || '0.00'}</td>
                                            <td class="text-end fw-bold">£${parseFloat(booking.TotalAmount).toFixed(2) || '0.00'}</td>
                                        </tr>`;
                            });

                            html += '</tbody></table></div>';

                            // Fade out loader and show table
                            setTimeout(() => {
                                modalBody.fadeOut(200, function() {
                                    $(this).html(html).fadeIn(300);
                                });
                            }, 500);
                        },
                        error: function() {
                            modalBody.html(
                                '<p class="text-danger text-center">Error loading data. Please try again.</p>'
                            );
                        }
                    });
                });

                $('#mergeBookingModal').on('show.bs.modal', function(e) {
                    var button = $(e.relatedTarget);
                    var invoiceId = button.data('invoice-id');
                    var modal = $(this);
                    var modalBody = modal.find('.modal-body');

                    // Show loader
                    modalBody.html(
                        '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-primary">Loading booking...</p></div>'
                        );

                    // Fetch invoice items via AJAX
                    $.ajax({
                        url: "{{ route('get.mergebookings.items') }}",
                        type: "GET",
                        data: {
                            invoice_id: invoiceId
                        },
                        success: function(response) {
                            if (!response.invoice_items || response.invoice_items.length === 0) {
                                modalBody.html(
                                    '<div class="text-center py-4 text-danger fw-bold">No details found for this invoice.</div>'
                                    );
                                return;
                            }
                            console.log(response);

                            var html = `<div class="table-responsive">
                                                                                    <table class="table table-bordered table-striped table-hover">
                                                                                        <thead class="table-primary">
                                                                                            <tr>
                                                                                                <th>Select</th>
                                                                                                <th width="5%">Expand</th>
                                                                                                <th>Booking ID</th>
                                                                                                <th>Booking Date</th>
                                                                                                <th>Company Name</th>
                                                                                                <th>Site Name</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>`;

                            // Iterate through invoice_items
                            response.invoice_items.forEach(invoice => {
                                if (!invoice || (Array.isArray(invoice) && invoice
                                        .length === 0)) {
                                    html += `<tr>
                                                        <td colspan="5" class="text-center text-warning fw-bold">No bookings found.</td>
                                                    </tr>`;
                                } else {

                                    html += `<tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="form-check-input merge-checkbox" value="${invoice.BookingRequestID}">
                                                            </td>
                                                            <td class="text-center">
                                                                <button class="btn btn-sm btn-outline-primary toggle-loads"
                                                                    data-booking-id="${invoice.BookingRequestID}">
                                                                    <i class="fas fa-chevron-down"></i>
                                                                </button>
                                                            </td>
                                                            <td>${invoice.BookingRequestID}</td>
                                                            <td>${invoice.CreateDateTime}</td>
                                                            <td>${invoice.CompanyName}</td>
                                                            <td>${invoice.OpportunityName}</td>
                                                        </tr>
                                                        <tr class="invoice-loads-container d-none" data-booking-id="${invoice.BookingRequestID}">
                                                            <td colspan="9" class="p-0">
                                                                <div class="invoice-loads-content p-3 bg-light">
                                                                    <!-- Fetched invoice items will be displayed here -->
                                                                </div>
                                                            </td>
                                                        </tr>`;

                                }
                            });


                            html += '</tbody></table></div>';

                            // Fade out loader and show content
                            setTimeout(() => {
                                modalBody.fadeOut(200, function() {
                                    $(this).html(html).fadeIn(300);
                                });
                            }, 500);
                        },
                        error: function() {
                            modalBody.html(
                                '<p class="text-danger text-center">Error loading data. Please try again.</p>'
                                );
                        }
                    });
                });

                function calculateWaitTime(siteIn, siteOut) {
                    if (!siteIn || !siteOut) return "N/A";
                    let inTime = new Date(siteIn);
                    let outTime = new Date(siteOut);
                    let diffMinutes = Math.round((outTime - inTime) / 60000);

                    if (diffMinutes > 60) {
                        let hours = Math.floor(diffMinutes / 60);
                        let mins = diffMinutes % 60;
                        return `${hours}h ${mins}m`;
                    }
                    return diffMinutes + " min";
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
            });
        });
    </script>
@endsection
