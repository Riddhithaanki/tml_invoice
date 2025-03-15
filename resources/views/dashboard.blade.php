@extends('layouts.main')

@section('content')
    <style>
        /* Compact styling with #3c8dbc color theme */
        .dashboard-container {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            border: none;
            height: 100%;
            background: white;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 1.8rem;
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
    </style>

    <div class="container-fluid px-2 py-1">
        <div class="dashboard-container">
            <!-- Header -->
            <div class="row mb-2">
                <div class="col-12">
                    <h4 style="color: #3c8dbc; font-weight: 600;">Dashboard Overview</h4>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="row">
                <!-- Ready Invoice -->
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card d-flex align-items-center justify-content-between border p-3 rounded">
                        <div class="card-body">
                            <div class="card-title">Ready Invoice</div>
                            <div class="card-value">{{ $readyHoldInvoiceCount }}</div>
                        </div>
                        <div class="icon-box border rounded-circle p-2">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 2H16L20 6V21C20 21.55 19.55 22 19 22H5C4.45 22 4 21.55 4 21V3C4 2.45 4.45 2 5 2H8ZM14 3H10V7H14V3ZM6 20H18V8H6V20ZM12 12H7V14H12V12ZM17 16H7V18H17V16Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Invoice -->
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card d-flex align-items-center justify-content-between border p-3 rounded">
                        <div class="card-body">
                            <div class="card-title">Completed Invoice</div>
                            <div class="card-value">{{ $completedInvoice }}</div>
                        </div>
                        <div class="icon-box border rounded-circle p-2">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="black" stroke-width="2" fill="none"/>
                                <path d="M9 12l2 2 4-4" stroke="black" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Recent Invoices Table -->
            <div class="table-container">
                <div class="table-header">
                    <h5 class="table-title text-center text-white">Recent Invoices</h5>
                </div>
                <div class="table-responsive">
                    <table id="invoiceTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking ID</th>
                                <th>Date</th>
                                <th>Company</th>
                                <th>Site</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentInvoice as $key => $invoice)
                                <tr>
                                    <td><strong>{{ $key + 1 }}</strong></td>
                                    <td>{{ $invoice->BookingRequestID ?? 'N/A' }}</td>
                                    <td>{{ $invoice->CreateDateTime ??  'N/A' }}</td>
                                    <td>{{ $invoice->CompanyName ?? 'N/A' }}</td>
                                    <td>{{ $invoice->OpportunityName ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('invoice.show',Crypt::encrypt($invoice->BookingRequestID)) }}" class="btn btn-xs btn-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
            $('#invoiceTable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    paginate: {
                        previous: "<i class='fas fa-chevron-left'></i>",
                        next: "<i class='fas fa-chevron-right'></i>"
                    }
                },
                lengthMenu: [5, 10, 25],
                pageLength: 5,
                order: [[0, 'desc']]
            });
        });
    </script>
@endsection
