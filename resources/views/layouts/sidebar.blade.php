<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo mt-5 d-flex flex-column align-items-center">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="app-brand-link mb-2">
            <img src="{{ asset('img/Logo.png') }}" alt="Logo" style="height: 150px;">
        </a>

        <!-- Brand Name -->
        <span class="app-brand-text demo menu-text fw-bold">TML Invoice</span>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-2">
        <li class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('delivery.index') }}" class="menu-link">
                <div data-i18n="Delivery Invoice List">Delivery Invoice List</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('collection.index') }}" class="menu-link">
                <div data-i18n="Collection Invoice List">Collection Invoice List</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('daywork.index') }}" class="menu-link">
                <div data-i18n="Daywork Invoice List">Daywork Invoice List</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('haulage.index') }}" class="menu-link">
                <div data-i18n="Haulage Invoice List">Haulage Invoice List</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('waitingtime.index') }}" class="menu-link">
                <div data-i18n="Waiting Time Invoice List">Waiting Time Invoice List</div>
            </a>
        </li>

        @if(session('roleId') != 8)
        <li class="menu-item">
            <a href="{{ route('users.list') }}" class="menu-link">
                <div data-i18n="Users List">Users List</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('systemlogs.list') }}" class="menu-link">
                <div data-i18n="System Logs">System Logs</div>
            </a>
        </li>
        @endif
    </ul>
</aside>
