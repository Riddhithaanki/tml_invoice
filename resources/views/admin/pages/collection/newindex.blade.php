@extends('layouts.main')
@section('content')
    <!-- Add Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Optional: Add a theme if you want -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <div class="table-container container-fluid mt-4">

      <!------ Old Code  -->
        <!-- <div class="tab-container mb-3">
            <ul class="nav nav-tabs d-flex justify-content-between" id="invoiceTabs"
                style="
            overflow-y: unset;
            overflow-x: unset !important;!i;!;
        "> -->
                <!-- <div class="d-flex">
                    <
                    <li class="nav-item">
                         a class="nav-link {{ request()->type === 'loads'  ? 'active' : '' }}"
                        href="{{ route('collection.newindex', ['type' => 'loads', 'invoice_type' => request()->invoice_type ?? 'preinvoice']) }}">
   Loads
</a> -->
 <!-- <li class="nav-item">
                         <a class="nav-link {{ request()->type === 'tonnage' ? 'active' : '' }}"
   href="{{ route('collection.newindex', ['type' => 'tonnage', 'invoice_type' => request()->invoice_type ?? 'preinvoice']) }}">
   Tonnage
</a> 
  <a class="nav-link {{  !request()->has('withtipticket') ||  request()->type === 'withtipticket' ? 'active' : '' }}" id="with-tip-tab" data-toggle="tab"
                            href="{{ route('collection.newindex', ['type' => 'withtipticket', 'invoice_type' => $invoice_type]) }}"
                            role="tab">With Tip Ticket</a>
                    </li>
                      <li class="nav-item">
                    <a class="nav-link {{  request()->type === 'withouttipticket' ? 'active' : '' }}" id="without-tip-tab"
                            data-toggle="tab"
                            href="{{ route('collection.newindex', ['type' => 'withouttipticket', 'invoice_type' => $invoice_type]) }}"
                            role="tab">Without Tip Ticket</a> 
                   
                    </li>
                      <li class="nav-item">
                        
                    <a class="nav-link {{ request()->type === 'missingtipticket' ? 'active' : '' }}" id="missing-tip-tab"
                            data-toggle="tab"
                            href="{{ route('collection.newindex', ['type' => 'missingtipticket', 'invoice_type' => $invoice_type]) }}"
                            role="tab">Missing Tip Ticket</a> 
                   
                    </li>
                </div>

                <div class="d-flex">
                   
                    <li class="nav-item">
                          <a class="nav-link {{ !request()->has('invoice_type') || request()->invoice_type === 'preinvoice' ? 'active' : '' }}"
                        href="{{ route('collection.newindex', ['type' => request()->type ?? 'loads', 'invoice_type' => 'preinvoice']) }}">
                        Pre Invoice
                    </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->invoice_type === 'readyinvoice' ? 'active' : '' }}"
                        href="{{ route('collection.newindex', ['type' => request()->type ?? 'loads', 'invoice_type' => 'readyinvoice']) }}">
                        Ready Invoice
                    </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->invoice_type === 'readyinvoice' ? 'active' : '' }}"
                        href="{{ route('collection.newindex', ['type' => request()->type ?? 'loads', 'invoice_type' => 'readyinvoice']) }}">
                        Hold Invoice
                    </a>
                    </li>
                </div>
            </ul>

        </div> -->
        

        <!------- //////////  New Update code ////////   -->
        <div class="tab-container mb-3">
                                <ul class="nav nav-tabs d-flex justify-content-between" id="invoiceTabs" style="overflow-x: unset;">
                        <div class="d-flex">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('type') || request('type') === 'withtipticket' ? 'active' : '' }} waves-effect"
                                href="{{ url('collection-newinvoice-list') }}/withtipticket?update_type={{ request('update_type') }}&invoice_type={{ request('invoice_type') }}"
                                role="tab">With Tip Ticket</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') === 'withouttipticket' ? 'active' : '' }} waves-effect"
                                href="{{ url('collection-newinvoice-list') }}/withouttipticket?update_type={{ request('update_type') }}&invoice_type={{ request('invoice_type') }}"
                                role="tab">Without Tip Ticket</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') === 'missingtipticket' ? 'active' : '' }} waves-effect"
                                href="{{ url('collection-newinvoice-list') }}/missingtipticket?update_type={{ request('update_type') }}&invoice_type={{ request('invoice_type') }}"
                                role="tab">Missing Tip Ticket</a>
                            </li>
                        </div>

                        <div class="d-flex-new">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('invoice_type') || request('invoice_type') === 'preinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('collection-newinvoice-list') }}/{{ request('type') ?? 'withtipticket' }}?invoice_type=preinvoice">
                                    Pre Invoice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('invoice_type') === 'readyinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('collection-newinvoice-list') }}/{{ request('type') ?? 'withtipticket' }}?invoice_type=readyinvoice">
                                    Ready Invoice
                                </a>
                            </li>
                              <li class="nav-item">
                            <a class="nav-link {{ request('invoice_type') === 'holdinvoice' ? 'active' : '' }} waves-effect"
                                href="{{ url('collection-newinvoice-list') }}/{{ request('type') ?? 'withtipticket' }}?invoice_type=holdinvoice">
                                    Hold Invoice
                                </a>
                            </li>
                        </div>
                    </ul>

                    <!-- 2nd Row: Loads and Tonnage -->
                    {{-- <ul class="nav nav-tabs mt-2">
                        <li class="nav-item">
                            <a class="nav-link {{ !request()->update_type || request()->update_type === 'loads' ? 'active' : '' }}"
                            href="{{ route('collection.newindex', [
                                    'type' => request()->type ?? 'withtipticket',
                                    'update_type' => 'loads',
                                    'invoice_type' => request()->invoice_type ?? 'preinvoice'
                            ]) }}">
                                Loads
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->update_type === 'tonnage' ? 'active' : '' }}"
                            href="{{ route('collection.newindex', [
                                    'type' => request()->type ?? 'withtipticket',
                                    'update_type' => 'tonnage',
                                    'invoice_type' => request()->invoice_type ?? 'preinvoice'
                            ]) }}">
                                Tonnage
                            </a>
                        </li>
                    </ul> --}}
                                                                                
           </div>


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

        <!-- Compact date filter row
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
        </div> -->

        <div class="table-responsive">
            <table id="invoiceTable" class="table table-hover">
                <thead>
                    <tr class="filters">
                        <th>Booking ID</th>
                        <!-- <th>Booking Date</th> -->
                        <th>Company Name</th>
                        <th>Site Name</th>
                        <th>Material name</th>
                        <!-- <th>On Hold</th> -->
                        <th>Action</th>
                    </tr>
                    <tr class="search-row">
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Booking ID"></th>   
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Company"></th>
                       
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Search Site">
                        </th>
                         <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Material name">
                        </th>
                        <!-- <th><input type="text" class="column-ntrol form-control-sm column-search" placeholder="Search hold" /></th> -->
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const slider = document.querySelector('.tab-slider');
    if (slider) {
        slider.style.display = 'none';
    }
});

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
                    // Old code
                    // ajax: {
                    //     url: "{{ route('collection.data') }}",
                    //     data: function(d) {
                    //         d.type = "{{ $type }}";
                    //         d.invoice_type = "{{ $invoice_type }}";
                    //         d.start_date = $('#startDate').val();
                    //         d.end_date = $('#endDate').val();
                    //     }
                    // },

                    //New Update code
                     ajax: {
                           url: "{{ route('collection.data') }}",
                            data: function(d) {
                                d.type = '{{ request('type', 'withtipticket') }}';
                                d.update_type = '{{ request('update_type', 'load') }}';
                                d.invoice_type = '{{ request('invoice_type', 'preinvoice') }}';
                                d.start_date = $('#startDate').val();
                                d.end_date = $('#endDate').val();
                            }
                   },
                     columns: [
                        {
                            data: 'BookingRequestID',
                            name: 'BookingRequestID'
                        },
                        // {
                        //     data: 'CreateDateTime',
                        //     name: 'CreateDateTime'
                        // },
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

            
      
        // Global search on button click
          $('#globalSearchBtn').on('click', function () {
    let searchTerm = $('#globalSearchInput').val();
    differencesTable.search(searchTerm).draw();
    perfectTable.search(searchTerm).draw();
});

        $('#filterBtn').click(function () {
           differencesTable.draw();
           perfectTable.draw();
        });

        $('#clearFilters').click(function () {
            $('#startDate').val('');
            $('#endDate').val('');
            differencesTable.draw();
            perfectTable.draw();
        });
    

    
            $('#invoiceTable thead .column-search').on('keyup change', function() {
                let colIndex = $(this).parent().index();
                table.column(colIndex).search(this.value).draw();
            });
        });
        
    </script>


   <style>
              body {
        overflow-x: hidden;
    }

