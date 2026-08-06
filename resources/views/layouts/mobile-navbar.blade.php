{{-- Mobile & Tablet Top Bar (< 992px) --}}
<div class="prism-mobile-header d-flex d-lg-none align-items-center justify-content-between px-3">
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
        <img src="{{ asset('assets/logo/logo-sda-global-24.svg') }}" alt="PRISM SDA" class="brand-logo-img">
    </a>

    <div class="d-flex align-items-center gap-2">
        {{-- Theme switcher --}}
        <div class="theme-switcher-wrapper">
            <input type="checkbox" id="theme-switcher-mobile" onchange="document.getElementById('theme-switcher')?.click();">
            <label for="theme-switcher-mobile" class="mb-0">
                <div class="box">
                    <div class="ball"></div>
                    <div class="icons">
                        <i class='bx bxs-sun'></i>
                        <i class='bx bxs-moon'></i>
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>

@auth
    @php
        $role = strtolower(Auth::user()->rolesUsers->first()?->roles->name ?? '');
        $isAdmin = ($role === 'admin');
        $totalItems = $isAdmin ? 5 : 3;
    @endphp

    {{-- Fixed Role-Adaptive Bottom Navigation Bar (< 992px) --}}
    <nav class="prism-bottom-nav d-flex d-lg-none" id="prismBottomNav" data-item-count="{{ $totalItems }}">
        {{-- Sliding Active Indicator Pill --}}
        <div class="prism-nav-active-pill" id="prismNavActivePill"></div>

        {{-- 1. Dashboard (Index 0) --}}
        <a href="{{ route('dashboard') }}" class="nav-item-mobile {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-nav-index="0">
            <i data-feather="grid"></i>
            <span>Dashboard</span>
        </a>

        @if ($isAdmin)
            {{-- 2. Data Master (ADMIN ONLY - Index 1) --}}
            <a href="javascript:void(0);" class="nav-item-mobile {{ (request()->routeIs('brand.*') || request()->routeIs('currency.*') || request()->routeIs('user.*') || request()->routeIs('roles.*') || request()->routeIs('activity-log.*')) ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#mobileMasterModal" data-nav-index="1">
                <i data-feather="layers"></i>
                <span>Data Master</span>
            </a>
        @endif

        {{-- 3. Center FAB: Direct Mobile QR Camera Scanner --}}
        <a href="{{ route('scan.camera') }}" class="prism-center-fab-wrapper {{ (request()->routeIs('scan.qr') || request()->routeIs('scan.camera')) ? 'active' : '' }}" data-nav-index="{{ $isAdmin ? 2 : 1 }}">
            <div class="prism-center-fab-btn">
                <i data-feather="maximize"></i>
            </div>
            <span class="prism-center-fab-label">QR Scan</span>
        </a>

        @if ($isAdmin)
            {{-- 4. Pricelist (ADMIN ONLY - Index 3) --}}
            <a href="{{ route('pricelists.realtime') }}" class="nav-item-mobile {{ request()->routeIs('pricelists.*') ? 'active' : '' }}" data-nav-index="3">
                <i data-feather="pie-chart"></i>
                <span>Pricelist</span>
            </a>
        @endif

        {{-- 5. Profil --}}
        <a href="javascript:void(0);" class="nav-item-mobile" data-bs-toggle="modal" data-bs-target="#mobileProfileModal" data-nav-index="{{ $isAdmin ? 4 : 2 }}">
            <i data-feather="user"></i>
            <span>Profil</span>
        </a>
    </nav>
@endauth

