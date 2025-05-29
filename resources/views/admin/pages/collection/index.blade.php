@extends('layouts.main')
@section('content')
    <!-- Add Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <div class="table-container mt-4">
        <!-- Back Button -->
        <div class="d-flex align-items-center mb-3">
            <a href="{{ url()->previous() }}" class="text-decoration-none text-dark">
                <i class="fas fa-arrow-left me-2"></i>
            </a>
            <h4 class="m-0">Collection Invoices</h4>
        </div>

        <!-- Tabs with updated styling -->
        <div class="tab-container mb-3">
            <ul class="nav nav-tabs justify-content-between flex-wrap w-100" id="invoiceTabs">
                <!-- Left Tabs -->
                <li class="nav-item">
                    <a class="nav-link {{ !request()->has('type') || request()->type === 'loads' ? 'active' : '' }}"
                        href="{{ route('collection.index', ['type' => 'loads', 'invoice_type' => request()->invoice_type ?? 'preinvoice']) }}">
                        Loads
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->type === 'tonnage' ? 'active' : '' }}"
                        href="{{ route('collection.index', ['type' => 'tonnage', 'invoice_type' => request()->invoice_type ?? 'preinvoice']) }}">
                        Tonnage
                    </a>
                </li>

                <!-- Right Tabs (separated visually using ms-auto) -->
                <li class="nav-item ms-auto">
                    <a class="nav-link {{ !request()->has('invoice_type') || request()->invoice_type === 'preinvoice' ? 'active' : '' }}"
                        href="{{ route('collection.index', ['type' => request()->type ?? 'loads', 'invoice_type' => 'preinvoice']) }}">
                        Pre Invoice
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->invoice_type === 'readyinvoice' ? 'active' : '' }}"
                        href="{{ route('collection.index', ['type' => request()->type ?? 'loads', 'invoice_type' => 'readyinvoice']) }}">
                        Ready Invoice
                    </a>
                </li>
            </ul>
        </div>

        <style>
            .nav-tabs {
                border-bottom: none;
                width: auto;
            }
            .nav-tabs .nav-link {
                color: #495057;
                background: none;
                border: none;
                margin-right: 4px;
                padding: 8px 16px;
                position: relative;
                transition: all 0.2s ease-in-out;
            }
            .nav-tabs .nav-link:hover {
                border: none;
                color: #3c8dbc;
            }
            .nav-tabs .nav-link.active {
                color: #3c8dbc;
                background: none;
                border: none;
                font-weight: 500;
            }
            .nav-tabs .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 2px;
                background-color: #3c8dbc;
            }
            .nav-tabs .nav-item.ms-auto .nav-link {
                margin-left: 4px;
                margin-right: 0;
            }
            .tab-container {
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
                padding-bottom: 2px;
            }

            .nav-tabs {
            flex-wrap: nowrap !important;
            overflow: hidden;
            }

            .nav-tabs .nav-item {
            flex-shrink: 0;
            }

        </style>

        <!-- Header -->
        <div class="table-header">
            <h3 class="table-title text-center text-white">Collection Invoices</h3>
        </div>

        <!-- Custom Global Search + Date Range Row -->
<div class="row px-3 py-2 bg-light align-items-center">
    <!-- Global Search -->
    <div class="col-md-6 d-flex align-items-center">
        <input type="text" id="globalSearchInput" class="form-control form-control-sm mr-2" placeholder="Search all data">
        <button id="globalSearchBtn" class="btn btn-sm btn-primary ms-2">Search</button>
    </div>

    <!-- Date Range Filter -->
    <div class="col-md-6 d-flex align-items-center justify-content-end">
        <span class="mr-2">Date: </span>
        <input type="date" id="startDate" class="form-control form-control-sm mr-1" placeholder="Start Date">
        <span class="mx-1">to</span>
        <input type="date" id="endDate" class="form-control form-control-sm mr-2" placeholder="End Date">
        <button id="filterBtn" class="btn btn-sm btn-primary ms-2">Filter</button>
        <button id="clearFilters" class="btn btn-sm btn-outline-secondary ml-1 ms-2">Clear</button>
    </div>
