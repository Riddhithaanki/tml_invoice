@extends('layouts.main')

@section('content')
    <style>
        /* Compact styling with #3c8dbc color theme */


        .dashboard-container {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 1rem;
            margin: 10px 0;
            padding: 10px;
            margin-right: 0;
            margin-left: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            border: none;
            height: 100%;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1.5rem !important;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card .card-body {
            text-align: center;
            width: 100%;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 1rem !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
            color: #3c8dbc;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-top: 1rem;
            margin-right: 0;
        }

        .table-header {
            background: #3c8dbc;
            color: white;
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .table-title {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }

        .table th {
            background-color: #f2f4f8;
            color: #333;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.6rem;
            white-space: nowrap;
        }

        .table td {
            padding: 0.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f2f2f2;
            font-size: 0.85rem;
        }

        .btn-primary {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
        }

        .btn-primary:hover {
            background-color: #367fa9;
            border-color: #367fa9;
        }

        /* DataTables customization */
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 0.25rem 0.5rem;
        }

        div.dataTables_length select {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 0.25rem;
        }

        .page-item.active .page-link {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
        }

        /*  Add these styles for the search inputs */
        .search-row input {
            width: 100%;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 0.85rem;
        }

        .search-row input:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
            outline: none;
        }

        .search-row th {
            padding: 8px;
            background-color: white;
            border-top: none;
        }

        .column-search {
            min-width: 100px;
            max-width: 150px;

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
</style>
    </style>

    <div class="dashboard-container p-0">
        <!-- Header -->
        <div class="row mb-2 mx-2">
            <div class="col-12">
                <h4 style="color: #3c8dbc; font-weight: 600;">Dashboard Overview</h4>
            </div>
        </div>

        <div class="container-fluid p-0">
            <!-- Stat Cards -->
            <div class="row mx-2">
                <!-- New Added Invoice -->
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="card-title">Invoice On Sage</div>
                            <div class="card-value">{{ $completedInvoice }}</div>
                        </div>
                    </div>
                </div>

                <!-- Invoices on Hold -->
                <div class="col-md-3 col-sm-6 mb-4">
                     <a class="nav-link {{ request('type') === 'invoicehold' ? 'active' : '' }} waves-effect"
                                href="{{ url('dashboard') }}/invoicehold"
                            style="text-decoration: none; color: inherit;">
                    <div class="stat-card">
                        <div class="card-body">
                           <div class="card-title">Invoices on Hold</div>
                            <div class="card-value">{{ $readyHoldInvoiceCount }}</div>
                        </div>
                    </div>
                    </a>
                </div>

                <!-- Ready Invoice -->
                <div class="col-md-3 col-sm-6 mb-4">
                    <a class="nav-link {{ request('type') === 'readyinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('dashboard') }}/readyinvoice"
                            style="text-decoration: none; color: inherit;">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="card-title">Ready Invoice</div>
                            <div class="card-value">{{ $readyInvoiceCount }}</div>
                        </div>
                    </div>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <a class="nav-link {{ request('type') === 'booking' ? 'active' : '' }} waves-effect"
                                href="{{ url('dashboard') }}/booking"
                            style="text-decoration: none; color: inherit;">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="card-title">Bookings</div>
                            <div class="card-value">{{ $bookingCount }}</div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>

            <!-- Recent Invoices Table -->
            <div class="table-container mx-2">
                <div class="table-header">
                    <h5 class="table-title text-center text-white">New Added Invoices</h5>
                </div>
            

                <div class="table-responsive">
                    <table id="invoiceTable" class="table table-hover">
                        <thead>
                            <tr>
                                <!-- <th>Booking Request ID</th>
                                <th>Date</th> -->
                        <th>Company Name</th>
                        <th>Site Name</th>
                        <th>Material name</th>
                         <th>Invoice type</th>
                        <th>Action</th>
                         </tr>
                           <tr class="search-row">
                       <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Company"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Site">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Material Name">
                        </th>
                         <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Invoice Type">
                        </th>
                       <th></th>
                    </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentInvoice as $key => $invoice)
                            @php  
                         
                            $materialName = optional($invoice->booking?->first())->MaterialName ?? $invoice->MaterialName ?? 'N/A';
                            $CompanyName = $invoice->CompanyName ?? optional($invoice->bookingRequest)->CompanyName;
                            $OpportunityName = $invoice->OpportunityName ?? optional($invoice->bookingRequest)->OpportunityName
                            @endphp
                            <tr>
                                   
                                    <td>{{ $invoice->CompanyName ??  $CompanyName }}</td>
                                    <td>{{ $invoice->OpportunityName ?? $OpportunityName }}</td>
                                     <td>{{ $materialName ?? 'N/A' }}</td>
                                     <td>
                                        @php 
                                        $firstBooking = $invoice->booking?->first(); 
                                        @endphp
                                        @if( isset($firstBooking) ||  isset($invoice->BookingType))
                                            @if(isset($firstBooking->BookingType) && $firstBooking->BookingType == 1 || $invoice->BookingType == 1)
                                                <span class="badge bg-success">Collection</span>
                                            @elseif(isset($firstBooking->BookingType) && $firstBooking->BookingType == 2 || $invoice->BookingType == 2)
                                                <span class="badge bg-warning">Delivery</span>
                                            @elseif(isset($firstBooking->BookingType) && $firstBooking->BookingType == 3 || $invoice->BookingType == 3)
                                                <span class="badge bg-warning">Daywork</span>
                                            @elseif(isset($firstBooking->BookingType) && $firstBooking->BookingType == 4 || $invoice->BookingType == 4)
                                                <span class="badge bg-warning">Haulage</span>
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif

                                     </td>
                                    <td>
                                      
                                        <!-- <a href="{{ route('invoice.show', Crypt::encrypt($invoice->BookingRequestID)) }}"
                                            class="btn btn-xs btn-primary">View</a> -->
                                        <a href="{{ route('invoice.show', [Crypt::encrypt($invoice->BookingRequestID),Crypt::encrypt( $materialName) ]) }}"
                                            class="btn btn-xs btn-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- DataTables -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

        <script>
            $(document).ready(function() {
                var table = $('#invoiceTable').DataTable({
                    responsive: true,
                    orderCellsTop: true,
                    lengthChange: false,
                    pageLength: 20,
                    order: [
                        [0, 'desc']
                    ],
                    searching: true,
                    language: {
                search: "_INPUT_",
                searchPlaceholder: "Search invoices...",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            }
                });

                // Apply column search
                $('.column-search').on('keyup change', function() {
                    table
                        .column($(this).closest('th').index())
                        .search(this.value)
                        .draw();
                });
            });

            // Global search on button click
            $('#globalSearchBtn').on('click', function() {
                let searchTerm = $('#globalSearchInput').val();
                table.search(searchTerm).draw();
            });
        </script>
    @endsection
