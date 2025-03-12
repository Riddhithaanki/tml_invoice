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

    <!-- Icons -->
    <link rel="stylesheet" href="{{ url('vendor/fonts/materialdesignicons.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/fonts/flag-icons.css') }}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ url('vendor/libs/node-waves/node-waves.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    {{-- <link rel="stylesheet" href="{{ url('vendor/css/rtl/core.css') }}" class="template-customizer-core-css" /> --}}
    <link rel="stylesheet" href="{{ url('css/demo.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ url('vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ request()->root() }}/vendor/css/rtl/core.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ url('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/libs/sweetalert2/sweetalert2.scss') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ url('vendor/css/pages/cards-statistics.css') }}" />
    <link rel="stylesheet" href="{{ url('vendor/css/pages/cards-analytics.css') }}" />

    <!-- Add Leaflet.js CDN link in the <head> section -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <!-- Helpers -->
    <script src="{{ url('vendor/js/template-customizer.js') }}"></script>
    <script src="{{ url('vendor/js/helpers.js') }}"></script>
    <script src="{{ url('js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            {{-- Sidebar --}}
            @include('layouts.sidebar')
            <div class="layout-page">
                {{-- Header --}}
                @include('layouts.header')
                <div class="container">
                    <div class="content-wrapper">
                        {{-- Main Content --}}
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ url('vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ url('vendor/js/bootstrap.js') }}"></script>
    <script src="{{ url('vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ url('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ url('vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ url('vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ url('vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ url('vendor/js/menu.js') }}"></script>
    <script src="{{ url('vendor/js/dropdown-hover.js') }}"></script>
    <script src="{{ url('vendor/js/mega-dropdown.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ url('vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ url('vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ url('vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ url('vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Main JS -->
    <script src="{{ url('js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {
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
