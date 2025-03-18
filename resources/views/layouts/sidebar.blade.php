<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo my-4">
        <a href="{{ route('dashboard') }}" class="app-brand-link d-flex flex-column align-items-center">
            <!-- Logo on top -->
            <span class="app-brand-logo mb-2">
                <img src="{{ asset('img/Logo.png') }}" alt="TML Logo" class="logo-img">
            </span>
            <!-- Text below logo -->
            <span class="app-brand-text demo menu-text fw-bold">TML Invoice</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="menu-icon tf-icons mdi mdi-menu-open"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Delivery Invoice -->
        <li class="menu-item {{ Request::routeIs('delivery.*') ? 'active' : '' }}">
            <a href="{{ route('delivery.index') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                <div>Delivery Invoice</div>
            </a>
        </li>

        <!-- Collection Invoice -->
        <li class="menu-item {{ Request::routeIs('collection.*') ? 'active' : '' }}">
            <a href="{{ route('collection.index') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                <div>Collection Invoice</div>
            </a>
        </li>

        <!-- Daywork Invoice -->
        <li class="menu-item {{ Request::routeIs('daywork.*') ? 'active' : '' }}">
            <a href="{{ route('daywork.index') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                <div>Daywork Invoice</div>
            </a>
        </li>

        <!-- Haulage Invoice -->
        <li class="menu-item {{ Request::routeIs('haulage.*') ? 'active' : '' }}">
            <a href="{{ route('haulage.index') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                <div>Haulage Invoice</div>
            </a>
        </li>

        <!-- Waiting Time Invoice -->
        <li class="menu-item {{ Request::routeIs('waitingtime.*') ? 'active' : '' }}">
            <a href="{{ route('waitingtime.index') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                <div>Waiting Time Invoice</div>
            </a>
        </li>

        @if (session('roleId') != 8)
            <!-- Users -->
            <li class="menu-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
                <a href="{{ route('users.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-group"></i>
                    <div>Users</div>
                </a>
            </li>

            <!-- System Logs -->
            <li class="menu-item {{ Request::routeIs('systemlogs.*') ? 'active' : '' }}">
                <a href="{{ route('systemlogs.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-math-log"></i>
                    <div>System Logs</div>
                </a>
            </li>
        @endif
    </ul>
</aside>

<!-- CSS for active highlight and other improvements -->
<style>
    /* Sidebar main container styles */
    .layout-menu {
        box-shadow: 0 0.125rem 0.375rem 0 rgba(0, 0, 0, 0.16);
        transition: box-shadow 0.3s ease-in-out;
        background-color: #3c8dbc;
    }

    .layout-menu:hover {
        box-shadow: 0 0.25rem 1rem 0 rgba(0, 0, 0, 0.16);
    }

    /* Menu item styles */
    /* Active item highlight - Border only & black text */
    .layout-menu.menu-vertical .menu-inner .menu-item.active .menu-link {
        border: 1px solid #3c8dbc;
        background-color: transparent !important;
        font-weight: 600;
        color: #000 !important;
        /* Ensure black text color */
    }

    /* Active menu item icon color */
    .layout-menu.menu-vertical .menu-inner .menu-item.active .menu-link .menu-icon {
        color: #000 !important;
        /* Ensure black icon color */
    }

    /* Make sure text is black for active items */
    .layout-menu.menu-vertical .menu-inner .menu-item.active .menu-link div {
        color: #000 !important;
        /* Additional selector for text inside divs */
    }

    /* Hover effect with subtle shift */
    .menu-vertical .menu-item .menu-link:hover {
        border: 1px solid #3c8dbc;
        background-color: rgba(60, 141, 188, 0.05);
        transform: translateX(3px);
        color: #000;
        /* Text color on hover */
    }

    /* Active menu item indicator */
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

    .app-brand {
        display: flex;
        justify-content: space-between;
        /* Ensures space between logo and toggle button */
        align-items: center;
    }

    /* Logo styling */
    .app-brand-logo {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .logo-img {
        width: 150px;
        height: auto;
        transition: transform 0.3s ease;
    }

    .app-brand-link:hover .logo-img {
        transform: scale(1.05);
    }

    /* Text below logo */
    .app-brand-text {
        color: black;
        font-size: 1.2rem;
        transition: color 0.2s;
        letter-spacing: 0.5px;
        text-align: center;
    }

    .app-brand-link:hover .app-brand-text {
        color: #3c8dbc;
    }

    /* Menu inner shadow effect */
    .menu-inner-shadow {
        background: linear-gradient(#000000 10%, rgba(255, 255, 255, 0.1) 100%);
        height: 80px;
        opacity: 0.7;
    }

    /* Menu padding and spacing */
    .menu-inner {
        padding-top: 0.75rem !important;
        padding-bottom: 2rem !important;
    }

    /* App brand container */
    .app-brand {
        padding: 0 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    /* Logo container */
    .app-brand-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    /* Menu icon styling */
    .menu-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: #697a8d;
        font-size: 1.35rem;
        transition: all 0.2s;
    }

    .menu-link:hover .menu-icon {
        color: #3c8dbc;
    }

    /* Layout and spacing */
    .menu-item+.menu-item {
        margin-top: 0.35rem;
    }

    .menu-link {
        border-radius: 0.425rem;
        margin: 0 0.85rem;
        padding-left: 1.15rem !important;
        position: relative;
    }

    @media (max-width: 727px) {

        .app-brand-logo,
        .app-brand-text {
            display: none;
            /* Hide logo and text */
        }

        .layout-menu-toggle {
            display: block !important;
            /* Ensure toggle button is visible */
        }
    }

    /* Hide toggle button on larger screens */
    @media (min-width: 728px) {
        .layout-menu-toggle {
            display: none !important;
        }
    }
</style>
