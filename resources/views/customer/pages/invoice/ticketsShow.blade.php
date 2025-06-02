@extends('customer.Layouts.main')

@section('content')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0.5rem;
        }

        .thumbnail-img {
            max-height: 50px;
            width: auto;
            cursor: pointer;
            border-radius: 3px;
            transition: transform 0.2s;
        }

        .thumbnail-img:hover {
            transform: scale(1.1);
        }

        .compact-table th,
        .compact-table td {
            padding: 0.4rem 0.5rem;
            vertical-align: middle;
        }

        .checkbox-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .select-all-container {
            display: flex;
            gap: 0.5rem;
        }

        .download-progress {
            display: none;
            margin-left: 10px;
            align-items: center;
        }

        .badge-count {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px;
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
            margin-left: 5px;
        }
    </style>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tickets for Invoice #{{ $invoice->Invoice_No }}</h5>
                <div class="select-all-container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                        <label class="form-check-label" for="select-all-checkbox">Select All</label>
                    </div>
                    <button id="download-selected" class="btn btn-sm btn-primary" disabled>
                        <i class="fas fa-download me-1"></i>Download Selected
                        <span id="selected-count" class="badge-count">0</span>
                    </button>
                    <div class="download-progress">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span>Preparing download...</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="tickets-table" class="table table-bordered table-striped compact-table">
                    <thead>
                        <tr>
                            <th width="40px"></th>
                            <th width="40px">#</th>
                            <th>Customer Name</th>
                            <th>GUID</th>
                            <th width="80px">Image</th>
                            <th width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $index => $ticket)
                            <tr>
                                <td class="text-center">
                                    @if($ticket->ImagePath)
                                        <input type="checkbox" class="form-check-input ticket-checkbox"
                                            data-guid="{{ $ticket->GUID }}" data-image="{{ $ticket->ImagePath }}">
                                    @endif
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ticket->cust_name ?? 'N/A' }}</td>
                                <td>{{ $ticket->GUID }}</td>
                                <td class="text-center">
                                    @if($ticket->ImagePath)
                                        <img src="{{ $ticket->ImagePath }}" alt="Ticket" class="thumbnail-img ticket-image">
                                    @else
                                        <span class="badge bg-secondary">No Image</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($ticket->ImagePath)
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info view-btn" data-image="{{ $ticket->ImagePath }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ $ticket->ImagePath }}" download class="btn btn-sm btn-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="imageModalLabel">Ticket Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="modalImage" src="" alt="Ticket" style="max-width: 100%;">
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadBtn" href="" download class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            const table = $('#tickets-table').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                columnDefs: [{ orderable: false, targets: [0, 4, 5] }],
                language: {
                    emptyTable: "No tickets found",
                    zeroRecords: "No matching tickets found"
                }
            });

            function openImageInModal(imagePath) {
                $('#modalImage').attr('src', imagePath);
                $('#downloadBtn').attr('href', imagePath);
                const filename = imagePath.split('/').pop();
                $('#downloadBtn').attr('download', filename);
                new bootstrap.Modal(document.getElementById('imageModal')).show();
            }

            $(document).on('click', '.view-btn', function () {
                openImageInModal($(this).data('image'));
            });

            $(document).on('click', '.ticket-image', function () {
                openImageInModal($(this).attr('src'));
            });

            function updateSelectedCount() {
                const count = $('.ticket-checkbox:checked').length;
                $('#selected-count').text(count);
                $('#download-selected').prop('disabled', count === 0);
            }

            $('#select-all-checkbox').on('change', function () {
                $('.ticket-checkbox').prop('checked', this.checked);
                updateSelectedCount();
            });

            $(document).on('change', '.ticket-checkbox', function () {
                const total = $('.ticket-checkbox').length;
                const selected = $('.ticket-checkbox:checked').length;
                $('#select-all-checkbox').prop('checked', total === selected);
                updateSelectedCount();
            });

            $('#download-selected').on('click', function () {
                const selected = $('.ticket-checkbox:checked');
                if (selected.length === 0) return;

                if (selected.length === 1) {
                    const imagePath = selected.first().data('image');
                    const link = document.createElement('a');
                    link.href = imagePath;
                    link.download = imagePath.split('/').pop();
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    return;
                }

                $('.download-progress').css('display', 'flex');
                $('#download-selected').prop('disabled', true);

                const images = [];
                selected.each(function () {
                    images.push({
                        path: $(this).data('image'),
                        guid: $(this).data('guid')
                    });
                });

                $.ajax({
                    url: '{{ route("customer.tickets.download") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        images: images
                    },
                    success: function (response) {
                        if (response && response.success && response.download_url) {
                            window.location.href = response.download_url;
                        } else {
                            alert('Something went wrong while generating the ZIP.');
                        }
                    },
                    error: function () {
                        alert('Error preparing download.');
                    },
                    complete: function () {
                        $('.download-progress').hide();
                        $('#download-selected').prop('disabled', false);
                    }
                });
            });

            table.on('draw', function () {
                const total = $('.ticket-checkbox').length;
                const selected = $('.ticket-checkbox:checked').length;
                $('#select-all-checkbox').prop('checked', total === selected && total > 0);
            });
        });
    </script>
@endsection
