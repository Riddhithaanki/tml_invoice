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
                            @php
                                $details = json_decode($log->details, true); // Decode JSON string to an array
                                $oldData = $details['old_data'] ?? null;
                                $newData = $details['new_data'] ?? null;
                            @endphp
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>{{$log->email}}</td>
                            <td>{{$log->name}}</td>
                            <td>{{$log->activity}}</td>
                            <td>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logDetailsModal{{$key}}">View Details</button>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y') }}</td>    
                        </tr>
                        <!-- Modal for each log entry -->
                        <div class="modal fade" id="logDetailsModal{{$key}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-cc">
                                <div class="modal-content">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="modal-body p-4">
                                        <div class="text-center mb-4">
                                            <h4 class="mb-2">Log Details</h4>
                                        </div>
                                        <div class="log-details">
                                        @if($oldData)
                                                <h6>Old Data:</h6>
                                                <pre style="background-color: rgb(154 195 219 / 40%)">{{ json_encode($oldData, JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                <h6>Old Data:</h6>
                                                <pre style="background-color: rgb(154 195 219 / 40%)">No old data available.</pre>
                                            @endif

                                            @if($newData)
                                                <h6>New Data:</h6>
                                                <pre style="background-color: rgb(154 195 219 / 40%)">{{ json_encode($newData, JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                <h6>New Data:</h6>
                                                <pre style="background-color: rgb(154 195 219 / 40%)">No new data available.</pre>
                                            @endif
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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