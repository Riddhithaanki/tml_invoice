@extends('layouts.main')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">User List</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-users table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Role</th>
                        <th>Created On</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('.datatables-users').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.data') }}",
                    type: "GET",
                },
                columns: [{
                        data: "userId",
                        name: "userId"
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "email",
                        name: "email"
                    },
                    {
                        data: "mobile",
                        name: "mobile"
                    },
                    {
                        data: "role.role",
                        name: "role.role"
                    },
                    {
                        data: "createdDtm",
                        name: "createdDtm"
                    }
                ],
                order: [
                    [0, "desc"]
                ]
            });

            // Delete action
            $(document).on("click", ".delete", function() {
                var userId = $(this).data("id");
                if (confirm("Are you sure you want to delete this user?")) {
                    $.ajax({
                        url: `/users/${userId}`,
                        type: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function(response) {
                            alert("User deleted successfully!");
                            table.ajax.reload();
                        },
                        error: function() {
                            alert("Error deleting user.");
                        }
                    });
                }
            });
        });
    </script>
@endsection
