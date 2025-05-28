@extends('layouts.main')
@section('content')
<div class="table-container mt-4">
    <div class="tab-container mb-3">
        <ul class="nav nav-tabs justify-content-end flex-nowrap" id="invoiceTabs">
            <li class="nav-item">
                <a class="nav-link {{ $invoice_type === 'preinvoice' ? 'active' : '' }}" id="pre-invoice-tab"
                    href="{{ route('daywork.index', ['invoice_type' => 'preinvoice']) }}">Pre Invoice</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $invoice_type === 'readyinvoice' ? 'active' : '' }}" id="ready-invoice-tab"
                    href="{{ route('daywork.index', ['invoice_type' => 'readyinvoice']) }}">Ready Invoice</a>
            </li>
        </ul>
    </div>

    <div class="table-header">
        <h3 class="table-title text-center text-white">Daywork Invoices</h3>
    </div>

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

    <div class="table-responsive">
        <table id="invoiceTable" class="table table-hover">
            <thead>
                <tr class="filters">
                    <th>SR. No</th>
                    <th>Booking ID</th>
                    <th>Booking Date</th>
                    <th>Company Name</th>
                    <th>Site Name</th>
                    <th>Action</th>
                </tr>
                <tr class="search-row">
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Booking ID"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Date"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Company"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Site"></th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        var table = $('#invoiceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('daywork.data') }}",
                data: function (d) {
                    d.invoice_type = '{{ request('invoice_type', 'preinvoice') }}';
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'BookingRequestID', name: 'BookingRequestID' },
                { data: 'CreateDateTime', name: 'CreateDateTime' },
                { data: 'CompanyName', name: 'CompanyName' },
                { data: 'OpportunityName', name: 'OpportunityName' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[2, 'desc']],
            pageLength: 100,
            lengthMenu: [1, 10, 50, 100],
            responsive: true,
            orderCellsTop: true,
            searching: false,
            lengthChange: false,
        });

        $('#filterBtn').click(function () {
            table.ajax.reload();
        });

        $('#clearFilters').click(function () {
            $('#startDate').val('');
            $('#endDate').val('');
            table.ajax.reload();
        });
    });
</script>

<style>
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

    #invoiceTable thead tr.filters th {
        background-color: #f5f5f5;
        color: #333;
        font-weight: 600;
        padding: 12px 10px;
        border-bottom: 2px solid #e0e0e0;
    }

    .search-row th {
        padding: 8px 10px;
        background-color: white;
        border-top: none;
        border-bottom: 1px solid #e0e0e0;
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

    #invoiceTabs {
        overflow-x: hidden !important;
        overflow-y: hidden;
        display: flex;
        flex-wrap: nowrap;
        width: 100%;
        border-bottom: 1px solid #dee2e6;
    }

    #invoiceTabs .nav-item {
        flex-shrink: 1;
        min-width: 0;
    }

    #invoiceTabs .nav-link {
        white-space: nowrap;
        padding: 0.5rem 1rem;
        border: none;
        color: #555;
    }

    #invoiceTabs .nav-link.active {
        color: #3c8dbc;
        border-bottom: 2px solid #3c8dbc;
        background-color: transparent;
    }

    #invoiceTabs::-webkit-scrollbar {
        display: none;
    }

    #invoiceTabs {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection
