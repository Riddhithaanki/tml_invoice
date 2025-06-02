<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo py-4 px-4">
        <a href="{{ route('dashboard') }}" class="app-brand-link d-flex flex-column align-items-center">
            <span class="app-brand-logo mb-3">
                <img src="{{ asset('img/Logo.png') }}" alt="TML Logo" class="logo-img">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">TML Invoice</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="menu-icon tf-icons mdi mdi-close align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-2">
        <li class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/dashboard.png') }}" alt="Dashboard" class="menu-icon">
                </div>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('delivery.*') ? 'active' : '' }}">
            <a href="{{ route('delivery.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/delivery.png') }}" alt="Delivery" class="menu-icon">
                </div>
                <div>Delivery Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('collection.*') ? 'active' : '' }}">
            <a href="{{ route('collection.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/collection.png') }}" alt="Collection" class="menu-icon">
                </div>
                <div>Collection Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('daywork.*') ? 'active' : '' }}">
            <a href="{{ route('daywork.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/daywork.png') }}" alt="Daywork" class="menu-icon">
                </div>
                <div>Daywork Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('haulage.*') ? 'active' : '' }}">
            <a href="{{ route('haulage.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/haulage.png') }}" alt="Haulage" class="menu-icon">
                </div>
                <div>Haulage Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('waitingtime.*') ? 'active' : '' }}">
            <a href="{{ route('waitingtime.index') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/waitime.png') }}" alt="Waiting Time" class="menu-icon">
                </div>
                <div>Waiting Time Invoice</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('invoice.differences') ? 'active' : '' }}">
            <a href="{{ route('invoice.differences') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/log.png') }}" alt="Invoice Comparison" class="menu-icon">
                </div>
                <div>Invoice Comparison</div>
            </a>
        </li>

        @if (session('roleId') != 8)
        <li class="menu-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
            <a href="{{ route('users.list') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/users.png') }}" alt="Users" class="menu-icon">
                </div>
                <div>Users</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('systemlogs.*') ? 'active' : '' }}">
            <a href="{{ route('systemlogs.list') }}" class="menu-link">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('svg/sidebar/log.png') }}" alt="System Logs" class="menu-icon">
                </div>
                <div>System Logs</div>
            </a>
        </li>
        @endif
    </ul>
</aside>

<style>
    /* Base Layout Styles */
    .layout-menu {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        background-color: #3c8dbc;
        transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        width: 260px;
        z-index: 1050;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Main Content Spacing */
    .layout-page {
        margin-left: 260px;
        min-height: 100vh;
        padding: 1.5rem;
        transition: margin-left 0.3s ease-in-out;
    }

    .layout-navbar {
        margin-left: 260px;
        transition: margin-left 0.3s ease-in-out;
    }

    /* Brand Section Styles */
    .app-brand {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .app-brand-logo .logo-img {
        width: 200px;
        height: auto;
        transition: transform 0.3s ease;
    }

    .app-brand-text {
        color: #ffffff;
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }

    /* Menu Items Styles */
    .menu-inner {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .menu-item {
        margin: 0.5rem 0;
    }

    .menu-link {
        display: flex;
        align-items: center;
        padding: 0.85rem 1.5rem;
        color: #ffffff;
        border-radius: 0.375rem;
        margin: 0 0.75rem;
        transition: all 0.3s ease;
        text-decoration: none;
        position: relative;
    }

    .menu-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        transform: translateX(3px);
    }

    .menu-item.active .menu-link {
        background-color: #ffffff;
        color: #3c8dbc !important;
        font-weight: 600;
    }

    .menu-icon {
        width: 1.5rem;
        height: 1.5rem;
        margin-right: 1rem;
        object-fit: contain;
    }

    /* Shadow Effect */
    .menu-inner-shadow {
        position: absolute;
        top: 0;
        height: 60px;
        width: 100%;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0) 100%);
    }

    /* Mobile Responsive Styles */
    @media (max-width: 1199.98px) {
        .layout-menu {
            transform: translateX(-100%);
        }

        .layout-menu.show {
            transform: translateX(0);
        }

        .layout-page,
        .layout-navbar {
            margin-left: 0 !important;
        }

        .layout-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1049;
            display: none;
        }

        .layout-overlay.show {
            display: block;
        }
    }

    /* Animation and Transitions */
    .menu-link, .menu-icon, .app-brand-logo .logo-img {
        transition: all 0.3s ease-in-out;
    }

    /* Scrollbar Styling */
    .layout-menu::-webkit-scrollbar {
        width: 6px;
    }

    .layout-menu::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .layout-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .layout-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.layout-menu-toggle');
        const sidebar = document.getElementById('layout-menu');
        const body = document.body;
        
        // Create overlay element
        const overlay = document.createElement('div');
        overlay.className = 'layout-overlay';
        body.appendChild(overlay);

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }

        // Toggle sidebar on button click
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', toggleSidebar);

        // Close sidebar on window resize if in mobile view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1200 && sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });
    });
</script>
