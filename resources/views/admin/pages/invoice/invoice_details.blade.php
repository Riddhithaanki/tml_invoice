@extends('layouts.main')

@section('content')
    <div class="container-fluid">
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
                            <h5 class="mb-0"><i class="fas fa-building me-2"></i>Company Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Company Name:</strong> {{ $booking->CompanyName }}
                            </div>
                            <div class="mb-3">
                                <strong>Opportunity Name:</strong> {{ $booking->OpportunityName }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waiting Time and Charges -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Booking Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Waiting Time:</strong> <span class="badge bg-light text-dark">15 minutes</span>
                            </div>
                            <div class="mb-3">
                                <strong>Wait Charges:</strong> <span class="badge bg-light text-dark">1 £/minute</span>
                            </div>
                            <div class="mb-3">
                                <strong>Purchase Order:</strong> <span class="badge bg-light text-dark">215/102649</span>
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
                            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
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
                                <th width="10%">Price/Load</th>
                                <th width="10%">Total Price</th>
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
                                    <td>{{ $invoice->InvoiceDate }}</td>
                                    <td><span class="badge bg-primary">{{ $booking->BookingType }}</span></td>
                                    <td>{{ $booking->MaterialName }}</td>
                                    <td>{{ $booking->LoadType }}</td>
                                    <td>{{ $booking->LorryType }}</td>
                                    <td class="text-center fw-bold">{{ $booking->Loads }}</td>
                                    <td class="text-center"><span class="text-primary fw-bold">£{{ $booking->Price }}</span>
                                    </td>
                                    <td class="text-end fw-bold">£{{ $booking->TotalAmount }}</td>
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
                        <tfoot class="table">
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end"><strong>SubTotal</strong></td>
                                <td class="text-end">£{{ $invoice->SubTotalAmount }}</td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end"><strong>VAT ({{ $invoice->TaxRate }}%)</strong></td>
                                <td class="text-end">£{{ $invoice->VatAmount }}</td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end"><strong>Total</strong></td>
                                <td class="text-end fw-bold fs-5">£{{ $invoice->FinalAmount }}</td>
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
                        <button class="btn btn-secondary btn-lg px-4 me-2" data-bs-toggle="modal"
                            data-bs-target="#splitInvoiceModal" data-invoice-id="{{ $booking['BookingRequestID'] }}">
                            <i class="fas fa-random me-2"></i>Split Invoice
                        </button>

                        <button class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-check-circle me-2"></i> Proceed To Confirm
                        </button>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="submitSelectedLoads" class="btn btn-primary mt-3">Confirm Split</button>

                </div>
            </div>
        </div>
    </div>


    <!-- jQuery (Ensure it's included in your layout) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.toggle-invoice').on('click', function () {
                let bookingId = $(this).data('booking-id');
                let container = $('.invoice-items-container[data-booking-id="' + bookingId + '"]');
                let content = container.find('.invoice-items-content');

                if (container.hasClass('d-none')) {
                    // Show loader
                    content.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-primary">Loading booking details...</p></div>');
                    container.removeClass('d-none').slideDown(300);

                    $.ajax({
                        url: "{{ route('get.invoice.items') }}",
                        type: "GET",
                        data: { booking_id: bookingId },
                        success: function (response) {
                            if (!response.invoice_items || response.invoice_items.length === 0) {
                                content.html('<div class="text-center py-4 text-danger fw-bold">No details found for this booking.</div>');
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
                                    // If loads is empty, show "No data found"
                                    html += `<tr>
                                                            <td colspan="12" class="text-center text-warning fw-bold">No details available for this material.</td>
                                                         </tr>`;
                                } else {
                                    // Loop through loads if available
                                    item.loads.forEach(load => {
                                        let waitTime = calculateWaitTime(load.SiteInDateTime, load.SiteOutDateTime);
                                        let statusText = getStatusText(load.Status);
                                        let statusClass = getStatusClass(load.Status);

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
                                                                <td class="text-end fw-bold">£${load.LoadPrice}</td>
                                                            </tr>`;
                                    });
                                }
                            });

                            html += '</tbody></table></div>';

                            // Animate content replacement
                            setTimeout(() => {
                                content.fadeOut(200, function () {
                                    $(this).html(html).fadeIn(300);
                                });
                            }, 500);
                        },
                        error: function () {
                            content.html('<div class="text-center py-4 text-danger fw-bold">Error loading details. Please try again.</div>');
                        }
                    });
                } else {
                    // Hide content
                    container.slideUp(300, function () {
                        $(this).addClass('d-none').css('display', '');
                    });
                }

                // Toggle expand icon
                $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
            });

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
            }

            function formatWeight(weight) {
                return weight ? parseFloat(weight).toLocaleString('en-GB') : "0";
            }
        });

        $(document).ready(function () {
            $('#splitInvoiceModal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var invoiceId = button.data('invoice-id');
                var modal = $(this);
                var modalBody = modal.find('.modal-body');

                // Show loader
                modalBody.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-primary">Loading invoice items...</p></div>');

                // Fetch invoice items via AJAX
                $.ajax({
                    url: "{{ route('get.splitinvoice.items') }}",
                    type: "GET",
                    data: { invoice_id: invoiceId },
                    success: function (response) {
                        if (!response.invoice_items || response.invoice_items.length === 0) {
                            modalBody.html('<div class="text-center py-4 text-danger fw-bold">No details found for this invoice.</div>');
                            return;
                        }

                        var html = `<div class="table-responsive">
                                                <table class="table table-bordered table-striped table-hover">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th>Select</th>
                                                            <th>Booking ID</th>
                                                            <th>Material</th>
                                                            <th>Load Type</th>
                                                            <th>Total Loads</th>
                                                            <th>Price/Load</th>
                                                            <th>Total Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>`;

                        // Iterate through invoice_items
                        response.invoice_items.forEach(invoice => {
                            if (!invoice.booking || invoice.booking.length === 0) {
                                html += `<tr>
                                                    <td colspan="7" class="text-center text-warning fw-bold">No bookings found.</td>
                                                 </tr>`;
                            } else {
                                // Iterate through booking data
                                invoice.booking.forEach(booking => {
                                    html += `<tr>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input split-checkbox" value="${booking.BookingID}">
                                                        </td>
                                                        <td>${booking.BookingID}</td>
                                                        <td>${booking.MaterialName}</td>
                                                        <td>${booking.LoadType}</td>
                                                        <td class="text-center fw-bold">${booking.Loads}</td>
                                                        <td class="text-center">£${booking.Price}</td>
                                                        <td class="text-end fw-bold">£${booking.TotalAmount}</td>
                                                    </tr>`;
                                });
                            }
                        });

                        html += '</tbody></table></div>';

                        // Fade out loader and show content
                        setTimeout(() => {
                            modalBody.fadeOut(200, function () {
                                $(this).html(html).fadeIn(300);
                            });
                        }, 500);
                    },
                    error: function () {
                        modalBody.html('<p class="text-danger text-center">Error loading data. Please try again.</p>');
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

        $(document).ready(function () {
            $('#submitSelectedLoads').on('click', function () {
                var selectedLoads = [];

                $('.split-checkbox:checked').each(function () {
                    selectedLoads.push({ LoadID: $(this).val() });
                });

                if (selectedLoads.length === 0) {
                    alert('Please select at least one load to split.');
                    return;
                }

                $.ajax({
                    url: "{{ route('split.invoice') }}", // Ensure this route is defined
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        loads: selectedLoads
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('Invoice split successfully!');
                            $('#splitInvoiceModal').modal('hide'); // Close modal on success
                            location.reload(); // Reload the page to update the UI
                        } else {
                            alert(response.error || 'An error occurred.');
                        }
                    },
                    error: function (xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        });

    </script>

@endsection
