<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme pb-5">
    <div class="app-brand demo" style="padding-left: 22px">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo/logo-kabupaten.png') }}" alt="" height="50">

            </span>
            <span
                class="app-brand-text demo menu-text text-primary fw-semibold ms-2">{{ ucfirst(config('app.name')) }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="fa-solid fa-chevron-left d-flex align-items-center justify-content-center"></i>
        </a>
    </div>
    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon fa-solid fa-house fs-6"></i>
                <div class="text-truncate">
                    Beranda
                </div>
            </a>
        </li>


        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Menu</span>
        </li>
        @can('view permohonan')
            <li class="menu-item {{ request()->routeIs('permohonan.*') ? 'active' : '' }}">
                <a href="{{ route('permohonan.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-sheet-plastic fs-6"></i>
                    <div class="text-truncate">
                        Permohonan
                    </div>
                </a>
            </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">System</span>
        </li>
        @can('view template')
            <li class="menu-item {{ request()->routeIs('template.*') ? 'active' : '' }}">
                <a href="{{ route('template.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-grid-2 fs-6"></i>
                    <div class="text-truncate">
                        Templat Docs
                    </div>
                </a>
            </li>
        @endcan
        @can('view roles' || 'view users')
            <li class="menu-item {{ request()->routeIs('roles.*') || request()->routeIs('users.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base fa-solid fa-shield fs-6"></i>
                    <div>RBAC</div>
                </a>
                <ul class="menu-sub">
                    @can('view roles')
                        <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}" class="menu-link">
                                <div>Roles</div>
                            </a>
                        </li>
                    @endcan
                    @can('view users')
                        <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div>Users</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('view key-storage')
            <li class="menu-item {{ request()->routeIs('key-storages.*') ? 'active' : '' }}">
                <a href="{{ route('key-storages.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-key fs-6"></i>
                    <div class="text-truncate">
                        Key Storage
                    </div>
                </a>
            </li>
        @endcan
        @can('view audit-trail')
            <li class="menu-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                <a href="{{ route('audit.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-files fs-6"></i>
                    <div class="text-truncate">
                        Audit Trail
                    </div>
                </a>
            </li>
        @endcan
        @can('view logs')
            <li class="menu-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                <a href="{{ route('logs.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-file-lines fs-6"></i>
                    <div class="text-truncate">
                        Logs
                    </div>
                </a>
            </li>
        @endcan
        @can('view performance')
            <li class="menu-item {{ request()->routeIs('performance.*') ? 'active' : '' }}">
                <a href="{{ route('performance.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-chart-line fs-6"></i>
                    <div class="text-truncate">
                        Performance
                    </div>
                </a>
            </li>
        @endcan
        @can('view route-list')
            <li class="menu-item {{ request()->routeIs('route-list.*') ? 'active' : '' }}">
                <a href="{{ route('route-list.index') }}" class="menu-link">
                    <i class="menu-icon fa-solid fa-route fs-6"></i>
                    <div class="text-truncate">
                        Route List
                    </div>
                </a>
            </li>
        @endcan

        {{-- <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Profile</span>
        </li> --}}
        {{-- <li class="menu-item {{ request()->routeIs('account.index') ? 'active' : '' }}">
            <a href="{{ route('account.index') }}" class="menu-link">
                <i class="menu-icon fa-solid fa-address-card fs-6"></i>
                <div class="text-truncate" data-i18n="Profile">Profile</div>
            </a>
        </li> --}}
    </ul>
</aside>
