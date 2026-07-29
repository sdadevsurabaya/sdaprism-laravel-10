@extends('layouts.layout')

@section('content')
<style>
    .dash-tile-card {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .dash-tile-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border-color: rgba(0,0,0,0.12);
    }
    .tile-icon-box {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        margin-bottom: 12px;
    }
</style>

<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Main</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
    <div>
        <h3 class="mb-1 fw-bold text-dark">Welcome back, {{ Auth::user()->name ?? 'User' }}! 👋</h3>
        <p class="text-secondary mb-0">Pilih menu navigasi di bawah ini untuk menuju ke halaman terkait secara langsung.</p>
    </div>
    <div>
        @php $roleName = Auth::user()->rolesUsers->first()?->roles->name ?? 'User'; @endphp
        <span class="badge bg-primary px-3 py-2 fs-13px rounded-pill">
            <i data-feather="shield" class="icon-xs me-1"></i> Role: {{ ucfirst($roleName) }}
        </span>
    </div>
</div>

{{-- Quick Navigation App Launcher Grid --}}
<div class="row grid-margin g-3">
    {{-- Hose Assembly --}}
    <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('scan.qr') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
            <div class="tile-icon-box bg-soft-danger text-danger">
                <i data-feather="maximize" class="icon-lg"></i>
            </div>
            <h6 class="fw-bold text-dark mb-0 fs-15px">Hose Assembly</h6>
        </a>
    </div>

    {{-- Pricelist --}}
    <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('pricelists.index') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
            <div class="tile-icon-box bg-soft-primary text-primary">
                <i data-feather="pie-chart" class="icon-lg"></i>
            </div>
            <h6 class="fw-bold text-dark mb-0 fs-15px">Pricelist</h6>
        </a>
    </div>

    @auth
        @php $role = strtolower(Auth::user()->rolesUsers->first()?->roles->name ?? ''); @endphp
        @if ($role === 'admin')
            {{-- Brand Logo --}}
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('brand.index') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="tile-icon-box bg-soft-success text-success">
                        <i data-feather="image" class="icon-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 fs-15px">Brand (Logo)</h6>
                </a>
            </div>

            {{-- Currency --}}
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('currency.index') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="tile-icon-box bg-soft-info text-info">
                        <i data-feather="dollar-sign" class="icon-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 fs-15px">Currency</h6>
                </a>
            </div>

            {{-- User Management --}}
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('user.index') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="tile-icon-box bg-soft-warning text-warning">
                        <i data-feather="users" class="icon-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 fs-15px">User Login</h6>
                </a>
            </div>

            {{-- Roles --}}
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('roles.index') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="tile-icon-box bg-soft-secondary text-secondary">
                        <i data-feather="shield" class="icon-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 fs-15px">Roles</h6>
                </a>
            </div>

            {{-- Activity Log --}}
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('activity-log.index') }}" class="card dash-tile-card h-100 shadow-sm rounded-3 p-3 bg-white text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="tile-icon-box bg-soft-dark text-dark">
                        <i data-feather="activity" class="icon-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 fs-15px">Activity Log</h6>
                </a>
            </div>
        @endif
    @endauth
</div>

@endsection