div.dataTables_processing {
    position: absolute;
    top: 3%!important;
    left: 50%;
    width: 200px;
    margin-left: -100px;
    margin-top: 20px;
    text-align: center;
    padding: 2px;
    z-index: 10;
}
    .table-container {
        overflow-x: hidden;
    }
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
            
/* Active tab color for LEFT tabs (Loads/Tonnage) */
.nav-tabs .tab-left .nav-link.active {
    color: #004085; /* dark blue */
    font-weight: bold;
}

/* Active tab color for RIGHT tabs (Pre/Ready Invoice) */
.nav-tabs .tab-right .nav-link.active {
    color: #3c8dbc; /* existing blue */
    font-weight: bold;
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
                display: none;
            }
            .nav-tabs .nav-item.ms-auto .nav-link {
                margin-left: 20px;
                margin-right: 0;
            }
            .tab-container {
                overflow-x: hidden !important;
                overflow-y: hidden;
                /* white-space: nowrap; */
                padding-bottom: 2px;
            }



            .nav-tabs {
            flex-wrap: wrap !important;
            overflow-x: auto;
    width: 100%;
            }

            .nav-tabs .nav-item {
                  white-space: nowrap;
    flex: 0 0 auto;
            flex-shrink: 0;
            }
/* Hide the horizontal slider effect */
/* span.tab-slider,
.nav-tabs .tab-slider,
ul.nav-tabs > .tab-slider,
span.moving-tab {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    opacity: 0 !important;
    transform: none !important;
    transition: none !important;
} */
.tab-container .tab-slider,
.tab-slider {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    opacity: 0 !important;
    transform: none !important;
    transition: none !important;
    pointer-events: none !important;
    position: static !important;
}

.d-flex-new {
    display: flex !important;
    margin-right: 10px;
   
}

               /* Make the month name and year color match selected day color */
.flatpickr-current-month,
.flatpickr-current-month .cur-month,
.flatpickr-current-month input.cur-year {
    color:rgb(0, 0, 0) !important;
    font-weight: 600;
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

        /* div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button.active a:hover {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
            color: white;
        } */

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

        div.dataTables_filter {
            display: none;
        }
        
    </style>
@endsection
