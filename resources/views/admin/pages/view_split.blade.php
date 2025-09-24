@extends('layouts.main')

@section('content')
<style>
    .table-title {
        font-weight: 500;
        font-size: 1.2rem;
        text-align: center;
        margin-bottom: 15px;
    }

    .card-title {
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 15px;
    }

    .table th, .table td {
        padding: 12px;
        vertical-align: middle;
    }

    .table th {
        width: 40%;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
    }

    .text-end {
        text-align: right;
    }

    .border-top {
        border-top: 2px solid #dee2e6 !important;
    }
</style>

<div class="container mt-4">
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back</a>
     <font size="medium" color="black">Split Invoice Details - {{ $invoice->InvoiceNumber ?? 'N/A' }}
    {{-- <div class="table-title">
        <h3>Split Invoice Details - #{{ $invoice->InvoiceNumber ?? 'N/A' }}</h3>
    </div> --}}

    <div class="row g-4 mx-0 px-3">
        <!-- Company Information -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Company Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Company Name:</strong> {{ $booking->CompanyName }}</div>
                    <div class="mb-2"><strong>Opportunity Name:</strong> {{ $booking->OpportunityName }}</div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Purchase Order:</strong> {{ $booking->PurchaseOrderNo ?? '-' }}</div>
                    <div class="mb-2"><strong>Notes:</strong> <span class="text-muted fst-italic">{{ $booking->Notes ?? '-' }}</span></div>
                    <div class="mb-2"><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($booking->InvoiceDate)->format('d-m-Y') ?? '-' }}</div>
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
                    <div class="mb-2"><strong>Type Of Payment:</strong> <span class="badge bg-success">CARD</span></div>
                    <div class="mb-2"><strong>Tax Rate:</strong> {{ $invoice->TaxRate ?? 20 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Information Table -->
    <div class="loads-section mt-4 mx-3">
    <div class="d-flex justify-content-between align-items-center p-3 rounded-top mb-0" style="background: #3c8dbc;">
        <h5 class="text-white mb-0">
            <i class="fas fa-truck me-2"></i> Load Detail
        </h5>
        <a href="#" id="exportExcelBtn" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel me-2"></i> Export to Excel
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>Requested Date</th>
                    <th>Material</th>
                    <th>Load Type</th>
                    <th>Lorry Type</th>
                    <th>Total Loads</th>
                    <th class="text-end">Price/Load</th>
                    <th class="text-end">Total Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ \Carbon\Carbon::parse($booking->InvoiceDate)->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ $booking->MaterialName ?? '-' }}</td>
                    <td>
                        @if ($booking->LoadType == '1') Loads
                        @elseif ($booking->LoadType == '2') Turnaround
                        @else Both @endif
                    </td>
                    <td>
                        @if ($booking->LorryType == '1') Tipper
                        @elseif ($booking->LorryType == '2') Grab
                        @elseif ($booking->LorryType == '3') Bin
                        @else NA @endif
                    </td>
                    <td>{{ $booking->Loads ?? 0 }}</td>
                    <td class="text-end">£{{ number_format($booking->Price ?? 0, 2) }}</td>
                    <td class="text-end">£{{ number_format(($booking->Price ?? 0) * ($booking->Loads ?? 0), 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                            @php
                                $vatRate = $invoice->TaxRate ?? 20;
                                $subtotal = $booking->Price ?? 0;
                                $vatAmount = $subtotal * ($vatRate / 100);
                                $total = $subtotal + $vatAmount;
                            @endphp
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-end text-muted">SubTotal</td>
                                <td class="text-end subtotal-amount">£{{ number_format($invoice->SubTotalAmount, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-end text-muted">VAT ({{ $vatRate }}%)</td>
                                <td class="text-end vat-amount">£{{ number_format($vatAmount, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr class="border-top">
                                <td colspan="5"></td>
                                <td class="text-end text-muted">Total</td>
                                <td class="text-end total-amount fw-bold">£{{ number_format($total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
        </table>
    </div>
</div>

<!-- Comment and Hold Invoice Section -->
            <div class="card mt-4 border-0 shadow-sm mx-3 mb-3">
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
                                value="{{ $invoice->CompanyID }}">
                            <input type="hidden" name="CompanyName"
                                value="{{ $booking->CompanyName }}">
                            <input type="hidden" name="OpportunityID"
                                value="{{ $invoice->OpportunityID }}">
                            <input type="hidden" name="OpportunityName"
                                value="{{ $booking->OpportunityName }}">
                            <input type="hidden" name="ContactID"
                                value="{{ $invoice->ContactID }}"> 
                            <input type="hidden" name="ContactName"
                                value="{{ $invoice->ContactName }}">
                            <input type="hidden" name="ContactMobile"
                                value="{{ $invoice->ContactMobile }}"> 
                            <input type="hidden" name="SubTotalAmount" value="{{ $invoice->SubTotalAmount }}"> 
                            <input type="hidden" name="VatAmount" value="{{ $invoice->VatAmount }}">
                            <input type="hidden" name="FinalAmount" value="{{ $invoice->FinalAmount }}">
                            <input type="hidden" name="TaxRate" value="20">
                            <input type="hidden" name="CreatedUserID" value="{{ auth()->id() }}">
                            <input type="hidden" name="hold_invoice" id="hold_invoice" value="0">
                            <input type="hidden" name="comment" id="comment_value">

                            {{-- <button type="button" class="btn btn-secondary btn-lg px-4 me-2" data-bs-toggle="modal"
                                data-bs-target="#mergeBookingModal" data-invoice-id="{{ $booking['BookingRequestID'] }}">
                                <i class="fas fa-random me-2"></i>Merge Booking
                            </button>

                            <button type="button" class="btn btn-secondary btn-lg px-4 me-2" data-bs-toggle="modal"
                                data-bs-target="#splitInvoiceModal" data-invoice-id="{{ $booking->BookingID }}">
                                <i class="fas fa-random me-2"></i>Split Invoice
                            </button> --}}

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
<script>
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
    </script>
@endsection
