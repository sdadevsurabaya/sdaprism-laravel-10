@extends('layouts.layout')

<style>
    .badge-create { background-color: #10b981; color: #fff; }
    .badge-update { background-color: #3b82f6; color: #fff; }
    .badge-delete { background-color: #ef4444; color: #fff; }
    .badge-auth { background-color: #8b5cf6; color: #fff; }
    .badge-scan { background-color: #f59e0b; color: #fff; }
    .badge-import { background-color: #06b6d4; color: #fff; }
    .badge-default { background-color: #6b7280; color: #fff; }
    .json-key { color: #d97706; font-weight: 600; }
    .json-string { color: #059669; }
    .json-number { color: #2563eb; }
    .json-boolean { color: #7c3aed; }
    .pagination-wrapper .pagination {
        margin-bottom: 0;
        gap: 2px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination-wrapper .page-item .page-link {
        font-size: 0.8125rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        color: #495057;
        background-color: #fff;
        border: 1px solid #dee2e6;
        box-shadow: none;
    }
    .pagination-wrapper .page-item.active .page-link {
        background-color: #6576ff;
        border-color: #6576ff;
        color: #fff;
    }
    .pagination-wrapper .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Activity Log</li>
        </ol>
    </nav>

    <!-- Statistics Widget -->
    <div class="row mb-3">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Log Hari Ini</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2 mt-2">{{ number_format($stats['today']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Aktivitas CRUD</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2 mt-2 text-primary">{{ number_format($stats['total_crud']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Otentikasi / Auth</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2 mt-2 text-info">{{ number_format($stats['total_auth']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">QR Scan / Lookup</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2 mt-2 text-warning">{{ number_format($stats['total_scan']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-header bg-transparent font-weight-bold">
            <i class="me-1" data-feather="filter"></i> Filter Activity Log
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('activity-log.index') }}">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label fs-12px text-secondary">Cari Keyword</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Cari deskripsi, IP, user..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12px text-secondary">Modul</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">-- Semua Modul --</option>
                            @foreach ($modules as $mod)
                                <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>
                                    {{ $mod }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12px text-secondary">Aksi</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">-- Semua Aksi --</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                    {{ $act }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12px text-secondary">User</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">-- Semua User --</option>
                            @foreach ($users as $usr)
                                <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>
                                    {{ $usr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px text-secondary">Tanggal</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-sm btn-primary">Filter Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Daftar Audit Trail & Activity Logs</h6>
            {{-- Mobile & Tablet View: Activity Log Cards --}}
            <div class="d-block d-lg-none mb-3">
                <div class="d-flex flex-column gap-3">
                    @forelse ($logs as $index => $log)
                        @php
                            $badgeClass = match ($log->action) {
                                'Create' => 'badge-create',
                                'Update' => 'badge-update',
                                'Delete' => 'badge-delete',
                                'Login', 'Logout', 'Login As', 'Failed Login' => 'badge-auth',
                                'QR Access', 'QR Lookup' => 'badge-scan',
                                'Import' => 'badge-import',
                                default => 'badge-default',
                            };
                        @endphp
                        <div class="card prism-mobile-card border-0 shadow-sm rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-soft-primary px-2 py-1 rounded-pill fw-semibold">
                                        #{{ $logs->firstItem() + $index }}
                                    </span>
                                    <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2 fs-12px text-muted border-bottom pb-2">
                                    <div>
                                        <i data-feather="clock" class="icon-xs me-1"></i>
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <small>{{ $log->created_at->diffForHumans() }}</small>
                                </div>

                                <div class="mb-2">
                                    <div class="fw-bold text-dark fs-14px">{{ $log->user_name ?? 'System' }}</div>
                                    <span class="badge bg-secondary fs-10px me-1">{{ $log->user_role ?? 'Guest' }}</span>
                                    <span class="badge bg-light text-dark border fs-10px">{{ $log->module }}</span>
                                </div>

                                <p class="text-secondary fs-13px mb-3 text-break">
                                    {{ $log->description }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <code class="fs-12px">{{ $log->ip_address ?? '-' }}</code>
                                    <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 btn-detail-log"
                                        data-id="{{ $log->id }}" data-module="{{ $log->module }}"
                                        data-action="{{ $log->action }}" data-user="{{ $log->user_name }}"
                                        data-role="{{ $log->user_role }}"
                                        data-time="{{ $log->created_at->format('d F Y - H:i:s') }}"
                                        data-ip="{{ $log->ip_address }}" data-agent="{{ $log->user_agent }}"
                                        data-description="{{ $log->description }}"
                                        data-properties='@json($log->properties)'>
                                        <i data-feather="eye" class="icon-sm"></i> Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i data-feather="inbox" class="icon-lg mb-2"></i>
                            <p class="mb-0">Belum ada data activity log yang tercatat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Desktop View: Table --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="160">Waktu</th>
                            <th>Pengguna</th>
                            <th>Modul</th>
                            <th>Aksi</th>
                            <th>Deskripsi Aktivitas</th>
                            <th>IP Address</th>
                            <th width="80">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $index => $log)
                            @php
                                $badgeClass = match ($log->action) {
                                    'Create' => 'badge-create',
                                    'Update' => 'badge-update',
                                    'Delete' => 'badge-delete',
                                    'Login', 'Logout', 'Login As', 'Failed Login' => 'badge-auth',
                                    'QR Access', 'QR Lookup' => 'badge-scan',
                                    'Import' => 'badge-import',
                                    default => 'badge-default',
                                };
                            @endphp
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold fs-13px">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $log->user_name ?? 'System' }}</div>
                                    <span class="badge bg-secondary fs-10px">{{ $log->user_role ?? 'Guest' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $log->module }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 280px;" title="{{ $log->description }}">
                                        {{ \Illuminate\Support\Str::limit($log->description, 50) }}
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $log->ip_address ?? '-' }}</code>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-outline-primary btn-detail-log"
                                        data-id="{{ $log->id }}" data-module="{{ $log->module }}"
                                        data-action="{{ $log->action }}" data-user="{{ $log->user_name }}"
                                        data-role="{{ $log->user_role }}"
                                        data-time="{{ $log->created_at->format('d F Y - H:i:s') }}"
                                        data-ip="{{ $log->ip_address }}" data-agent="{{ $log->user_agent }}"
                                        data-description="{{ $log->description }}"
                                        data-properties='@json($log->properties)'>
                                        <i data-feather="eye" class="icon-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i data-feather="inbox" class="icon-lg mb-2"></i>
                                    <p>Belum ada data activity log yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted fs-12px">
                    Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari total
                    {{ $logs->total() }} log
                </span>
                <div class="pagination-wrapper">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Log -->
    <div class="modal fade" id="modalDetailLog" tabindex="-1" aria-labelledby="modalDetailLogLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLogLabel">Detail Activity Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold text-secondary" width="120">Waktu:</td>
                                    <td id="detail-time"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-secondary">Pengguna:</td>
                                    <td id="detail-user"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-secondary">Role:</td>
                                    <td id="detail-role"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold text-secondary" width="120">Modul:</td>
                                    <td id="detail-module"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-secondary">Aksi:</td>
                                    <td id="detail-action"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-secondary">IP Address:</td>
                                    <td id="detail-ip"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-secondary">Deskripsi:</label>
                        <div id="detail-description" class="p-2 bg-light border rounded"></div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-secondary">User Agent / Browser:</label>
                        <div id="detail-agent" class="p-2 bg-light border rounded fs-12px text-break"></div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-secondary">Properti / Data Terdampak (JSON):</label>
                        <pre id="detail-properties" class="p-3 bg-dark text-light rounded" style="max-height: 250px; overflow-y: auto;"></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-detail-log').forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;

                    document.getElementById('detail-time').innerText = data.time || '-';
                    document.getElementById('detail-user').innerText = data.user || 'System';
                    document.getElementById('detail-role').innerText = data.role || 'Guest';
                    document.getElementById('detail-module').innerText = data.module || '-';
                    document.getElementById('detail-action').innerText = data.action || '-';
                    document.getElementById('detail-ip').innerText = data.ip || '-';
                    document.getElementById('detail-description').innerText = data.description || '-';
                    document.getElementById('detail-agent').innerText = data.agent || '-';

                    try {
                        const parsedProps = JSON.parse(data.properties);
                        document.getElementById('detail-properties').innerText = JSON.stringify(parsedProps, null, 2);
                    } catch (e) {
                        document.getElementById('detail-properties').innerText = data.properties || 'Tidak ada data properti.';
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalDetailLog'));
                    modal.show();
                });
            });
        });
    </script>
@endpush