<script>
    function updateMobileNavHeight() {
        const nav = document.getElementById('prismBottomNav');
        if (nav) {
            const height = nav.offsetHeight;
            document.documentElement.style.setProperty('--actual-bottom-nav-height', `${height}px`);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateMobileNavHeight();
        const nav = document.getElementById('prismBottomNav');
        const pill = document.getElementById('prismNavActivePill');
        if (!nav || !pill) return;

        const itemCount = parseInt(nav.getAttribute('data-item-count') || '5', 10);
        pill.style.width = `calc((100% - 8px) / ${itemCount})`;

        function updatePillPosition(activeIndex) {
            if (activeIndex === null || activeIndex === undefined || isNaN(activeIndex)) return;
            const percentage = activeIndex * 100;
            pill.style.transform = `translateX(${percentage}%)`;
        }

        const activeItem = nav.querySelector('.nav-item-mobile.active, .prism-center-fab-wrapper.active');
        if (activeItem) {
            const index = parseInt(activeItem.getAttribute('data-nav-index') || '0', 10);
            updatePillPosition(index);
        } else {
            pill.style.opacity = '0';
        }

        nav.querySelectorAll('[data-nav-index]').forEach(item => {
            item.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-nav-index') || '0', 10);
                pill.style.opacity = '1';
                updatePillPosition(idx);
            });
        });
    });

    window.addEventListener('resize', updateMobileNavHeight);
    window.addEventListener('orientationchange', updateMobileNavHeight);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', updateMobileNavHeight);
    }
</script>

{{-- Mobile Data Master Bottom Sheet Modal --}}
<div class="modal fade modal-bottom-sheet d-lg-none" id="mobileMasterModal" tabindex="-1" aria-labelledby="mobileMasterModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="mobileMasterModalLabel">Data Master</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-3">
                    @auth
                        @php $role = strtolower(Auth::user()->rolesUsers->first()?->roles->name ?? ''); @endphp
                        @if ($role === 'admin')
                            {{-- Brand (Logo) --}}
                            <div class="col-6">
                                <a href="{{ route('brand.index') }}" class="card border shadow-sm rounded-3 p-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                                    <div class="tile-icon-box bg-soft-success text-success mb-2" style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="image" class="icon-md"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 fs-13px">Brand (Logo)</h6>
                                </a>
                            </div>

                            {{-- Currency --}}
                            <div class="col-6">
                                <a href="{{ route('currency.index') }}" class="card border shadow-sm rounded-3 p-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                                    <div class="tile-icon-box bg-soft-info text-info mb-2" style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="dollar-sign" class="icon-md"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 fs-13px">Currency</h6>
                                </a>
                            </div>

                            {{-- User Login --}}
                            <div class="col-6">
                                <a href="{{ route('user.index') }}" class="card border shadow-sm rounded-3 p-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                                    <div class="tile-icon-box bg-soft-warning text-warning mb-2" style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="users" class="icon-md"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 fs-13px">User Login</h6>
                                </a>
                            </div>

                            {{-- Roles --}}
                            <div class="col-6">
                                <a href="{{ route('roles.index') }}" class="card border shadow-sm rounded-3 p-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                                    <div class="tile-icon-box bg-soft-secondary text-secondary mb-2" style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="shield" class="icon-md"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 fs-13px">Roles</h6>
                                </a>
                            </div>

                            {{-- Activity Log --}}
                            <div class="col-6">
                                <a href="{{ route('activity-log.index') }}" class="card border shadow-sm rounded-3 p-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                                    <div class="tile-icon-box bg-soft-dark text-dark mb-2" style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="activity" class="icon-md"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 fs-13px">Activity Log</h6>
                                </a>
                            </div>
                        @else
                            <div class="col-12 text-center py-4">
                                <div class="bg-soft-warning text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                    <i data-feather="lock" class="icon-lg"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Akses Khusus Admin</h6>
                                <p class="text-secondary small mb-0 fs-12px px-2">Menu Data Master dan Pengaturan Sistem hanya dapat diakses oleh pengguna dengan role Admin.</p>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mobile Profile Quick Modal --}}
<div class="modal fade modal-bottom-sheet d-lg-none" id="mobileProfileModal" tabindex="-1" aria-labelledby="mobileProfileModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="mobileProfileModalLabel">Profil Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <img class="w-80px h-80px rounded-circle mb-3 border shadow-sm" src="{{ asset('assets/faces/300-15.jpg') }}" alt="Profile">
                @auth
                    <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-secondary mb-3">{{ Auth::user()->email }}</p>
                    <span class="badge bg-primary px-3 py-2 rounded-pill fs-13px mb-4">
                        <i data-feather="shield" class="icon-xs me-1"></i> {{ ucfirst(Auth::user()->rolesUsers->first()?->roles->name ?? 'User') }}
                    </span>
                @endauth
                <div class="d-grid gap-2 mt-2">
                    <button class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                        <i data-feather="log-out" class="icon-sm"></i>
                        <span>Log Out</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
