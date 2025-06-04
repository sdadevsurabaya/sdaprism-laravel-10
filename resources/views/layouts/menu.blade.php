<div class="horizontal-menu">
    <nav class="navbar top-navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="{{ route('dashboard') }}" class="navbar-brand d-none d-lg-flex">
                    <img src="{{ asset('assets/logo/logo-sda-global-24.svg') }}" alt="Logo" height="30">
                </a>

                <!-- Logo-mini for small screen devices (mobile/tablet) -->
                {{-- <div class="logo-mini-wrapper">
                    <img src="../assets/images/logo-mini-light.png" class="logo-mini logo-mini-light" alt="logo">
                    <img src="../assets/images/logo-mini-dark.png" class="logo-mini logo-mini-dark" alt="logo">
                </div> --}}

                {{-- <form class="search-form">
                    <div class="input-group">
                        <div class="input-group-text">
                            <i data-feather="search"></i>
                        </div>
                        <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
                    </div>
                </form> --}}

                <ul class="navbar-nav">
                    <li class="theme-switcher-wrapper nav-item">
                        <input type="checkbox" value="" id="theme-switcher">
                        <label for="theme-switcher">
                            <div class="box">
                                <div class="ball"></div>
                                <div class="icons">
                                    {{-- <i data-feather="icon-sun"></i> --}}
                                    <i class='bx bxs-sun'></i>
                                    <i class='bx bxs-moon'></i>
                                    {{-- <i data-feather="icon-moon"></i> --}}
                                </div>
                            </div>
                        </label>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="w-30px h-30px ms-1 rounded-circle" src="{{ asset('assets/imagenotavailable.jpg') }}"
                                alt="profile">
                        </a>
                        <div class="p-0 dropdown-menu" aria-labelledby="profileDropdown">
                            <div class="px-5 py-3 d-flex flex-column align-items-center border-bottom">
                                <div class="mb-3">
                                    <img class="w-80px h-80px rounded-circle" src="{{ asset('assets/imagenotavailable.jpg') }}"
                                        alt="">
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
                <!-- Menu untuk Admin dan Staff -->
                @auth
                    @php $role = strtolower(Auth::user()->rolesUsers->first()?->roles->name); @endphp
                    @if (in_array($role, ['admin', 'staff']))
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="link-icon" data-feather="pie-chart"></i>
                                <span class="menu-title">Data</span>
                                {{-- <i class="link-arrow"></i> --}}
                            </a>
                            <div class="submenu">
                                <ul class="submenu-item">
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('pricelists.index') }}">Pricelist</a></li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if (strtolower(Auth::user()->rolesUsers->first()?->roles->name) === 'admin')
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
                                {{-- <i class="link-arrow"></i> --}}
                            </a>
                            <div class="submenu">
                                <ul class="submenu-item">
                                    {{-- <li class="category-heading">Setting User</li> --}}
                                    <li class="nav-item"><a class="nav-link" href="{{ route('user.index') }}">User Login</a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
                                    </li>
                                    {{-- <li class="nav-item"><a class="nav-link" href="">Permission</a></li> --}}
                                </ul>
                            </div>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </nav>
</div>
