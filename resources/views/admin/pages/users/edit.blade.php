@extends('layouts.main')
@section('content')
    <div class="table-container container-fluid mt-4">
        <div class="table-header">
            <h3 class="table-title text-center text-white">User Edit</h3>
        </div>
    <form action="{{ route('users.update', $user->userId) }}" method="POST" id="userEditForm" class="formsubmit">
         <!-- CSRF Token -->
        @csrf
        
             <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required>
                    </div>

                    <!-- Mobile -->
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile"
                            value="{{ old('mobile', $user->mobile) }}" required>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                       
                        <select class="form-select" id="role" name="role" required>
                       
                            <option value="" disabled>Select role</option>
                            <option value="2" {{ $user->role->roleId == 2 ? 'selected' : '' }}>Manager</option>
                            <option value="3" {{ $user->role->roleId == 3 ? 'selected' : '' }}>Employee</option>
                            <option value="7" {{ $user->role->roleId == 7 ? 'selected' : '' }}>Weighbridge</option>
                            <option value="8" {{ $user->role->roleId == 8 ? 'selected' : '' }}>Accounts</option>
                            <option value="9" {{ $user->role->roleId == 9 ? 'selected' : '' }}>Read Only</option>
                            <option value="10" {{ $user->role->roleId == 10 ? 'selected' : '' }}>Account Reports</option>
                            <option value="11" {{ $user->role->roleId == 11 ? 'selected' : '' }}>Admin Staff</option>
                            <option value="12" {{ $user->role->roleId == 12 ? 'selected' : '' }}>Sales Manager</option>
                            <option value="13" {{ $user->role->roleId == 13 ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>

  <!-- Submit -->
  <button type="submit" class="btn btn-primary">Submit</button>

     </form>

    <script>
        $(document).ready(function() {

            // Initialize DataTable with individual column searching
            var table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: {
                    url: "{{ route('users.data') }}",
                },
                columns: [{
                        data: 'userId',
                        name: 'userId',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'role.role',
                        name: 'role.role'
                    },
                    // Old code
                    // {
                    //     data: 'createdDtm',
                    //     name: 'createdDtm',
                    //     orderable: false,
                    //     searchable: false
                    // }
                    // New coede

                {
                    data: 'createdDtm',
                    name: 'createdDtm',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (!data) return '';
                        
                        const date = new Date(data);
                        if (isNaN(date)) return data; // fallback if invalid

                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();

                        return `${day}-${month}-${year}`;
                    }
                }
                ],
                order: [
                    [2, 'desc']
                ],
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


            // Apply search to each column
            $('.column-search').on('keyup change', function() {
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

            $('#clearFilters').on('click', function() {
                $('.column-search').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>

    <style>
.formsubmit

{
    padding: 20px 20px 20px 20px;
}
        div.dataTables_processing {
    position: absolute;
    top: 10%!important;
    left: 50%;
    width: 200px;
    margin-left: -100px;
    margin-top: 10px;
    text-align: center;
    padding: 2px;
    z-index: 10;
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

        div.dataTables_filter {
            display: none;
        }
    </style>
@endsection
