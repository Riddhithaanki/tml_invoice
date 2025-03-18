<nav class="layout-navbar navbar navbar-expand-xl navbar-detached align-items-center"
    id="layout-navbar" style="background-color: #3c8dbc;">

    <div class="d-flex align-items-center ms-auto w-100 justify-content-start">
        <h3 class="text-white mx-5 mt-2 ">TML Invoice Portal</h3>
    </div>
    <div class="d-flex align-items-center ms-auto w-100 justify-content-end">
        <span class="text-white me-4">Version: 1.0.0</span>

        <ul class="navbar-nav flex-row align-items-center">
            <!-- User Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow text-white d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online me-2" style="height: 30px;">
                        <img src="{{ Auth::user()->profile_image ?? 'https://images.unsplash.com/photo-1676195470090-7c90bf539b3b?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D' }}"
                            alt="User Profile" class="w-px-30 h-px-30 rounded-circle border" />
                    </div>
                    <span class="fw-medium">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="https://images.unsplash.com/photo-1676195470090-7c90bf539b3b?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt="User Profile" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
                                    <small class="text-muted">Admin</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <!-- Logout Link -->
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="mdi mdi-logout me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User Dropdown -->
        </ul>
    </div>
</nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Connect navbar with sidebar */
    .layout-navbar {
        width: 100% !important; /* Adjust based on your sidebar width */
        margin-left: auto !important;
        margin-right: 0 !important;
        transition: width 0.3s ease;
    }

    /* Style for when sidebar is collapsed */
    .layout-menu-collapsed .layout-navbar {
        width: calc(100% - 80px) !important; /* Adjust based on your collapsed sidebar width */
    }

    /* Make navbar sticky */
    .layout-navbar {
        position: sticky;
        top: 0;
        z-index: 100;
    }

    /* Remove container-fluid class which limits width */
    @media (min-width: 1200px) {
        .layout-navbar {
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        }
    }
</style>
