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
    /* New status badge styles */
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
    .status-badge.resolved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-badge.perfect {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    /* Date range filter styles */
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
</style>
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Invoice Comparison Results</h3>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="invoiceTabs" role="tablist">
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

                    <!-- Tab panes -->
                    <div class="tab-content mt-4">
                        <!-- Differences Tab -->
                        <div class="tab-pane fade show active" id="differences" role="tabpanel">
                            <div class="table-responsive">
                                <div class="date-range-filter">
                                    <input type="date" id="startDate" class="form-control">
                                    <span>to</span>
                                    <input type="date" id="endDate" class="form-control">
                                    <button id="filterDate" class="btn btn-primary">Filter</button>
                                    <button id="clearFilter" class="btn btn-secondary">Clear</button>
                                </div>
                                <table class="table table-bordered table-hover" id="differencesTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Invoice Number</th>
                                            <th>Basic Details Differences</th>
                                            <th>Items Differences</th>
                                            <th>Status</th>
                                            <th>Resolution Notes</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
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
                                                                <span class="text-primary">Our System: {{ number_format($values['our_system'], 2) }}</span><br>
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
                                                                <span class="text-primary">Our System: {{ $values['our_system'] }}</span><br>
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
                                            <td>{{ $difference->created_at->format('Y-m-d H:i:s') }}</td>
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
                            <div class="table-responsive">
                                <div class="date-range-filter">
                                    <input type="date" id="startDatePerfect" class="form-control">
                                    <span>to</span>
                                    <input type="date" id="endDatePerfect" class="form-control">
                                    <button id="filterDatePerfect" class="btn btn-primary">Filter</button>
                                    <button id="clearFilterPerfect" class="btn btn-secondary">Clear</button>
                                </div>
                                <table class="table table-bordered table-hover" id="perfectTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Invoice Number</th>
                                            <th>Company Name</th>
                                            <th>Total Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($perfectInvoices as $invoice)
                                        <tr>
                                            <td class="font-weight-bold">{{ $invoice->InvoiceNumber }}</td>
                                            <td>{{ $invoice->CompanyName }}</td>
                                            <td class="text-right">{{ number_format($invoice->FinalAmount, 2) }}</td>
                                            <td>{{ $invoice->InvoiceDate }}</td>
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
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="differenceId">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="0">Pending</option>
                            <option value="1">Approved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="resolution_notes">Resolution Notes</label>
                        <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStatus">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables without export buttons
    $('#differencesTable').DataTable({
        order: [[5, 'desc']], // Sort by Created At by default
        pageLength: 10,
        responsive: true,
        language: {
            search: "Search in all columns:"
        }
    });

    $('#perfectTable').DataTable({
        order: [[3, 'desc']], // Sort by Date by default
        pageLength: 10,
        responsive: true,
        language: {
            search: "Search in all columns:"
        }
    });

    // Handle perfect invoice tab click
    $('#perfect-tab').click(function(e) {
        e.preventDefault();
        $('#invoiceTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('show active');
        $('#perfect').addClass('show active');
    });

    // Handle differences invoice tab click
    $('#differences-tab').click(function(e) {
        e.preventDefault();
        $('#invoiceTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('show active');
        $('#differences').addClass('show active');
    });

    // Date range filter for differences table
    $('#filterDate').click(function() {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        if (startDate && endDate) {
            var table = $('#differencesTable').DataTable();
            table.column(5).search(startDate + '|' + endDate, true, false).draw();
        }
    });

    $('#clearFilter').click(function() {
        $('#startDate').val('');
        $('#endDate').val('');
        var table = $('#differencesTable').DataTable();
        table.column(5).search('').draw();
    });

    // Date range filter for perfect invoices table
    $('#filterDatePerfect').click(function() {
        var startDate = $('#startDatePerfect').val();
        var endDate = $('#endDatePerfect').val();

        if (startDate && endDate) {
            var table = $('#perfectTable').DataTable();
            table.column(3).search(startDate + '|' + endDate, true, false).draw();
        }
    });

    $('#clearFilterPerfect').click(function() {
        $('#startDatePerfect').val('');
        $('#endDatePerfect').val('');
        var table = $('#perfectTable').DataTable();
        table.column(3).search('').draw();
    });

    // Status update modal
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
        var id = $('#differenceId').val();
        var status = $('#status').val();
        var notes = $('#resolution_notes').val();

        $.ajax({
            url: 'invoice-differences/' + id + '/update-status',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                resolution_notes: notes
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error updating status');
            }
        });
    });
});
</script>

@endsection