</div>

        <!-- Filters -->
        <!-- <div class="date-filter-container p-2 bg-light border-bottom">
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
        </div> -->

        <!-- Table -->
        <div class="table-responsive">
            <table id="invoiceTable" class="table table-hover">
                <thead>
                    <tr class="filters">
                        <th>Booking ID</th>
                        <th>Booking Date</th>
                        <th>Company Name</th>
                        <th>Site Name</th>
                        <th>On Hold</th>
                        <th>Action</th>
                    </tr>
                    <tr class="search-row">
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Booking ID"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Date"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Company"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Site"></th>
                         <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search hold" /></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            // Initialize Flatpickr for date inputs
            flatpickr("#startDate", {
                dateFormat: "Y-m-d",
                allowInput: true,
                clickOpens: true,
                enableTime: false,
                placeholder: "Start Date"
            });

            flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                allowInput: true,
                clickOpens: true,
                enableTime: false,
                placeholder: "End Date"
            });

            var table;

            function initializeDataTable(type, invoiceType) {
                table = $('#invoiceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('collection.data') }}",
                        data: function(d) {
                            d.type = "{{ $type }}";
                            d.invoice_type = "{{ $invoice_type }}";
                            d.start_date = $('#startDate').val();
                            d.end_date = $('#endDate').val();
                        }
                    },
                    columns: [{
                            data: 'BookingRequestID',
                            name: 'BookingRequestID'
                        },
                        {
                            data: 'CreateDateTime',
                            name: 'CreateDateTime'
                        },
                        {
                            data: 'CompanyName',
                            name: 'CompanyName'
                        },
                        {
                            data: 'OpportunityName',
                            name: 'OpportunityName'
                        },
                        {
                            data: 'InvoiceHold',
                            name: 'InvoiceHold'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [2, 'desc']
                    ],
                    pageLength: 100,
                    lengthMenu: [10, 25, 50, 100],
                    responsive: true,
                    orderCellsTop: true,
                    searching: true,
                    lengthChange: false,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search invoices...",
                        paginate: {
                            previous: "<i class='fas fa-chevron-left'></i>",
                            next: "<i class='fas fa-chevron-right'></i>"
                        }
                    }
                });
            }

            initializeDataTable("{{ $type }}", "{{ $invoice_type }}");

            $('#filterBtn').click(function() {
                table.ajax.reload();
            });

            $('#clearFilters').click(function() {
                $('#startDate').val('');
                $('#endDate').val('');
                table.ajax.reload();
            });

            $('#invoiceTable thead .column-search').on('keyup change', function() {
                let colIndex = $(this).parent().index();
                table.column(colIndex).search(this.value).draw();
            });
        });
    </script>

    <!-- CSS -->
    <style>
              #globalSearchInput {
    width: 80%;
    height: 40px;
}

        #globalSearchBtn{
            width:140px;
            height:40px;
        }

        #clearFilters{
            width:140px;
             height:40px;
        }
        #endDate{
             width: 40%;
            height: 40px;
        }
        #startDate{
             width: 40%;
            height: 40px;
        }

        #filterBtn{
            width:140px;
             height:40px;
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

        #invoiceTable {
            margin-bottom: 0;
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

        #invoiceTable tbody tr:hover {
            background-color: rgba(60, 141, 188, 0.05);
        }

        #invoiceTable tbody td {
            padding: 10px;
            vertical-align: middle;
            color: #333;
            border-color: #f0f0f0;
        }

        .table-responsive {
            padding: 0 15px 15px;
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

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button a:hover {
            background-color: #f0f0f0;
            border-color: #ddd;
        }

        #clearFilters:hover {
            background-color: white;
            color: #3c8dbc;
        }

        div.dataTables_filter {
            display: none;
        }

        div.dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            margin: 0 2px;
            font-size: 0.85rem;
        }

        /* Add these styles for Flatpickr customization */
        .flatpickr-calendar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .flatpickr-day.selected {
            background: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }

        .flatpickr-day:hover {
            background: #e6f3f9;
        }

        .flatpickr-current-month {
            color: #3c8dbc;
        }
    </style>
@endsection
