@extends('layouts.main')

@section('content')
    <style>
        /* Animation for welcome banner */
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-20px) translateX(-50%); }
            15% { opacity: 1; transform: translateY(0) translateX(-50%); }
            85% { opacity: 1; transform: translateY(0) translateX(-50%); }
            100% { opacity: 0; transform: translateY(-20px) translateX(-50%); }
        }

        .welcome-banner {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #1a237e, #3949ab);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            z-index: 1000;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            animation: fadeInOut 4s ease-in-out;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .dashboard-container {
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 15px;
            margin-top: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-card {
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
            height: 100%;
            background: white;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card .card-body {
            padding: 1.75rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #1a237e, #3949ab);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-trend {
            color: #4caf50;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-top: 2rem;
        }

        .table-header {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .table-title {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table th {
            background-color: #f2f4f8;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f2f2f2;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .icon-card {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #1a237e;
            opacity: 0.2;
            position: absolute;
            right: 1rem;
            top: 1rem;
        }
    </style>

    <div class="container-fluid px-4 py-2">
        <div class="dashboard-container">
            <div class="row mb-2">
                <div class="col-12">
                    <h2 class="mb-4" style="color: #333; font-weight: 700;">Dashboard Overview</h2>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="card-body">
                            {{-- <i class="fas fa-users icon-card"></i> --}}
                            <div class="card-title">Ready Invoice</div>
                            <div class="card-value">{{ $readyHoldInvoiceCount }}</div>
                            {{-- <div class="card-trend">
                                <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="card-body">
                            {{-- <i class="fas fa-chart-line icon-card"></i> --}}
                            <div class="card-title">Completed Invoice</div>
                            <div class="card-value">{{ $completedInvoice }}</div>
                            {{-- <div class="card-trend">
                                <i class="fas fa-arrow-up mr-1"></i> 8% from last month
                            </div> --}}
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-4">
                    <div class="stat-card">
                        <div class="card-body">
                            <i class="fas fa-file-invoice-dollar icon-card"></i>
                            <div class="card-title">Completed Jobs</div>
                            <div class="card-value">15</div>
                            <div class="card-trend">
                                <i class="fas fa-arrow-up mr-1"></i> 5% from last month
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            <div class="table-container mt-5">
                <div class="table-header">
                    <h3 class="table-title text-center text-white">Recent Invoices</h3>
                </div>
                <div class="table-responsive">
                    <table id="invoiceTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>SR. No</th>
                                <th>Booking ID</th>
                                <th>Booking Date</th>
                                <th>Company Name</th>
                                <th>Site Name</th>
                                {{-- <th>Type Of Job</th> --}}
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
                                    {{-- <td>
                                        @if ($invoice->booking && $invoice->booking->BookingType !== null)
                                            @if ($invoice->booking->BookingType == '1')
                                                <span class="badge bg-primary">Collection</span>
                                            @elseif($invoice->booking->BookingType == '2')
                                                <span class="badge bg-success">Delivery</span>
                                            @else
                                                <span class="badge bg-warning">Other</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        <a href="{{ route('invoice.show',Crypt::encrypt($invoice->BookingRequestID)) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- DataTables CSS and JS -->
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
                    searchPlaceholder: "Search invoices...",
                    paginate: {
                        previous: "<i class='fas fa-chevron-left'></i>",
                        next: "<i class='fas fa-chevron-right'></i>"
                    }
                },
                lengthMenu: [5, 10, 25, 50],
                pageLength: 5,
                order: [[0, 'desc']]
            });
        });
    </script>
@endsection
