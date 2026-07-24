@extends('layouts.layout')

<style>
    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: left !important;
    }
</style>

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Roles</li>
        </ol>
    </nav>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex w-100 justify-content-end">

                    <button type="button" class="mb-2 btn btn-outline-primary btn-icon-text me-2 mb-md-0"
                        data-bs-toggle="modal" data-bs-target="#modalCreateRoles">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </button>
                </div>
                {{-- Mobile Card List --}}
                <div class="d-block d-md-none">
                    <div class="mb-3">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" class="icon-sm"></i></span>
                            <input type="text" id="mobile-role-search" class="form-control border-start-0 ps-0" placeholder="Cari role...">
                        </div>
                    </div>

                    <div id="mobile-role-card-container" class="d-flex flex-column gap-3">
                        @forelse ($data as $item)
                            <div class="card border-0 shadow-sm rounded-3 role-item-card">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-soft-primary px-2 py-1 rounded-pill fw-semibold">
                                            #{{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <i data-feather="shield" class="icon-md text-primary"></i>
                                        <h6 class="fw-bold text-dark mb-0 text-capitalize">{{ $item->name }}</h6>
                                    </div>
                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-warning flex-fill d-flex align-items-center justify-content-center btn-edit-roles"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-url="{{ route('roles.update', $item->id) }}">
                                            <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                        </a>
                                        <a href="{{ route('roles.destroy', $item->id) }}"
                                            class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center" target="_blank">
                                            <i data-feather="trash" class="icon-sm me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i data-feather="inbox" class="mb-2" style="width: 36px; height: 36px;"></i>
                                <p class="mb-0">Belum ada data role.</p>
                            </div>
                        @endforelse
                    </div>

                    <div id="mobile-role-pagination-wrapper" class="d-flex flex-column align-items-center gap-2 mt-4">
                        <small id="mobile-role-page-info" class="text-muted"></small>
                        <nav>
                            <ul id="mobile-role-pagination-nav" class="pagination pagination-sm mb-0 flex-wrap justify-content-center"></ul>
                        </nav>
                    </div>
                </div>

                {{-- Desktop Table --}}
                <div class="table-responsive d-none d-md-block">
                    <table id="roles-table" class="table table-responsive" style="text-align: center;">
                        <thead style="text-align: center;">
                            <tr>
                                <th>No</th>
                                <th>Roles</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php
                            $no = 1;
                        @endphp
                        <tbody>

                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary btn-icon-text btn-edit-roles"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-url="{{ route('roles.update', $item->id) }}">
                                            <i class="btn-icon-prepend" data-feather="edit"></i>
                                            Edit
                                        </a>
                                        <a href="{{ route('roles.destroy', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text" target="_blank">
                                            <i class="btn-icon-prepend" data-feather="trash"></i>
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create roles -->
    <div class="modal fade" id="modalCreateRoles" tabindex="-1" aria-labelledby="modalCreateRolesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-create-roles" action="{{ route('roles.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateRolesLabel">Create Role User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Edit roles -->
    <div class="modal fade" id="modalEditRoles" tabindex="-1" aria-labelledby="modalEditRolesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-edit-roles" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditRolesLabel">Edit Role User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-roles-id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-roles-name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('components.toast')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('roles-table')) {
                new DataTable('#roles-table', {
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-center'
                    }]
                });
            }

            document.querySelectorAll('.btn-edit-roles').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const url = this.dataset.url;

                    document.querySelector('#form-edit-roles').action = url;
                    document.querySelector('#edit-roles-name').value = name;

                    const editModal = new bootstrap.Modal(document.getElementById('modalEditRoles'));
                    editModal.show();
                });
            });

            // Real-time Search & Pagination untuk Mobile Cards
            const mobileSearch = document.getElementById('mobile-role-search');
            const cards = Array.from(document.querySelectorAll('.role-item-card'));
            const pageInfo = document.getElementById('mobile-role-page-info');
            const pageNav = document.getElementById('mobile-role-pagination-nav');
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

                    const nextLi = document.createElement('li');
                    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                    nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)">&raquo;</a>`;
                    nextLi.addEventListener('click', () => {
                        if (currentPage < totalPages) {
                            currentPage++;
                            updateMobileView();
                        }
                    });
                    pageNav.appendChild(nextLi);
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

