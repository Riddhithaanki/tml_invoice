@extends('layouts.main')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">System Logs</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table id="logDetailsTable" class="datatables-users table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Id</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Activity</th>
                        <th>Details</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $key => $log)
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>{{$log->email}}</td>
                            <td>{{$log->name}}</td>
                            <td>{{$log->activity}}</td>
                            <td>{{$log->details}}</td>
                            <td>{{$log->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
            $('#logDetailsTable').DataTable({
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