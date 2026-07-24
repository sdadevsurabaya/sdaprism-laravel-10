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
            <li class="breadcrumb-item"><a href="#">Master</a></li>
            <li class="breadcrumb-item active" aria-current="page">Brand</li>
        </ol>
    </nav>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex w-100 justify-content-end">

                    {{-- <a href="{{ route('currency.create') }}" type="button"
                        class="mb-2 btn btn-outline-primary btn-icon-text me-2 mb-md-0">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </a> --}}

                    <button type="button" class="mb-2 btn btn-outline-primary btn-icon-text me-2 mb-md-0"
                        data-bs-toggle="modal" data-bs-target="#modalCreateCurrency">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </button>
                </div>
                {{-- Mobile Card List --}}
                <div class="d-block d-md-none">
                    <div class="mb-3">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" class="icon-sm"></i></span>
                            <input type="text" id="mobile-currency-search" class="form-control border-start-0 ps-0" placeholder="Cari mata uang...">
                        </div>
                    </div>

                    <div id="mobile-currency-card-container" class="d-flex flex-column gap-3">
                        @forelse ($data as $item)
                            <div class="card border-0 shadow-sm rounded-3 currency-item-card">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-soft-primary px-2 py-1 rounded-pill fw-semibold">
                                            #{{ $loop->iteration }}
                                        </span>
                                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">
                                                {{ $item->name }}
                                                <small class="text-muted">({{ $item->code }})</small>
                                            </h6>
                                            <span class="fs-13px text-secondary">Symbol: <strong>{{ $item->symbol }}</strong></span>
                                        </div>
                                        <div class="bg-light rounded-circle px-3 py-2 fw-bold text-primary fs-5 border">
                                            {{ $item->symbol }}
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-warning flex-fill d-flex align-items-center justify-content-center btn-edit-currency"
                                            data-id="{{ $item->id }}" data-code="{{ $item->code }}"
                                            data-symbol="{{ $item->symbol }}" data-name="{{ $item->name }}"
                                            data-is_active="{{ $item->is_active }}"
                                            data-url="{{ route('currency.update', $item->id) }}">
                                            <i data-feather="edit" class="icon-sm me-1"></i> Edit
                                        </a>
                                        <a href="{{ route('currency.destroy', $item->id) }}"
                                            class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center" target="_blank">
                                            <i data-feather="trash" class="icon-sm me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i data-feather="inbox" class="mb-2" style="width: 36px; height: 36px;"></i>
                                <p class="mb-0">Belum ada data mata uang.</p>
                            </div>
                        @endforelse
                    </div>

                    <div id="mobile-currency-pagination-wrapper" class="d-flex flex-column align-items-center gap-2 mt-4">
                        <small id="mobile-currency-page-info" class="text-muted"></small>
                        <nav>
                            <ul id="mobile-currency-pagination-nav" class="pagination pagination-sm mb-0"></ul>
                        </nav>
                    </div>
                </div>

                {{-- Desktop Table --}}
                <div class="table-responsive d-none d-md-block">
                    <table id="currency-table" class="table table-responsive" style="text-align: center;">
                        <thead style="text-align: center;">
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>Symbol</th>
                                <th>Name</th>
                                <th>Status</th>
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
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->symbol }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->is_active ? 'Active' : 'In Active' }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary btn-icon-text btn-edit-currency"
                                            data-id="{{ $item->id }}" data-code="{{ $item->code }}"
                                            data-symbol="{{ $item->symbol }}" data-name="{{ $item->name }}"
                                            data-is_active="{{ $item->is_active }}"
                                            data-url="{{ route('currency.update', $item->id) }}">
                                            <i class="btn-icon-prepend" data-feather="edit"></i>
                                            Edit
                                        </a>
                                        <a href="{{ route('currency.destroy', $item->id) }}"
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

    <!-- Modal Create Currency -->
    <div class="modal fade" id="modalCreateCurrency" tabindex="-1" aria-labelledby="modalCreateCurrencyLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-create-currency" action="{{ route('currency.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateCurrencyLabel">Create Currency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency Code</label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Symbol</label>
                            <input type="text" class="form-control" name="symbol" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Is Active</label>
                            <select class="form-select text-capitalize" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
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


    <!-- Modal Edit Currency -->
    <div class="modal fade" id="modalEditCurrency" tabindex="-1" aria-labelledby="modalEditCurrencyLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-edit-currency" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditCurrencyLabel">Edit Currency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-currency-id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-currency-name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency Code</label>
                            <input type="text" class="form-control" name="code" id="edit-currency-code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Symbol</label>
                            <input type="text" class="form-control" name="symbol" id="edit-currency-symbol"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select text-capitalize" name="is_active" id="edit-currency-is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
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
            if (document.getElementById('currency-table')) {
                new DataTable('#currency-table', {
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-center'
                    }]
                });
            }

            document.querySelectorAll('.btn-edit-currency').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const code = this.dataset.code;
                    const symbol = this.dataset.symbol;
                    const name = this.dataset.name;
                    const is_active = this.dataset.is_active;
                    const url = this.dataset.url;

                    document.querySelector('#form-edit-currency').action = url;
                    document.querySelector('#edit-currency-code').value = code;
                    document.querySelector('#edit-currency-symbol').value = symbol;
                    document.querySelector('#edit-currency-name').value = name;
                    document.querySelector('#edit-currency-is_active').value = is_active;

                    const editModal = new bootstrap.Modal(document.getElementById('modalEditCurrency'));
                    editModal.show();
                });
            });

            // Real-time Search & Pagination untuk Mobile Cards
            const mobileSearch = document.getElementById('mobile-currency-search');
            const cards = Array.from(document.querySelectorAll('.currency-item-card'));
            const pageInfo = document.getElementById('mobile-currency-page-info');
            const pageNav = document.getElementById('mobile-currency-pagination-nav');
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

