@extends('layouts.main')

<style>
    .invoice-page {
        background: #f8f9fa;
    }
    .card {
        transition: all 0.3s ease;
        border-radius: 10px !important;
        overflow: hidden;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: none;
    }
    .card-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .card-body {
        padding: 1.5rem;
    }
    .badge {
        padding: 0.5rem 1rem;
        font-weight: 500;
        font-size: 0.9rem;
        border-radius: 6px;
    }
    .badge.bg-light {
        background-color: #f8f9fa !important;
        border: 1px solid #e9ecef;
    }
    .table {
        width: 100%;
        margin-bottom: 0;
        border-radius: 10px;
        overflow: hidden;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border-top: none;
    }
    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f1f1f1;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table tfoot {
        background-color: #f8f9fa;
        font-weight: 500;
    }
    .table tfoot tr:last-child {
        border-top: 2px solid #dee2e6;
    }
    .text-muted {
        color: #6c757d !important;
    }
    .border-top {
        border-top: 2px solid #dee2e6 !important;
    }
    .invoice-header {
        color: black;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .action-buttons .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .loads-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .loads-section .header {
        background: linear-gradient(45deg, #3c8dbc, #2196F3);
        color: white;
        padding: 1.5rem;
        border-radius: 10px 10px 0 0;
    }
</style>

@section('content')
<div class="container-fluid py-4 invoice-page">
    <!-- Add Back Button at Top -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() ?? route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <h4 class="m-0 ms-3">View Invoice</h4>
    </div>

    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="m-0 fw-bold">
                    <!-- <a href="{{ url()->previous() }}" class="text-decoration-none text-dark">
                        <i class="fas fa-arrow-left me-2"></i>
                    </a> -->
                    Invoice #{{ $invoice->InvoiceNumber }}
                </h3>
                <h5 class="text-black mb-0 mt-2">
                    <i class="far fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d-m-Y') }}
                </h5>
            </div>
            <div class="text-end">
                <h4 class="mb-2">Total Amount</h4>
                <h2 class="mb-0">£{{ number_format($invoice->FinalAmount, 2) }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Invoice Information -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-white mb-0 fs-3"><i class="fas fa-file-invoice me-2"></i>Invoice Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="info-label">Invoice Number</div>
                        <div class="info-value">{{ $invoice->InvoiceNumber }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Invoice Date</div>
                        
                        <div class="info-value">{{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Status</div>
                        <div>
                            @if($invoice->is_hold == 1)
                                <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>On Hold</span>
                            @elseif($invoice->is_hold == 0)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Complete</span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-question me-1"></i>Unknown</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Booking Request ID</div>
                        <div class="info-value">{{ $invoice->BookingRequestID }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="text-white mb-0 fs-3"><i class="fas fa-building me-2"></i>Client Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="info-label">Company</div>
                        <div class="info-value">{{ $invoice->CompanyName }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Contact Person</div>
                        <div class="info-value">{{ $invoice->ContactName }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value">{{ $invoice->ContactMobile }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Opportunity</div>
                        <div class="info-value">{{ $invoice->OpportunityName }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="text-white mb-0 fs-3"><i class="fas fa-clock me-2"></i>Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="info-label">Waiting Time</div>
                        <div class="info-value">{{ $invoice->WaitingTime ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Wait Charges</div>
                        <div class="info-value">{{ $invoice->WaitingCharge ? '£'.number_format($invoice->WaitingCharge, 2) : 'N/A' }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Purchase Order</div>
                        <div class="info-value">{{ $invoice->PurchaseOrderNo ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Notes</div>
                        <div class="text-muted fst-italic small mt-1">
                            {{ $invoice->Notes ?? 'No notes available' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="text-white mb-0 fs-3"><i class="fas fa-money-bill me-2"></i>Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="info-label">Sub Total</div>
                        <div class="info-value">£{{ number_format($invoice->SubTotalAmount, 2) }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">VAT 20%</div>
                        <div class="info-value">£{{ number_format($invoice->VatAmount, 2) }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value text-success fs-4">£{{ number_format($invoice->FinalAmount, 2) }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Payment Method</div>
                        <span class="badge bg-info"><i class="fas fa-credit-card me-1"></i>{{ $invoice->PaymentType ?? 'CARD' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="loads-section mt-4">
        <div class="header">
            <h5 class="mb-0 fs-3"><i class="fas fa-truck me-2"></i>Invoice Items</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr fs-5>
                        <th>Item #</th>
                        <th>Description</th>
                        <th class="text-center">Quantity</th>
                        <th>Comment</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Net Amount</th>
                        <th class="text-end">Gross Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $items = $invoice->items;
                        $subtotal = 0;
                    @endphp
                    @forelse($items as $item)
                        <tr>
                            <td><span class="badge bg-light text-dark">{{ str_pad($item->ItemNumber, 3, '0', STR_PAD_LEFT) }}</span></td>
                            <td>{{ $item->Description }}</td>
                            <td class="text-center numeric-cell">{{ $item->Qty }}</td>
                            <td>{{ $item->Comment1 }}</td>
                            <td class="text-end numeric-cell">£{{ number_format($item->UnitPrice, 2) }}</td>
                            <td class="text-end numeric-cell">£{{ number_format($item->NetAmount, 2) }}</td>
                            <td class="text-end numeric-cell">£{{ number_format($item->GrossAmount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-2x mb-3"></i>
                                    <p class="mb-0">No invoice items found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($items->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end">Subtotal</td>
                        <td class="text-end numeric-cell fw-bold">£{{ number_format($invoice->SubTotalAmount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end">VAT {{ $invoice->TaxRate ?? 20 }}%</td>
                        <td class="text-end numeric-cell fw-bold">£{{ number_format($invoice->VatAmount, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td colspan="6" class="text-end fw-bold">Total Amount</td>
                        <td class="text-end numeric-cell fw-bold fs-5 text-primary">£{{ number_format($invoice->FinalAmount, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0 fs-3"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <div class="info-label">Comments</div>
                        <div class="border rounded p-3 bg-light mt-2">
                            {{ $invoice->comment ?: 'No comments available' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <div class="info-label">Created By</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Created At</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($invoice->CreateDateTime)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">
                            @if($invoice->UpdateDateTime && $invoice->UpdateDateTime != '0000-00-00 00:00:00')
                                {{ \Carbon\Carbon::parse($invoice->UpdateDateTime)->format('d/m/Y H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($invoice->UpdateDateTime)->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Add hover effect to table rows
        $('.table tbody tr').hover(
            function() { $(this).addClass('table-hover'); },
            function() { $(this).removeClass('table-hover'); }
        );
    });
</script>
@endpush
@endsection
