@extends('layouts.main')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fs-4 text-white">Invoice Details</h2>
            {{-- <div>
                <button class="btn btn-light btn-sm me-2"><i class="fas fa-print"></i> Print</button>
                <button class="btn btn-light btn-sm"><i class="fas fa-download"></i> Download PDF</button>
            </div> --}}
        </div>
        <div class="card-body">
            <!-- Invoice Header Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h5 class="border-bottom pb-2 mb-3">Invoice Information</h5>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Invoice Number:</div>
                            <div class="col-7 fw-bold">{{ $invoice->InvoiceNumber }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Invoice Type:</div>
                            <div class="col-7">{{ $invoice->InvoiceType }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Invoice Date:</div>
                            <div class="col-7">{{ date('F d, Y', strtotime($invoice->InvoiceDate)) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Status:</div>
                            <div class="col-7">
                                @if($invoice->Status == 0)
                                    <span class="badge bg-warning text-dark">Ready</span>
                                @elseif($invoice->Status == 1)
                                    <span class="badge bg-success">Complete</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Booking Request:</div>
                            <div class="col-7">{{ $invoice->BookingRequestID }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h5 class="border-bottom pb-2 mb-3">Client Information</h5>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Company:</div>
                            <div class="col-7 fw-bold">{{ $invoice->CompanyName }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Contact Name:</div>
                            <div class="col-7">{{ $invoice->ContactName }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Contact Mobile:</div>
                            <div class="col-7">{{ $invoice->ContactMobile }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-muted">Opportunity:</div>
                            <div class="col-7">{{ $invoice->OpportunityName }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="border rounded p-3 mb-4">
                <h5 class="border-bottom pb-2 mb-3">Financial Summary</h5>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Sub Total:</div>
                            <div class="col-sm-8 text-end">{{ number_format($invoice->SubTotalAmount, 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">VAT Amount (20%):</div>
                            <div class="col-sm-8 text-end">{{ number_format($invoice->VatAmount, 2) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 text-muted fw-bold">Final Amount:</div>
                            <div class="col-sm-8 fw-bold text-primary fs-5 text-end">{{ number_format($invoice->FinalAmount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-light p-3 rounded text-center mt-2 mt-lg-0">
                            <div class="text-muted mb-2">Final Amount</div>
                            <div class="display-6 fw-bold text-primary">{{ number_format($invoice->FinalAmount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border rounded p-3">
                <h5 class="border-bottom pb-2 mb-3">Additional Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label class="text-muted mb-1">Comment:</label>
                            <div class="border rounded p-2 bg-light">{{ $invoice->comment ?: 'No comments available' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Invoice ID:</div>
                            <div class="col-7">{{ $invoice->InvoiceID }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Created By:</div>
                            <div class="col-7">{{ $user->name }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-muted">Created At:</div>
                            <div class="col-7">{{ date('M d, Y H:i', strtotime($invoice->CreateDateTime)) ?? 'N/A' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-muted">Last Updated:</div>
                            <div class="col-7">
                                @if($invoice->UpdateDateTime && $invoice->UpdateDateTime != '0000-00-00 00:00:00')
                                    {{ date('M d, Y H:i', strtotime($invoice->UpdateDateTime)) }}
                                @else
                                    {{ date('M d, Y H:i', strtotime($invoice->CreateDateTime)) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>
@endsection