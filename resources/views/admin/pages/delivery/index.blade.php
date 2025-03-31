@extends('layouts.main')
@section('content')
    <div class="table-container container-fluid mt-4">
        <div class="tab-container mb-3">
            <ul class="nav nav-tabs d-flex justify-content-between" id="invoiceTabs"
                style="
            overflow-y: unset;
            overflow-x: unset !important;!i;!;
        ">
                <div class="d-flex">
                    <!-- Left side tabs -->
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'withtipticket' ? 'active' : '' }}" id="with-tip-tab" data-toggle="tab"
                            href="{{ route('delivery.index', ['type' => 'withtipticket', 'invoice_type' => $invoice_type]) }}"
                            role="tab">With Tip Ticket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'withouttipticket' ? 'active' : '' }}" id="without-tip-tab"
                            data-toggle="tab"
                            href="{{ route('delivery.index', ['type' => 'withouttipticket', 'invoice_type' => $invoice_type]) }}"
                            role="tab">Without Tip Ticket</a>
                    </li>
                </div>

                <div class="d-flex">
                    <!-- Right side tabs -->
                    <li class="nav-item">
                        <a class="nav-link {{ $invoice_type === 'preinvoice' ? 'active' : '' }}" id="pre-invoice-tab"
                            data-toggle="tab"
                            href="{{ route('delivery.index', ['type' => $type, 'invoice_type' => 'preinvoice']) }}"
                            role="tab">Pre Invoice</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $invoice_type === 'readyinvoice' ? 'active' : '' }}" id="ready-invoice-tab"
                            data-toggle="tab"
                            href="{{ route('delivery.index', ['type' => $type, 'invoice_type' => 'readyinvoice']) }}"
                            role="tab">Ready Invoice</a>
                    </li>
                </div>
            </ul>

        </div>

        <div class="table-header">
            <h3 class="table-title text-center text-white">Delivery Invoices</h3>
        </div>

        <!-- Compact date filter row -->
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
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Booking ID"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Date">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Company"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Site">
                        </th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('delivery.data') }}",
                    data: function(d) {
                        d.type = '{{ request('type', 'withtipticket') }}';
                        d.invoice_type = '{{ request('invoice_type', 'preinvoice') }}';
                        d.start_date = $('#startDate').val(); // Get start date
                        d.end_date = $('#endDate').val(); // Get end date
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            // 🔹 Enable column search
            $('#invoiceTable thead .column-search').on('keyup change', function() {
                let colIndex = $(this).parent().index();  // Get column index
                table.column(colIndex).search(this.value).draw();
            });

            // Filter button click event
            $('#filterBtn').click(function() {
                table.ajax.reload(); // Reload table with new date filters
            });

            // Clear filters button
            $('#clearFilters').click(function() {
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
            position: relative;
        }

        .table-title {
            font-weight: 500;
            font-size: 1.2rem;
            margin: 0;
            color: white;
        }

        /* Compact date filter styling */
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
            transition: all 0.2s ease;
        }

        .search-row input:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
            outline: none;
        }

        #invoiceTable tbody tr {
            transition: background-color 0.2s ease;
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

        /* DataTables styling */
        div.dataTables_wrapper div.dataTables_length select {
            width: auto;
            padding: 4px 24px 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 8px;
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }

        div.dataTables_wrapper div.dataTables_filter input:focus,
        div.dataTables_wrapper div.dataTables_length select:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
            outline: none;
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

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button.active a:hover {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
            color: white;
        }

        .table-responsive {
            padding: 0 15px 15px;
        }

        /* Processing indicator */
        div.dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Action buttons styling */
        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            margin: 0 2px;
            font-size: 0.85rem;
        }

        /* Clear button hover effect */
        #clearFilters:hover {
            background-color: white;
            color: #3c8dbc;
        }
    </style>
@endsection
