@extends('customer.layouts.main')

@section('content')
    <div class="table-container mt-4">
        <div class="table-header">
            <h3 class="table-title text-center text-white">All Invoices</h3>
        </div>

        {{-- Date Filter --}}
        <div class="row justify-content-center mt-3 mb-3">
            <div class="col-md-3">
                <input type="date" id="start_date" class="form-control" placeholder="Start Date">
            </div>
            <div class="col-md-3">
                <input type="date" id="end_date" class="form-control" placeholder="End Date">
            </div>
            <div class="col-md-2">
                <button id="filter" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <button id="reset" class="btn btn-secondary w-100">Clear</button>
            </div>
        </div>

        {{-- Invoice Table --}}
        <div class="table-responsive">
            <table id="invoiceTable" class="table table-hover">
                <thead>
                    <tr class="filters">
                        <th>SR. No</th>
                        <th>Invoice No.</th>
                        <th>Ref No</th>
                        <th>Invoice Date</th>
                        <th>Company Name</th>
                        <th>Site Name</th>
                        <th>Ticket List</th>
                        <th>Tickets</th>
                        <th>Select All</th>
                    </tr>
                    <tr class="search-row">
                        <th></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Invoice No."></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Ref No">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Invoice Date"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Company Name">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Site Name">
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function loadTable(start_date = '', end_date = '') {
                table = $('#invoiceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('customer.invoice.data') }}",
                        data: { start_date: start_date, end_date: end_date }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'id', name: 'id', orderable: false, searchable: false },
                        { data: 'reference', name: 'reference' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'CompanyName', name: 'CompanyName' },
                        { data: 'billing_address', name: 'billing_address' },
                        { data: 'ticket_list', name: 'ticket_list', orderable: false, searchable: false },
                        { data: 'tickets', name: 'tickets', orderable: false, searchable: false },
                        { data: 'select_all', name: 'select_all', orderable: false, searchable: false }
                    ],
                    order: [[2, 'desc']],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    responsive: true,
                });
            }

            loadTable(); // Initial load

            $('#filter').click(function () {
                let start = $('#start_date').val();
                let end = $('#end_date').val();
                loadTable(start, end);
            });

            $('#reset').click(function () {
                $('#start_date').val('');
                $('#end_date').val('');
                loadTable();
            });

             // 🔹 Enable column search
             $('#invoiceTable thead .column-search').on('keyup change', function() {
                let colIndex = $(this).parent().index();  // Get column index
                table.column(colIndex).search(this.value).draw();
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

        div.dataTables_wrapper div.dataTables_filter input,
        #globalSearch {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        div.dataTables_wrapper div.dataTables_filter input:focus,
        #globalSearch:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
            outline: none;
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
    </style>
@endsection
