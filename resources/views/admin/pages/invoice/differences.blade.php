@extends('layouts.main')

@section('content')


<style>
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
    }
    .badge-count {
        margin-left: 5px;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .text-primary {
        font-weight: 500;
    }
    .text-danger {
        font-weight: 500;
    }
    .list-unstyled li {
        margin-bottom: 5px;
    }
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 15px;
    }
    .status-badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 100px;
        justify-content: center;
    }
    .status-badge i {
        font-size: 14px;
    }
    .status-badge.pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .status-badge.reviewed {
        background-color: #cce5ff;
        color: #004085;
        border: 1px solid #b8daff;
    }
    .status-badge.resolved,
    .status-badge.perfect {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .date-range-filter {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .date-range-filter input {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .date-range-filter button {
        padding: 5px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .date-range-filter button:hover {
        background-color: #0056b3;
    }

    .table-container {
        background-color: white;
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 0;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .table-header {
        background-color: #3c8dbc;
        padding: 12px 15px;
    }

    .table-title {
        font-weight: 500;
        font-size: 1.2rem;
        margin: 0;
        color: white;
    }

    .date-filter-container {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .date-range-compact input[type="date"] {
        width: 140px;
        height: 32px;
        font-size: 0.85rem;
    }

    .date-range-compact .btn {
        height: 32px;
        font-size: 0.85rem;
        padding: 0.25rem 0.75rem;
    }

    .table-responsive {
        padding: 0 15px 15px;
    }

    #invoiceTabs {
        overflow-x: hidden !important;
        overflow-y: hidden;
        display: flex;
        flex-wrap: wrap; /* changed from nowrap */
        width: 100%;
        border-bottom: 1px solid #dee2e6;
        margin: 0;
        padding: 0;
    }

    #invoiceTabs .nav-item {
        flex: 1 1 auto;
        min-width: 0;
    }

    #invoiceTabs .nav-link {
        white-space: nowrap;
        padding: 0.5rem 1rem;
        border: none;
        color: #555;
        text-align: center;
        width: 100%;
    }

    #invoiceTabs .nav-link.active {
        color: #3c8dbc;
        border-bottom: 2px solid #3c8dbc;
        background-color: transparent;
    }

    .search-row input {
        width: 100%;
        padding: 6px 8px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .search-row input:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
        outline: none;
    }

    .search-row th {
        padding: 8px 10px;
        background-color: white;
        border-top: none;
        border-bottom: 1px solid #e0e0e0;
    }

    thead tr.filters th {
        background-color: #f5f5f5;
        color: #333;
        font-weight: 600;
        padding: 12px 10px;
        border-bottom: 2px solid #e0e0e0;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin: 15px 0 0;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button a {
        padding: 6px 12px;
        margin: 0 3px;
        background-color: white;
        border: 1px solid #ddd;
        color: #333;
        border-radius: 4px;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button.active a {
        background-color: #3c8dbc;
        border-color: #3c8dbc;
        color: white;
    }

    div.dataTables_filter {
        display: none;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <div class="tab-container mb-3">
                        <ul class="nav nav-tabs justify-content-end" id="invoiceTabs" role="tablist">

                            <li class="nav-item">
                                <a class="nav-link active" id="differences-tab" data-toggle="tab" href="#differences" role="tab">
                                    Different Invoices
                                    <span class="badge badge-danger badge-count">{{ $differences->where('status', '!=', 'resolved')->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="perfect-tab" data-toggle="tab" href="#perfect" role="tab">
                                    Matched Invoices
                                    <span class="badge badge-success badge-count">{{ $perfectInvoices->count() }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab panes -->
                    <div class="tab-content mt-4">
                        <!-- Differences Tab -->
                        <div class="tab-pane fade show active" id="differences" role="tabpanel">
                            <div class="table-header">
                                <h3 class="table-title text-center text-white">Invoice Comparison Results</h3>
                            </div>

                            <!-- Compact date filter -->
                            <div class="date-filter-container p-2 bg-light border-bottom">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="date-range-compact d-flex align-items-center">
                                        <span class="mr-2">Date: &nbsp;</span>
                                        <input type="date" id="startDate" class="form-control form-control-sm mr-1" placeholder="Start">
                                        <span class="mx-1">to</span>
                                        <input type="date" id="endDate" class="form-control form-control-sm mr-2" placeholder="End">
                                        <button id="filterBtn" class="btn btn-sm btn-primary">Filter</button>
                                        <button id="clearFilters" class="btn btn-sm btn-outline-secondary ml-1">Clear</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive px-3">
                                <table id="differencesTable" class="table table-hover">
                                    <thead>
                                        <tr class="filters">
                                            <th>Invoice Number</th>
                                            <th>Basic Details</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                        <tr class="search-row">
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Invoice"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Details"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Items"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Status"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Notes"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Date"></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($differences as $difference)
                                        <tr>
                                            <td class="font-weight-bold">{{ $difference->invoice_number }}</td>
                                            <td>
                                                @if($difference->basic_details_differences)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($difference->basic_details_differences as $field => $values)
                                                            <li>
                                                                <strong>{{ $field }}:</strong><br>
                                                                <span class="text-primary">Our: {{ number_format($values['our_system'], 2) }}</span><br>
                                                                <span class="text-danger">Sage: {{ number_format($values['sage'], 2) }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-success">No differences</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($difference->items_differences)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($difference->items_differences as $description => $values)
                                                            <li>
                                                                <strong>{{ $description }}:</strong><br>
                                                                <span class="text-primary">Our: {{ $values['our_system'] }}</span><br>
                                                                <span class="text-danger">Sage: {{ $values['sage'] }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-success">No differences</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="status-badge {{ $difference->status }}">
                                                    @if($difference->status === 'pending')
                                                        <i class="fas fa-clock"></i> Pending
                                                    @elseif($difference->status === 'reviewed')
                                                        <i class="fas fa-eye"></i> Reviewed
                                                    @else
                                                        <i class="fas fa-check-circle"></i> Resolved
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $difference->resolution_notes }}</td>
                                            <td>{{ $difference->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary update-status"
                                                        data-id="{{ $difference->id }}"
                                                        data-status="{{ $difference->status }}"
                                                        data-notes="{{ $difference->resolution_notes }}">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Perfect Invoices Tab -->
                        <div class="tab-pane fade" id="perfect" role="tabpanel">
                            <div class="table-header">
                                <h3 class="table-title text-center text-white">Matched Invoices</h3>
                            </div>

                            <!-- Compact date filter -->
                            <div class="date-filter-container p-2 bg-light border-bottom">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="date-range-compact d-flex align-items-center">
                                        <span class="mr-2">Date: &nbsp;</span>
                                        <input type="date" id="startDatePerfect" class="form-control form-control-sm mr-1" placeholder="Start">
                                        <span class="mx-1">to</span>
                                        <input type="date" id="endDatePerfect" class="form-control form-control-sm mr-2" placeholder="End">
                                        <button id="filterDatePerfect" class="btn btn-sm btn-primary">Filter</button>
                                        <button id="clearFilterPerfect" class="btn btn-sm btn-outline-secondary ml-1">Clear</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive px-3">
                                <table id="perfectTable" class="table table-hover">
                                    <thead>
                                        <tr class="filters">
                                            <th>Invoice Number</th>
                                            <th>Company Name</th>
                                            <th>Total Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                        <tr class="search-row">
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Invoice"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Company"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Amount"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Date"></th>
                                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Status"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($perfectInvoices as $invoice)
                                        <tr>
                                            <td class="font-weight-bold">{{ $invoice->InvoiceNumber }}</td>
                                            <td>{{ $invoice->CompanyName }}</td>
                                            <td class="text-right">{{ number_format($invoice->FinalAmount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <span class="status-badge perfect">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Difference Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="differenceId">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="resolution_notes">Resolution Notes</label>
                        <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveStatus">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var differencesTable = $('#differencesTable').DataTable({
            orderCellsTop: true,
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100],
            order: [[5, 'desc']],
            responsive: true,
            searching: true,
            lengthChange: false
        });
    
        var perfectTable = $('#perfectTable').DataTable({
            orderCellsTop: true,
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100],
            order: [[3, 'desc']],
            responsive: true,
            searching: true,
            lengthChange: false
        });
    
        $('#perfect-tab').click(function(e) {
            e.preventDefault();
            $('#invoiceTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            $('.tab-pane').removeClass('show active');
            $('#perfect').addClass('show active');
        });
    
        $('#differences-tab').click(function(e) {
            e.preventDefault();
            $('#invoiceTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            $('.tab-pane').removeClass('show active');
            $('#differences').addClass('show active');
        });
    
        $('#differencesTable .column-search').on('keyup change', function() {
            differencesTable
                .column($(this).closest('th').index())
                .search(this.value)
                .draw();
        });
    
        $('#perfectTable .column-search').on('keyup change', function() {
            perfectTable
                .column($(this).closest('th').index())
                .search(this.value)
                .draw();
        });
    
        $('#filterBtn').click(function() {
            differencesTable.draw();
        });
    
        $('#clearFilters').click(function() {
            $('#startDate').val('');
            $('#endDate').val('');
            differencesTable.draw();
        });
    
        $('#filterDatePerfect').click(function() {
            perfectTable.draw();
        });
    
        $('#clearFilterPerfect').click(function() {
            $('#startDatePerfect').val('');
            $('#endDatePerfect').val('');
            perfectTable.draw();
        });
    
        $('.update-status').click(function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var notes = $(this).data('notes');
    
            $('#differenceId').val(id);
            $('#status').val(status);
            $('#resolution_notes').val(notes);
            $('#statusModal').modal('show');
        });
    
        $('#saveStatus').click(function() {
            $.ajax({
                url: 'invoice-differences/' + $('#differenceId').val() + '/update-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: $('#status').val(),
                    resolution_notes: $('#resolution_notes').val()
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        });
    });
    </script>
@endsection
