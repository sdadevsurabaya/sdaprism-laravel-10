@extends('layouts.layout')

<style>
    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: left !important;
    }

    .mobile-pricelist-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    .mobile-pricelist-card:hover, .mobile-pricelist-card:active {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .bg-soft-primary {
        background-color: rgba(114, 124, 245, 0.12) !important;
        color: #727cf5 !important;
    }
</style>

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricelist</li>
        </ol>
    </nav>

    {{-- Mobile & Tablet View (< 992px): Standalone Responsive List View without Outer Card Container --}}
    <div class="d-block d-lg-none mb-4" style="padding-bottom: 76px !important;">
        {{-- Header & Add New Button --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0 fs-16px text-uppercase">Daftar Pricelist</h5>
            @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
                <a href="{{ route('pricelists.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm">
                    <i data-feather="plus" class="icon-sm"></i>
                    <span>Add New</span>
                </a>
            @endif
        </div>

        {{-- Mobile Search Input --}}
        <div class="mb-3">
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" class="icon-sm"></i></span>
                <input type="text" id="mobile-search" class="form-control border-start-0 ps-0 fs-14px py-2" placeholder="Cari pricelist...">
            </div>
        </div>

        {{-- Mobile List Items (Standalone Cards) --}}
        <div id="mobile-card-container" class="d-flex flex-column gap-3">
            @forelse ($data as $item)
                <div class="card prism-mobile-card border-0 shadow-sm rounded-3 pricelist-item-card bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-soft-primary px-2.5 py-1 rounded-pill fw-bold fs-11px">
                                #{{ $loop->iteration }}
                            </span>
                            <small class="text-secondary d-flex align-items-center fs-12px">
                                <i data-feather="calendar" class="icon-xs me-1"></i>
                                {{ $item->date }}
                            </small>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 card-title-text fs-14px">
                            <i data-feather="file-text" class="icon-sm me-1.5 text-primary"></i>
                            {{ $item->title ?? 'Tanpa Judul' }}
                        </h6>

                        <div class="d-flex gap-2 pt-2 border-top">
                            <a href="{{ route('pricelists.show', $item->id) }}"
                                class="btn btn-sm btn-primary flex-fill d-flex align-items-center justify-content-center gap-1 py-1.5 fs-13px fw-semibold">
                                <i data-feather="list" class="icon-xs"></i> List
                            </a>
                            @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
                                <a href="{{ route('pricelists.edit', $item->id) }}"
                                    class="btn btn-sm btn-outline-warning flex-fill d-flex align-items-center justify-content-center gap-1 py-1.5 fs-13px fw-semibold">
                                    <i data-feather="edit" class="icon-xs"></i> Edit
                                </a>

                                <a href="{{ route('pricelist.pdf', $item->id) }}"
                                    class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center gap-1 py-1.5 fs-13px fw-semibold"
                                    target="_blank">
                                    <i data-feather="file" class="icon-xs"></i> PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3 text-center py-4 text-muted">
                    <div class="card-body">
                        <i data-feather="inbox" class="mb-2 text-secondary" style="width: 36px; height: 36px;"></i>
                        <p class="mb-0 fs-13px">Belum ada data pricelist.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Mobile Pagination Controls --}}
        <div id="mobile-pagination-wrapper" class="d-flex flex-column align-items-center gap-2 mt-4">
            <small id="mobile-page-info" class="text-muted fs-12px"></small>
            <nav>
                <ul id="mobile-pagination-nav" class="pagination pagination-sm mb-0 flex-wrap justify-content-center"></ul>
            </nav>
        </div>

        @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
            <div class="prism-fab-container d-lg-none">
                <a href="{{ route('pricelists.create') }}" class="prism-fab-btn" aria-label="Add Pricelist">
                    <i data-feather="plus"></i>
                </a>
            </div>
        @endif
    </div>

    {{-- Desktop View (>= 992px): Table in Main Card Container --}}
    <div class="d-none d-lg-block">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Daftar Pricelist</h5>

                        {{-- Tombol Add New hanya untuk admin --}}
                        @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
                            <a href="{{ route('pricelists.create') }}" type="button"
                                class="btn btn-outline-primary btn-icon-text">
                                <i class="btn-icon-prepend" data-feather="plus"></i>
                                Add New
                            </a>
                        @endif
                    </div>

                    {{-- Desktop View: Table --}}
                    <div class="table-responsive">
                        <table id="pricelist" class="table table-striped align-middle w-100 nowrap">
                            <thead class="text-start">
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Title</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-start">
                                @php $no = 1; @endphp
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->date }}</td>
                                        <td>{{ $item->title }}</td>

                                        <td>
                                            <a href="{{ route('pricelists.show', $item->id) }}"
                                                class="btn btn-sm btn-primary btn-icon-text">
                                                <i data-feather="list" class="btn-icon-prepend"></i> List
                                            </a>
                                            @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
                                                <a href="{{ route('pricelists.edit', $item->id) }}"
                                                    class="btn btn-sm btn-primary btn-icon-text">
                                                    <i data-feather="edit" class="btn-icon-prepend"></i> Edit
                                                </a>

                                                <a href="{{ route('pricelist.pdf', $item->id) }}"
                                                    class="btn btn-sm btn-primary btn-icon-text" target="_blank">
                                                    <i data-feather="file" class="btn-icon-prepend"></i> PDF
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DataTables untuk desktop
            if (document.getElementById('pricelist')) {
                const dt = new DataTable('#pricelist', {
                    responsive: {
                        details: {
                            type: 'column',
                            target: 0,
                            renderer: function(api, rowIdx, columns) {
                                const rows = columns
                                    .filter(col => col.hidden)
                                    .map(col => (
                                        '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col
                                        .columnIndex + '">' +
                                        '<td class="fw-semibold pe-3">' + col.title + ':</td>' +
                                        '<td>' + col.data + '</td>' +
                                        '</tr>'
                                    ))
                                    .join('');

                                return rows ? $('<table class="table table-sm mb-0"><tbody/>').append(
                                    rows) : false;
                            }
                        }
                    },
                    columnDefs: [{
                            targets: 0,
                            className: 'dtr-control',
                            orderable: false
                        },
                        {
                            targets: 2,
                            responsivePriority: 1
                        },
                        {
                            targets: 1,
                            responsivePriority: 3
                        },
                        {
                            targets: 3,
                            responsivePriority: 4
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    pagingType: 'simple_numbers',
                    autoWidth: false
                });

                dt.search('').draw();
            }

            // Real-time Search & Pagination untuk Mobile Cards
            const mobileSearch = document.getElementById('mobile-search');
            const cards = Array.from(document.querySelectorAll('.pricelist-item-card'));
            const pageInfo = document.getElementById('mobile-page-info');
            const pageNav = document.getElementById('mobile-pagination-nav');
            const itemsPerPage = 10;
            let currentPage = 1;

            function updateMobileView() {
                const query = mobileSearch ? mobileSearch.value.toLowerCase().trim() : '';
                const matchingCards = cards.filter(card => {
                    const text = card.textContent.toLowerCase();
                    return text.includes(query);
                });

                const totalItems = matchingCards.length;
                const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;

                cards.forEach(card => card.classList.add('d-none'));

                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                matchingCards.slice(start, end).forEach(card => card.classList.remove('d-none'));

                if (pageInfo) {
                    if (totalItems === 0) {
                        pageInfo.textContent = 'Tidak ada data ditemukan';
                    } else {
                        const displayStart = start + 1;
                        const displayEnd = Math.min(end, totalItems);
                        pageInfo.textContent = `Menampilkan ${displayStart}-${displayEnd} dari ${totalItems} data`;
                    }
                }

                if (pageNav) {
                    pageNav.innerHTML = '';
                    if (totalPages <= 1) return;

                    const prevLi = document.createElement('li');
                    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                    prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)">&laquo;</a>`;
                    prevLi.addEventListener('click', () => {
                        if (currentPage > 1) {
                            currentPage--;
                            updateMobileView();
                        }
                    });
                    pageNav.appendChild(prevLi);

                    // Truncated pagination range logic
                    const pages = [];
                    if (totalPages <= 5) {
                        for (let i = 1; i <= totalPages; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        if (currentPage > 3) pages.push('...');
                        const startPage = Math.max(2, currentPage - 1);
                        const endPage = Math.min(totalPages - 1, currentPage + 1);
                        for (let i = startPage; i <= endPage; i++) {
                            if (!pages.includes(i)) pages.push(i);
                        }
                        if (currentPage < totalPages - 2) {
                            if (!pages.includes('...')) pages.push('...');
                        }
                        if (!pages.includes(totalPages)) pages.push(totalPages);
                    }

                    pages.forEach(p => {
                        const li = document.createElement('li');
                        if (p === '...') {
                            li.className = 'page-item disabled';
                            li.innerHTML = `<span class="page-link">&hellip;</span>`;
                        } else {
                            li.className = `page-item ${p === currentPage ? 'active' : ''}`;
                            li.innerHTML = `<a class="page-link" href="javascript:void(0)">${p}</a>`;
                            li.addEventListener('click', () => {
                                currentPage = p;
                                updateMobileView();
                            });
                        }
                        pageNav.appendChild(li);
                    });

                    const nextLi = document.getElementById('next-li') || document.createElement('li');
                    const nextLiElem = document.createElement('li');
                    nextLiElem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                    nextLiElem.innerHTML = `<a class="page-link" href="javascript:void(0)">&raquo;</a>`;
                    nextLiElem.addEventListener('click', () => {
                        if (currentPage < totalPages) {
                            currentPage++;
                            updateMobileView();
                        }
                    });
                    pageNav.appendChild(nextLiElem);
                }
            }

            if (mobileSearch) {
                mobileSearch.addEventListener('input', function() {
                    currentPage = 1;
                    updateMobileView();
                });
            }

            if (cards.length > 0) {
                updateMobileView();
            }

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endpush
