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
            <li class="breadcrumb-item"><a href="#">Master</a></li>
            <li class="breadcrumb-item active" aria-current="page">Brand</li>
        </ol>
    </nav>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex w-100 justify-content-end">

                    <button type="button" class="mb-2 btn btn-outline-primary btn-icon-text me-2 mb-md-0"
                        data-bs-toggle="modal" data-bs-target="#modalCreateBrand">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </button>

                </div>
                {{-- Mobile Card List --}}
                <div class="d-block d-md-none">
                    <div class="mb-3">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" class="icon-sm"></i></span>
                            <input type="text" id="mobile-brand-search" class="form-control border-start-0 ps-0" placeholder="Cari brand...">
                        </div>
                    </div>

                    <div id="mobile-brand-card-container" class="d-flex flex-column gap-3">
                        @forelse ($data as $item)
                            <div class="card border-0 shadow-sm rounded-3 brand-item-card">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-soft-primary px-2 py-1 rounded-pill fw-semibold">
                                            #{{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="bg-light p-2 rounded-2 border d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <img src="{{ asset($item->logo_path) }}" alt="{{ $item->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $item->name }}</h6>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-warning flex-fill d-flex align-items-center justify-content-center btn-edit-brand"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-logo="{{ asset($item->logo_path) }}"
                                            data-url="{{ route('brand.update', $item->id) }}">
                                            <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                        </a>
                                        <a href="{{ route('brand.destroy', $item->id) }}"
                                            class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center" target="_blank">
                                            <i data-feather="trash" class="icon-sm me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i data-feather="inbox" class="mb-2" style="width: 36px; height: 36px;"></i>
                                <p class="mb-0">Belum ada data brand.</p>
                            </div>
                        @endforelse
                    </div>

                    <div id="mobile-brand-pagination-wrapper" class="d-flex flex-column align-items-center gap-2 mt-4">
                        <small id="mobile-brand-page-info" class="text-muted"></small>
                        <nav>
                            <ul id="mobile-brand-pagination-nav" class="pagination pagination-sm mb-0"></ul>
                        </nav>
                    </div>
                </div>

                {{-- Desktop Table --}}
                <div class="table-responsive d-none d-md-block">
                    <table id="pricelist" class="table table-responsive">
                        <thead style="text-align: left;">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php
                            $no = 1;
                        @endphp
                        <tbody style="text-align: left;">

                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div><img src="{{ asset($item->logo_path) }}" alt=""
                                                style="width: auto; height: 30px; border-radius: 0;"></div>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary btn-icon-text btn-edit-brand"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-logo="{{ asset($item->logo_path) }}"
                                            data-url="{{ route('brand.update', $item->id) }}">
                                            <i class="btn-icon-prepend" data-feather="edit"></i>
                                            Edit
                                        </a>
                                        <a href="{{ route('brand.destroy', $item->id) }}"
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

    <!-- Modal Create Brand -->
    <div class="modal fade" id="modalCreateBrand" tabindex="-1" aria-labelledby="modalCreateBrandLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-create-brand" action="{{ route('brand.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateBrandLabel">Create Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" class="form-control" name="logo">
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

    <!-- Modal Edit Brand -->
    <div class="modal fade" id="modalEditBrand" tabindex="-1" aria-labelledby="modalEditBrandLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-edit-brand" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditBrandLabel">Edit Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-brand-id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-brand-name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" class="form-control" name="logo" id="edit-brand-logo">
                            <div class="mt-2">
                                <img id="edit-brand-preview" src="" width="100" height="auto">
                            </div>
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
            if (document.getElementById('pricelist')) {
                new DataTable('#pricelist');
            }

            // Bind Edit Modal
            document.querySelectorAll('.btn-edit-brand').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const logo = this.dataset.logo;
                    const url = this.dataset.url;

                    document.querySelector('#form-edit-brand').action = url;
                    document.querySelector('#edit-brand-name').value = name;
                    document.querySelector('#edit-brand-preview').src = logo;

                    const editModal = new bootstrap.Modal(document.getElementById('modalEditBrand'));
                    editModal.show();
                });
            });

            // Real-time Search & Pagination untuk Mobile Cards
            const mobileSearch = document.getElementById('mobile-brand-search');
            const cards = Array.from(document.querySelectorAll('.brand-item-card'));
            const pageInfo = document.getElementById('mobile-brand-page-info');
            const pageNav = document.getElementById('mobile-brand-pagination-nav');
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

                    for (let i = 1; i <= totalPages; i++) {
                        const li = document.createElement('li');
                        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                        li.innerHTML = `<a class="page-link" href="javascript:void(0)">${i}</a>`;
                        li.addEventListener('click', () => {
                            currentPage = i;
                            updateMobileView();
                        });
                        pageNav.appendChild(li);
                    }

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

