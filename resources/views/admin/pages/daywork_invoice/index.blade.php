@extends('layouts.main')
@section('content')
<!-- Add Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<div class="table-container mt-4">
    <div class="tab-container mb-3">
         <ul class="nav nav-tabs d-flex justify-content-between" id="invoiceTabs" style="overflow-x: unset;">
                        <div class="d-flex">
                             <li class="nav-item">
                            <a class="nav-link {{ !request()->type || request()->type === 'loads' ? 'active' : '' }}"
                            href="{{ route('daywork.index', [
                                    'type' => request()->type ?? 'loads',
                                    'invoice_type' => request()->invoice_type ?? 'preinvoice'
                            ]) }}">
                                Loads
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->update_type === 'tonnage' ? 'active' : '' }}"
                            href="{{ route('daywork.index', [
                                    'type' => request()->type ?? 'tonnage',
                                    'invoice_type' => request()->invoice_type ?? 'preinvoice'
                            ]) }}">
                                Tonnage
                            </a>
                        </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link {{ !request('type') || request('type') === 'withtipticket' ? 'active' : '' }} waves-effect"
                                href="{{ url('daywork-invoice-list') }}/withtipticket?update_type={{ request('update_type') }}&invoice_type={{ request('invoice_type') }}"
                                role="tab">With Tip Ticket</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') === 'withouttipticket' ? 'active' : '' }} waves-effect"
                                href="{{ url('daywork-invoice-list') }}/withouttipticket?update_type={{ request('update_type') }}&invoice_type={{ request('invoice_type') }}"
                                role="tab">Without Tip Ticket</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') === 'missingtipticket' ? 'active' : '' }} waves-effect"
                                href="{{ url('daywork-invoice-list') }}/missingtipticket?update_type={{ request('update_type') }}&invoice_type={{ request('invoice_type') }}"
                                role="tab">Missing Tip Ticket</a>
                            </li> -->
                        </div>

                        <div class="d-flex-new">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('invoice_type') || request('invoice_type') === 'preinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('daywork-invoice-list') }}/{{ request('type') ?? 'withtipticket' }}?invoice_type=preinvoice">
                                    Pre Invoice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('invoice_type') === 'readyinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('daywork-invoice-list') }}/{{ request('type') ?? 'withtipticket' }}?invoice_type=readyinvoice">
                                    Ready Invoice
                                </a>
                            </li>
                              <li class="nav-item">
                            <a class="nav-link {{ request('invoice_type') === 'holdinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('daywork-invoice-list') }}/{{ request('type') ?? 'withtipticket' }}?invoice_type=holdinvoice">
                                    Hold Invoice
                                </a>
                            </li>
                        </div>
                    </ul>
                     <!-- 2nd Row: Loads and Tonnage -->
                    <!-- <ul class="nav nav-tabs mt-2">
                        <li class="nav-item">
                            <a class="nav-link {{ !request()->update_type || request()->update_type === 'loads' ? 'active' : '' }}"
                            href="{{ route('daywork.index', [
                                    'type' => request()->type ?? 'withtipticket',
                                    'update_type' => 'loads',
                                    'invoice_type' => request()->invoice_type ?? 'preinvoice'
                            ]) }}">
                                Loads
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->update_type === 'tonnage' ? 'active' : '' }}"
                            href="{{ route('daywork.index', [
                                    'type' => request()->type ?? 'withtipticket',
                                    'update_type' => 'tonnage',
                                    'invoice_type' => request()->invoice_type ?? 'preinvoice'
                            ]) }}">
                                Tonnage
                            </a>
                        </li>
                    </ul> -->
        <!-- <ul class="nav nav-tabs justify-content-end flex-nowrap" id="invoiceTabs">
            <li class="nav-item">
                <a class="nav-link {{ $invoice_type === 'preinvoice' ? 'active' : '' }}" id="pre-invoice-tab"
                    href="{{ route('daywork.index', ['invoice_type' => 'preinvoice']) }}">Pre Invoice</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $invoice_type === 'readyinvoice' ? 'active' : '' }}" id="ready-invoice-tab"
                    href="{{ route('daywork.index', ['invoice_type' => 'readyinvoice']) }}">Ready Invoice</a>
            </li>
        </ul> -->
    </div>

    <div class="table-header">
        <h3 class="table-title text-center text-white">Daywork Invoices</h3>
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

    <div class="table-responsive">
        <table id="invoiceTable" class="table table-hover">
            <thead>
                <tr class="filters">
                    <!-- <th>Booking ID</th>
                    <th>Booking Date</th> -->
                        <th>Company Name</th>
                        <th>Site Name</th>
                        <th>Material name</th>
                        <!-- <th>On Hold</th> -->
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
                        <!-- <th><input type="text" class="column-ntrol form-control-sm column-search" placeholder="Search hold" /></th> -->
                        <th></th>
                    </tr>
            </thead>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function () {
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

        var table = $('#invoiceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('daywork.data') }}",
                data: function (d) {
                    // d.invoice_type = '{{ request('invoice_type', 'preinvoice') }}';
                    // d.start_date = $('#startDate').val();
                    // d.end_date = $('#endDate').val();
                    //d.type = '{{ request('type', 'withtipticket') }}';
                    d.type = '{{ request('type', 'load') }}';
                    d.invoice_type = '{{ request('invoice_type', 'preinvoice') }}';
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            // columns: [
            //     { data: 'BookingRequestID', name: 'BookingRequestID' },
            //     { data: 'CreateDateTime', name: 'CreateDateTime' },
            //     { data: 'CompanyName', name: 'CompanyName' },
            //     { data: 'OpportunityName', name: 'OpportunityName' },
            //      { data: 'InvoiceHold', name: 'InvoiceHold'},
            //     { data: 'action', name: 'action', orderable: false, searchable: false }
            // ],
             columns: [
            {
                            data: 'CompanyName',
                            name: 'CompanyName'
                        },
                        {
                            data: 'OpportunityName',
                            name: 'OpportunityName'
                        },
                        {
                            data: 'MaterialName',
                            name: 'MaterialName'
                        },
                        // {
                        //     data: 'InvoiceHold',
                        //     name: 'InvoiceHold'
                        // },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
        ],
            order: [[2, 'desc']],
            pageLength: 100,
            lengthMenu: [1, 10, 50, 100],
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

          // Column search on input change
    $('#invoiceTable thead').on('input', '.column-search', function () {
        var colIndex = $(this).parent().index();
        var val = $(this).val();
        table.column(colIndex).search(val).draw();
    });
    // Global search on button click
    $('#globalSearchBtn').on('click', function () {
        let searchTerm = $('#globalSearchInput').val();
        table.search(searchTerm).draw();
    });
    

    // Trigger global search on input clear (real-time behavior)
$('#globalSearchInput').on('input', function () {
    const searchTerm = $(this).val().trim();

    // If input is cleared, reset the DataTable search
    if (searchTerm === '') {
        table.search('').draw();
    }
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
    /* Add these styles for Flatpickr customization
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
    } */
            /* Make the month name and year color match selected day color */
.flatpickr-current-month,
.flatpickr-current-month .cur-month,
.flatpickr-current-month input.cur-year {
    color:rgb(0, 0, 0) !important;
    font-weight: 600;
}
.d-flex-new {
    display: flex !important;
    margin-right: 10px;
   
}
div.dataTables_processing {
    position: absolute;
    top: 5%!important;
    left: 50%;
    width: 200px;
    margin-left: -100px;
    margin-top: -26px;
    text-align: center;
    padding: 2px;
    z-index: 10;
}

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
@endsection
