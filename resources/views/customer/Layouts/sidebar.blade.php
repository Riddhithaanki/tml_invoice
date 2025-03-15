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
            <a href="{{ route('customer.dashboard') }}" class="menu-link">
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('customer.invoice.index') }}" class="menu-link">
                <div data-i18n="Invoices">Invoices</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('collection.index') }}" class="menu-link">
                <div data-i18n="Archieve">Archieve</div>
            </a>
        </li>
    </ul>
</aside>
