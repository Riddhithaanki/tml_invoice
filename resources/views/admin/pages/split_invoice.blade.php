@extends('layouts.main')

@section('content')
    <style>
        /* Table styling */
        #invoiceTable tbody td {
            color: #000 !important;
            font-weight: 500;
        }

        #invoiceTable thead th {
            background-color: #e6f0ff;
            color: #000;
            font-weight: bold;
        }

        .table-container {
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px;
            margin-top: 20px;
        }

        .table-header {
            background-color: #3c8dbc;
            padding: 12px 15px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 10px;
            align-content: center;
        }

        .table-title {
            font-weight: 500;
            font-size: 1.2rem;
            color: white;
            margin: 0;
            text-align: center;
        }

        .table-responsive {
            overflow-x: auto;
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
    </style>

    <div class="container-fluid">
        <div class="table-container">
            <div class="table-header">
                <h4 class="table-title">Split Invoices</h4>
            </div>

            <div class="table-responsive">
                <table id="invoiceTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Conveyance Number</th>
                            <th>Booking ID</th>
                            <th>Site Name</th>
                            <th>Opportunity Name</th>
                            <th>Material Name</th>
                            {{-- <th>Job Type</th> --}}
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <th>{{ $invoice->ConveyanceNo ?? '-' }}</th>
                            <td>{{ $invoice->BookingID }}</td>
                            <td>{{ $invoice->CompanyName }}</td>
                            <td>{{ $invoice->OpportunityName }}</td>
                            <td>{{ $invoice->MaterialName ?? '-' }}</td>
                            {{-- <td>{{ $invoice->InvoiceType }}</td> --}}
                            <td>{{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d/m/Y H:i') }}</td>
                            <td>
                                {{-- <a href="{{ route('view.splitinvoice',Crypt::encrypt($invoice->BookingRequestID)) }}" class="btn btn-sm btn-primary">View</a> --}}
                                <a href="{{ route('view.splitinvoice',($invoice->BookingID)) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DataTables JS -->
    <script>
        $(document).ready(function() {
            $('#invoiceTable').DataTable({
                paging: true,
                searching: true,    // Global search enabled
                ordering: true,
                pageLength: 25,
                lengthChange: false,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search invoices...",
                    paginate: {
                        previous: "<i class='fas fa-chevron-left'></i>",
                        next: "<i class='fas fa-chevron-right'></i>"
                    }
                }
            });
        });
    </script>
@endsection
