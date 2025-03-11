@extends('layouts.main')
@section('content')
    <div class="table-container mt-4">
        <div class="table-header">
            <h3 class="table-title text-center text-white">Daywork Invoices</h3>
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
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Date">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Company"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Search Site">
                        </th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Initialize DataTable with individual column searching
            var table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('daywork.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // SR. No
                    { data: 'BookingRequestID', name: 'BookingRequestID' },
                    { data: 'CreateDateTime', name: 'CreateDateTime' },
                    { data: 'CompanyName', name: 'CompanyName' },
                    { data: 'OpportunityName', name: 'OpportunityName' },
                    { data: 'action', name: 'action', orderable: false, searchable: false } // Action buttons
                ],
                order: [[2, 'desc']], // Sort by Booking Date by default
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                responsive: true,
                orderCellsTop: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search invoices...",
                    paginate: {
                        previous: "<i class='fas fa-chevron-left'></i>",
                        next: "<i class='fas fa-chevron-right'></i>"
                    }
                }
            });

            // Apply search to each column
            $('.column-search').on('keyup change', function () {
                var colIndex = $(this).closest('th').index();
                table
                    .column(colIndex)
                    .search(this.value)
                    .draw();
            });

            // Add clear filters button
            $('.table-header').append(
                '<button id="clearFilters" class="btn btn-sm btn-outline-light position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%);">' +
                '<i class="fas fa-times"></i> Clear</button>'
            );

            $('#clearFilters').on('click', function () {
                $('.column-search').val('');
                table.search('').columns().search('').draw();
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
