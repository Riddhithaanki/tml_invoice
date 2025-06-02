<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo my-4 d-flex justify-content-between align-items-center px-3">
        <a href="{{ route('dashboard') }}" class="app-brand-link d-flex flex-column align-items-center">
            <span class="app-brand-logo mb-2">
                <img src="{{ asset('img/Logo.png') }}" alt="TML Logo" class="logo-img">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">TML Invoice</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link d-lg-none d-block text-large">
            <i class="menu-icon tf-icons mdi mdi-close"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/dashboard.png') }}" style="width:24px" alt="Dashboard" class="menu-icon">
                </div>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('delivery.*') ? 'active' : '' }}">
            <a href="{{ route('delivery.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/delivery.png') }}" style="width:24px" alt="Delivery" class="menu-icon">
                </div>
                <div>Delivery Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('collection.*') ? 'active' : '' }}">
            <a href="{{ route('collection.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/collection.png') }}" style="width:24px" alt="Collection" class="menu-icon">
                </div>
                <div>Collection Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('daywork.*') ? 'active' : '' }}">
            <a href="{{ route('daywork.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/daywork.png') }}" style="width:24px" alt="Daywork" class="menu-icon">
                </div>
                <div>Daywork Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('haulage.*') ? 'active' : '' }}">
            <a href="{{ route('haulage.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/haulage.png') }}" style="width:24px" alt="Haulage" class="menu-icon">
                </div>
                <div>Haulage Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('waitingtime.*') ? 'active' : '' }}">
            <a href="{{ route('waitingtime.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/waitime.png') }}" style="width:24px" alt="Waiting Time" class="menu-icon">
                </div>
                <div>Waiting Time Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('invoice.differences') ? 'active' : '' }}">
            <a href="{{ route('invoice.differences') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/log.png') }}" style="width:24px" alt="Invoice Comparison" class="menu-icon">
                </div>
                <div>Invoice Comparison</div>
            </a>
        </li>

        @if (session('roleId') != 8)
        <li class="menu-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
            <a href="{{ route('users.list') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/users.png') }}" style="width:24px" alt="Users" class="menu-icon">
                </div>
                <div>Users</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('systemlogs.*') ? 'active' : '' }}">
            <a href="{{ route('systemlogs.list') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/log.png') }}" style="width:24px" alt="System Logs" class="menu-icon">
                </div>
                <div>System Logs</div>
            </a>
        </li>
        @endif
    </ul>
</aside>

<style>
    .layout-menu {
        box-shadow: 0 0.125rem 0.375rem rgba(0, 0, 0, 0.16);
        background-color: #3c8dbc;
        transition: all 0.3s ease-in-out;
        width: 260px;
    }

    .layout-container .layout-page {
        margin-left: 260px;
        transition: margin-left 0.3s ease-in-out;
    }

    .menu-item.active .menu-link {
        border: 1px solid #3c8dbc;
        background-color: transparent !important;
        font-weight: 600;
        color: #000 !important;
    }

    .menu-item.active .menu-link .menu-icon,
    .menu-item.active .menu-link div {
        color: #000 !important;
    }

    .menu-item.active .menu-link::after {
        content: '';
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #3c8dbc;
    }

    .menu-link:hover {
        background-color: rgba(60, 141, 188, 0.05);
        border: 1px solid #3c8dbc;
        color: #000;
        transform: translateX(3px);
    }

    .menu-link {
        border-radius: 0.425rem;
        margin: 0 0.85rem;
        padding-left: 1.15rem !important;
        position: relative;
    }

    .menu-icon {
        width: 24px;
        height: 24px;
        margin-right: 0.75rem;
        object-fit: contain;
        transition: all 0.2s;
    }

    .app-brand-text {
        color: black;
        font-size: 1.2rem;
        text-align: center;
    }

    .app-brand-logo .logo-img {
        width: 250px;
        transition: transform 0.3s ease;
    }

    .app-brand-link:hover .logo-img {
        transform: scale(1.05);
    }

    .menu-inner-shadow {
        background: linear-gradient(#000000 10%, rgba(255, 255, 255, 0.1) 100%);
        height: 80px;
        opacity: 0.7;
    }

    /* Mobile responsive toggle */
    @media (max-width: 767px) {
        .layout-menu {
            position: fixed;
            left: -260px;
            top: 0;
            z-index: 1050;
            height: 100%;
            background: #3c8dbc;
        }

        .layout-menu.show {
            left: 0 !important;
        }

        .layout-container .layout-page {
            margin-left: 0 !important;
        }
    }
</style>

<script>
    // Toggle sidebar on mobile
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.querySelector('.layout-menu-toggle');
        const sidebar = document.getElementById('layout-menu');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('show');
            });
        }
    });
</script>
