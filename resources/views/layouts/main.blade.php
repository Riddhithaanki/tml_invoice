<!DOCTYPE html>
<html lang="en" data-template="front-pages">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="icon" type="image/x-icon" href="{{ url('img/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons - Load Font Awesome first -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('public/vendor/fonts/materialdesignicons.css') }}" />
    <link rel="stylesheet" href="{{ url('public/vendor/fonts/flag-icons.css') }}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ url('public/vendor/libs/node-waves/node-waves.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ url('public/vendor/css/rtl/core.css') }} />
    <link rel="stylesheet"
        href="{{ url('public/css/demo.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ url('public/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ url('public/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ url('public/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ url('public/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ url('public/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('public/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ url('public/vendor/libs/swiper/swiper.css') }}" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ url('public/vendor/css/pages/cards-statistics.css') }}" />
    <link rel="stylesheet" href="{{ url('public/vendor/css/pages/cards-analytics.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Load Toastr CSS last -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .btn-primary {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
            color: white;
        }

        .btn-primary:hover {
            background-color: #367fa9;
            border-color: #367fa9;
        }

        /* SweetAlert2 Custom Styles */
        .swal2-popup {
            font-size: 1rem;
        }

        .swal2-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .swal2-content {
            font-size: 1rem;
        }

        .swal2-styled.swal2-confirm {
            background-color: #dc3545;
            color: white;
            border: none;
            box-shadow: none;
        }

        .swal2-styled.swal2-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            box-shadow: none;
        }

        .swal2-icon {
            border-color: #ffa000;
        }

        /* Updated Toastr Styling */
        #toast-container {
            z-index: 999999;
        }

        #toast-container>.toast {
            background-image: none !important;
            padding: 15px 15px 15px 50px;
            width: 350px;
            border-radius: 8px;
            opacity: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #toast-container>.toast:before {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 24px;
            line-height: 18px;
            color: #ffffff;
        }

        #toast-container>.toast-success {
            background-color: #51A351 !important;
            color: #ffffff !important;
        }

        #toast-container>.toast-success:before {
            content: '\f00c';
        }

        #toast-container>.toast-error {
            background-color: #BD362F !important;
            color: #ffffff !important;
        }

        #toast-container>.toast-error:before {
            content: '\f00d';
        }

        #toast-container>.toast-info {
            background-color: #2F96B4 !important;
            color: #ffffff !important;
        }

        #toast-container>.toast-info:before {
            content: '\f129';
        }

        #toast-container>.toast-warning {
            background-color: #F89406 !important;
            color: #ffffff !important;
        }

        #toast-container>.toast-warning:before {
            content: '\f071';
        }

        .toast-close-button {
            position: absolute;
            right: 10px;
            top: 5px;
            color: #ffffff;
            opacity: 0.7;
            text-shadow: none;
            font-weight: 300;
        }

        .toast-close-button:hover {
            color: #ffffff;
            opacity: 1;
        }

        #toast-container>.toast-message {
            font-size: 14px;
            line-height: 1.4;
            color: #ffffff;
            margin-top: 4px;
        }

        /* Ensure toasts are visible on modals */
        .modal-backdrop {
            z-index: 99999;
        }

        .modal {
            z-index: 999999;
        }

        #template-customizer,
        .template-customizer,
        .layout-customizer {
            display: none !important;
        }
    </style>

    <!-- Load jQuery first -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Then load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Then load Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Configure Toastr options
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Configure SweetAlert2 default options
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger ms-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });
    </script>

    <script src="{{ url('public/vendor/js/helpers.js') }}"></script>

    <script src="{{ url('public/js/config.js') }}"></script>

</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            {{-- Sidebar --}}
            @include('layouts.sidebar')
            <div class="layout-page">
                {{-- Header --}}
                @include('layouts.header')
                <div class="container-fluid">
                    <div class="content-wrapper">
                        {{-- Main Content --}}
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ url('public/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ url('public/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ url('public/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ url('public/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ url('public/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ url('public/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ url('public/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ url('public/vendor/js/menu.js') }}"></script>
    <script src="{{ url('public/vendor/js/dropdown-hover.js') }}"></script>
    <script src="{{ url('public/vendor/js/mega-dropdown.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ url('public/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ url('public/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ url('public/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ url('public/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ url('public/js/main.js') }}"></script>

    <script>
        $(document).ready(function() {
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
</body>

</html>
