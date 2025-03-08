$(document).ready(function () {
    $(".datatables-users").DataTable({
        processing: true,
        serverSide: false, // Change to true if using server-side processing
        ajax: {
            url: "/users-data", // Adjust the route as needed
            type: "GET",
        },
        columns: [
            { data: "id", name: "id" },
            { data: "username", name: "username" },
            { data: "email", name: "email" },
            { data: "age", name: "age" },
            { data: "mobile_number", name: "mobile_number" },
            { data: "role.name", name: "role.name" },
            {
                data: "status",
                name: "status",
                render: function (data, type, row) {
                    let statusClass = data == 1 ? "bg-success" : "bg-danger";
                    let statusText = data == 1 ? "Active" : "Inactive";
                    return `<span class="badge ${statusClass} toggle-status" style="cursor:pointer;" data-id="${row.id}" data-status="${data}">${statusText}</span>`;
                },
            },
            {
                data: "id",
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-primary edit" data-id="${data}">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="${data}">Delete</button>
                    `;
                },
            },
        ],
        order: [[0, "desc"]],
        dom:
            '<"d-flex justify-content-between align-items-center header-actions mx-0 mb-3"' +
            '<"d-flex align-items-center"l<"ms-2"f>>' +
            '<"d-flex align-items-center"B>>' +
            '<"row"' +
            '<"col-sm-12"tr>>' +
            '<"row"' +
            '<"col-sm-12 col-md-10"i>' +
            '<"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: "collection",
                className: "btn btn-outline-secondary dropdown-toggle me-2",
                text: '<i class="ti ti-download me-1"></i> Export',
                buttons: [
                    {
                        extend: "copy",
                        text: '<i class="ti ti-copy me-1"></i>Copy',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                    },
                    {
                        extend: "csv",
                        text: '<i class="ti ti-file-spreadsheet me-1"></i>CSV',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                    },
                    {
                        extend: "excel",
                        text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                    },
                    {
                        extend: "pdf",
                        text: '<i class="ti ti-file-text me-1"></i>PDF',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                    },
                    {
                        extend: "print",
                        text: '<i class="ti ti-printer me-1"></i>Print',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                    },
                ],
            },
            {
                text: '<i class="ti ti-reload me-1"></i> Refresh',
                className: "btn btn-primary",
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                },
            },
        ],
    });
});
