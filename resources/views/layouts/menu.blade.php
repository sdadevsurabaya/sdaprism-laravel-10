<div class="horizontal-menu">
    <nav class="navbar top-navbar">
        <div class="container-fluid container-lg">
            <div class="navbar-content">
                <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center">
                    <img src="{{ asset('assets/logo/logo-sda-global-24.svg') }}" alt="Logo" height="28" style="max-height: 30px;">
                </a>

                <!-- Logo-mini for small screen devices (mobile/tablet) -->
                {{-- <div class="logo-mini-wrapper">
                    <img src="../assets/images/logo-mini-light.png" class="logo-mini logo-mini-light" alt="logo">
                    <img src="../assets/images/logo-mini-dark.png" class="logo-mini logo-mini-dark" alt="logo">
                </div> --}}

                <ul class="navbar-nav">
                    <li class="theme-switcher-wrapper nav-item">
                        <input type="checkbox" value="" id="theme-switcher">
                        <label for="theme-switcher">
                            <div class="box">
                                <div class="ball"></div>
                                <div class="icons">
                                    <i class='bx bxs-sun'></i>
                                    <i class='bx bxs-moon'></i>
                                </div>
                            </div>
                        </label>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="w-30px h-30px ms-1 rounded-circle"
                                src="{{ asset('assets/faces/300-15.jpg') }}" alt="profile">
                        </a>
                        <div class="p-0 dropdown-menu" aria-labelledby="profileDropdown">
                            <div class="px-5 py-3 d-flex flex-column align-items-center border-bottom">
                                <div class="mb-3">
                                    <img class="w-80px h-80px rounded-circle"
                                        src="{{ asset('assets/faces/300-15.jpg') }}" alt="">
                                </div>
                                <div class="text-center">
                                    @auth
                                        <p class="fs-16px fw-bolder text-capitalize">{{ Auth::user()->name }}</p>
                                        <p class="fs-12px text-secondary">{{ Auth::user()->email }}</p>
                                    @endauth
                                </div>
                            </div>
                            <ul class="p-1 list-unstyled">
                                <li class="py-2 dropdown-item">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>

                                    <a href="#" class="text-body ms-0 d-flex align-items-center"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="me-2 icon-md" data-feather="log-out"></i>
                                        <span>Log Out</span>
                                    </a>

                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <!-- navbar toggler for small devices -->
                <div data-toggle="horizontal-menu-toggle"
                    class="navbar-toggler navbar-toggler-right d-lg-none align-self-center">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>
    <nav class="bottom-navbar">
        <div class="container">
            <ul class="nav page-navigation">
                @auth
                    @php $role = strtolower(Auth::user()->rolesUsers->first()?->roles->name); @endphp
                    
                    {{-- Dashboard (QR Scan) --}}
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="link-icon" data-feather="grid"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    @if (in_array($role, ['admin', 'staff']))
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="link-icon" data-feather="pie-chart"></i>
                                <span class="menu-title">Data</span>
                            </a>
                            <div class="submenu">
                                <ul class="submenu-item">
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('pricelists.index') }}">Pricelist</a></li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($role === 'admin')
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="link-icon" data-feather="hard-drive"></i>
                                <span class="menu-title">Master</span>
                            </a>
                            <div class="submenu">
                                <ul class="submenu-item">
                                    <li class="nav-item"><a class="nav-link" href="{{ route('brand.index') }}">Brand (Logo)
                                        </a></li>
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('currency.index') }}">Currency</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="link-icon" data-feather="users"></i>
                                <span class="menu-title">User Management</span>
                            </a>
                            <div class="submenu">
                                <ul class="submenu-item">
                                    <li class="nav-item"><a class="nav-link" href="{{ route('user.index') }}">User
                                            Login</a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('activity-log.index') }}" class="nav-link">
                                <i class="link-icon" data-feather="activity"></i>
                                <span class="menu-title">Activity Log</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <a href="#" class="nav-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="link-icon" data-feather="log-out"></i>
                            <span class="menu-title">Log Out</span>
                        </a>
                    </li>
                @endauth

            </ul>
        </div>
    </nav>
</div>
