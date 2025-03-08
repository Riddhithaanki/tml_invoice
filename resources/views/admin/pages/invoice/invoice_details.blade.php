@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="booking-section p-4 bg-light rounded shadow-sm">
            <div class="d-flex align-items-center mb-4">
                <a href="#" class="text-decoration-none text-primary">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>
                <h4 class="m-0 fw-bold">Booking No. : {{ $item->booking->BookingID }}</h4>
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
                                <strong>Company Name:</strong> {{ $item->CompanyName }}
                            </div>
                            <div class="mb-3">
                                <strong>Opportunity Name:</strong> {{ $item->OpportunityName }}
                            </div>
                            <div class="mb-3">
                                <strong>Contact Name:</strong> {{ $item->ContactName }}
                            </div>
                            <div class="mb-3">
                                <strong>Contact Mobile:</strong> <a href="tel:{{ $item->ContactMobile }}"
                                    class="text-decoration-none">{{ $item->ContactMobile }}</a>
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
                            <tr>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary toggle-invoice"
                                        data-invoice-id="{{ $item->InvoiceID }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </td>
                                <td>{{ $item->InvoiceDate }}</td>
                                <td><span class="badge bg-primary">{{ $item->booking->BookingType }}</span></td>
                                <td>{{ $item->booking->MaterialName }}</td>
                                <td>{{ $item->booking->LoadType }}</td>
                                <td>{{ $item->booking->LorryType }}</td>
                                <td class="text-center fw-bold">{{ $item->booking->Loads }}</td>
                                <td class="text-center"><span
                                        class="text-primary fw-bold">£{{ $item->booking->Price }}</span></td>
                                <td class="text-end fw-bold">£{{ $item->booking->TotalAmount }}</td>
                            </tr>
                            <tr class="invoice-items-container d-none" id="invoice-items-{{ $item->InvoiceID }}">
                                <td colspan="9" class="p-0">
                                    <div class="invoice-items-content p-3 bg-light">
                                        <!-- Fetched invoice items will be displayed here -->
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table">
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end"><strong>SubTotal</strong></td>
                                <td class="text-end">£1,680.00</td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end"><strong>VAT (20%)</strong></td>
                                <td class="text-end">£336.00</td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end"><strong>Total</strong></td>
                                <td class="text-end fw-bold fs-5">£2,016.00</td>
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
                            data-bs-target="#splitInvoiceModal" data-invoice-id="{{ $item->InvoiceID }}">
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
                let invoiceId = $(this).data('invoice-id');
                let container = $('#invoice-items-' + invoiceId);
                let content = container.find('.invoice-items-content');

                if (container.hasClass('d-none')) {
                    // Show loader with animation
                    content.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-primary">Loading invoice details...</p></div>');
                    container.removeClass('d-none');

                    $.ajax({
                        url: "{{ route('get.invoice.items') }}",
                        type: "GET",
                        data: { invoice_id: invoiceId },
                        success: function (response) {
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
                            });

                            html += '</tbody></table></div>';

                            // Animate content replacement
                            setTimeout(() => {
                                content.fadeOut(200, function () {
                                    $(this).html(html).fadeIn(300);
                                });
                            }, 500);
                        }
                    });
                } else {
                    // Slide up animation when hiding
                    container.slideUp(300, function () {
                        $(this).addClass('d-none').css('display', '');
                    });
                }

                // Toggle expand icon with animation
                $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
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
            $('#splitInvoiceModal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var invoiceId = button.data('invoice-id');
                var modal = $(this);
                var modalBody = modal.find('.modal-body');

                // Show loader
                modalBody.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-primary">Loading invoice items...</p></div>');

                // Fetch invoice items via AJAX
                $.ajax({
                    url: "{{ route('get.invoice.items') }}",
                    type: "GET",
                    data: { invoice_id: invoiceId },
                    success: function (response) {
                        var html = `<div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Select</th>
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
                            item.loads.forEach(load => {
                                var waitTime = calculateWaitTime(load.SiteInDateTime, load.SiteOutDateTime);
                                var statusText = getStatusText(load.Status);
                                var statusClass = getStatusClass(load.Status);

                                html += `<tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input split-checkbox" value="${load.TicketID}">
                                            </td>
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

            $(document).ready(function () {
                $('#splitInvoiceModal').on('show.bs.modal', function (e) {
                    var button = $(e.relatedTarget);
                    var invoiceId = button.data('invoice-id');
                    var modal = $(this);
                    var modalBody = modal.find('.modal-body');

                    // Show loader
                    modalBody.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-primary">Loading invoice items...</p></div>');

                    // Fetch invoice items
                    $.ajax({
                        url: "{{ route('get.invoice.items') }}",
                        type: "GET",
                        data: { invoice_id: invoiceId },
                        success: function (response) {
                            var html = `<div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Select</th>
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
                                item.loads.forEach(load => {
                                    var statusClass = getStatusClass(load.Status);

                                    html += `<tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input split-checkbox" value='${JSON.stringify(load)}'>
                                    </td>
                                    <td>${load.ConveyanceNo}</td>
                                    <td><span class="badge bg-dark">${load.TicketID}</span></td>
                                    <td>${formatDateTime(load.JobStartDateTime)}</td>
                                    <td>${load.DriverName}</td>
                                    <td>${load.VehicleRegNo}</td>
                                    <td class="text-end">${formatWeight(load.GrossWeight)}</td>
                                    <td class="text-end">${formatWeight(load.Tare)}</td>
                                    <td class="text-end fw-bold">${formatWeight(load.Net)}</td>
                                    <td>${item.MaterialName}</td>
                                    <td>${calculateWaitTime(load.SiteInDateTime, load.SiteOutDateTime)}</td>
                                    <td><span class="badge ${statusClass}">${getStatusText(load.Status)}</span></td>
                                    <td class="text-end fw-bold">£${load.LoadPrice}</td>
                                </tr>`;
                                });
                            });

                            html += '</tbody></table></div>';

                            // Append submit button
                            html += `<button id="submitSelectedLoads" class="btn btn-primary mt-3">Submit Selected Loads</button>`;

                            modalBody.fadeOut(200, function () {
                                $(this).html(html).fadeIn(300);
                            });
                        }
                    });
                });

                // Submit selected loads
                $(document).on('click', '#submitSelectedLoads', function () {
                    var selectedLoads = [];

                    $('.split-checkbox:checked').each(function () {
                        selectedLoads.push(JSON.parse($(this).val())); // Parse JSON to object
                    });

                    if (selectedLoads.length === 0) {
                        alert('Please select at least one load.');
                        return;
                    }

                    console.log(selectedLoads); // Debugging: Check the selected loads in console

                    // Send data to backend
                    $.ajax({
                        url: "{{ route('split.invoice') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}", // Include CSRF token
                            invoice_id: $('#splitInvoiceModal').data('invoice-id'),
                            loads: selectedLoads
                        },
                        success: function (response) {
                            alert('Loads submitted successfully!');
                            $('#splitInvoiceModal').modal('hide');
                        },
                        error: function () {
                            alert('Something went wrong. Please try again.');
                        }
                    });
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

    </script>

@endsection
