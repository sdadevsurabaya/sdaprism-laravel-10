@extends('layouts.layout')

@section('content')
    <style>
        table.dataTable th.dt-type-numeric,
        table.dataTable th.dt-type-date,
        table.dataTable td.dt-type-numeric,
        table.dataTable td.dt-type-date {
            text-align: left !important;
        }
    </style>
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">User</li>
        </ol>
    </nav>
    <div class="flex-wrap mb-2 d-flex justify-content-end text-nowrap">
        <button type="button" class="mb-2 btn btn-outline-primary btn-icon-text me-2 mb-md-0" data-bs-toggle="modal"
            data-bs-target="#modalCreateUser">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            Create
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            {{-- Mobile & Tablet Card List --}}
            <div class="d-block d-lg-none">
                <div class="mb-3">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" class="icon-sm"></i></span>
                        <input type="text" id="mobile-user-search" class="form-control border-start-0 ps-0" placeholder="Cari pengguna...">
                    </div>
                </div>

                <div id="mobile-user-card-container" class="d-flex flex-column gap-3">
                    @forelse ($data as $item)
                        <div class="card prism-mobile-card border-0 shadow-sm rounded-3 user-item-card">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-soft-primary px-2 py-1 rounded-pill fw-semibold">
                                        #{{ $loop->iteration }}
                                    </span>
                                    <span class="badge bg-primary px-2 py-1 rounded-pill">
                                        {{ $item->rolesUsers->first()?->roles->name ?? '-' }}
                                    </span>
                                </div>

                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold fs-5 border" style="width: 44px; height: 44px;">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $item->name }}</h6>
                                        <p class="text-secondary small mb-0 text-truncate"><i data-feather="mail" class="icon-xs me-1"></i>{{ $item->email }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-outline-warning flex-fill d-flex align-items-center justify-content-center btn-edit-roles"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                        data-email="{{ $item->email }}"
                                        data-roleid="{{ $item->rolesUsers->first()?->roles->id }}"
                                        data-userroleid="{{ $item->rolesUsers->first()?->id }}"
                                        data-url="{{ route('user.update', $item->id) }}">
                                        <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('user.destroy', $item->id) }}" method="POST" class="flex-fill"
                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                                            <i data-feather="trash" class="icon-sm me-1"></i> Delete
                                        </button>
                                    </form>

                                    <form action="{{ route('login.as', $item->id) }}" method="POST" class="w-100"
                                        onsubmit="return confirm('Are you sure you want to login as {{ $item->name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                            <i data-feather="users" class="icon-sm me-1"></i> Login As
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i data-feather="inbox" class="mb-2" style="width: 36px; height: 36px;"></i>
                            <p class="mb-0">Belum ada data user.</p>
                        </div>
                    @endforelse
                </div>

                <div id="mobile-user-pagination-wrapper" class="d-flex flex-column align-items-center gap-2 mt-4">
                    <small id="mobile-user-page-info" class="text-muted"></small>
                    <nav>
                        <ul id="mobile-user-pagination-nav" class="pagination pagination-sm mb-0 flex-wrap justify-content-center"></ul>
                    </nav>
                </div>

                <div class="prism-fab-container d-lg-none">
                    <button type="button" class="prism-fab-btn" data-bs-toggle="modal" data-bs-target="#modalCreateUser" aria-label="Add User">
                        <i data-feather="plus"></i>
                    </button>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="table-responsive d-none d-lg-block">
                <table id="user" class="table table-responsive">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 0;
                        @endphp
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ++$no }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>
                                    {{ $item->rolesUsers->first()?->roles->name ?? '-' }}
                                </td>

                                <td>
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-primary btn-icon-text btn-edit-roles"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                        data-email="{{ $item->email }}"
                                        data-roleid="{{ $item->rolesUsers->first()?->roles->id }}"
                                        data-userroleid="{{ $item->rolesUsers->first()?->id }}"
                                        data-url="{{ route('user.update', $item->id) }}">
                                        <i class="btn-icon-prepend" data-feather="edit"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('user.destroy', $item->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-primary btn-icon-text"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="btn-icon-prepend" data-feather="trash"></i>
                                            Delete
                                        </button>
                                    </form>
                                    <form action="{{ route('login.as', $item->id) }}" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Are you sure you want to login as {{ $item->name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary btn-icon-text">
                                            <i class="btn-icon-prepend" data-feather="users"></i>
                                            Login As
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal Create User -->
    <div class="modal fade" id="modalCreateUser" tabindex="-1" aria-labelledby="modalCreateUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-create-user" action="{{ route('user.store') }}" method="POST"
                    enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateUserLabel">Create User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" placeholder="Masukkan nama" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Nama wajib diisi.</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" placeholder="contoh@email.com" required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Email wajib diisi dengan format yang benar.</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                                placeholder="Minimal 8 karakter" minlength="8" required>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Password wajib diisi (minimal 8 karakter).</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Role <span class="text-danger">*</span></label>
                            <select class="form-select text-capitalize @error('roles_id') is-invalid @enderror" name="roles_id" required>
                                <option value="" disabled {{ old('roles_id') ? '' : 'selected' }}>Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('roles_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('roles_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Role wajib dipilih.</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSubmitUser">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Edit User -->
    <div class="modal fade" id="modalEditRoles" tabindex="-1" aria-labelledby="modalEditRolesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-edit-roles" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditUserLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit-user-name" required>
                            <div class="invalid-feedback">Nama wajib diisi.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="edit-user-email" required>
                            <div class="invalid-feedback">Email wajib diisi dengan format yang benar.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-secondary">(opsional)</span></label>
                            <input type="password" class="form-control" name="password" id="edit-user-password" minlength="8" placeholder="Kosongkan jika tidak ingin mengubah password">
                            <div class="invalid-feedback">Password minimal 8 karakter.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Role <span class="text-danger">*</span></label>
                            <select class="form-select text-capitalize" name="roles_id" id="edit-user-roles-id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Role wajib dipilih.</div>
                        </div>
                    </div>
                    <input type="hidden" id="edit-user-usersroles-id" name="roles_user_id">
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
            if (document.getElementById('user')) {
                new DataTable('#user', {
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-center'
                    }]
                });
            }

            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            @if ($errors->any())
                const createModal = new bootstrap.Modal(document.getElementById('modalCreateUser'));
                createModal.show();
            @endif

            document.querySelectorAll('.btn-edit-roles').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const email = this.dataset.email;
                    const roleId = this.dataset.roleid;
                    const userRoleId = this.dataset.userroleid;
                    const url = this.dataset.url;

                    document.querySelector('#form-edit-roles').action = url;
                    document.querySelector('#edit-user-name').value = name;
                    document.querySelector('#edit-user-email').value = email;
                    document.querySelector('#edit-user-roles-id').value = roleId;
                    document.querySelector('#edit-user-usersroles-id').value = userRoleId;
                    document.querySelector('#edit-user-password').value = '';

                    const editModal = new bootstrap.Modal(document.getElementById('modalEditRoles'));
                    editModal.show();
                });
            });

            // Real-time Search & Pagination untuk Mobile Cards
            const mobileSearch = document.getElementById('mobile-user-search');
            const cards = Array.from(document.querySelectorAll('.user-item-card'));
            const pageInfo = document.getElementById('mobile-user-page-info');
            const pageNav = document.getElementById('mobile-user-pagination-nav');
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

