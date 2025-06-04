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
                        class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </a> --}}

                    <button type="button" class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0"
                        data-bs-toggle="modal" data-bs-target="#modalCreateCurrency">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </button>
                </div>
                <div class="table-responsive">
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
                                        {{-- <a href="{{ route('currency.edit', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text">
                                            <i class="btn-icon-prepend" data-feather="edit"></i>
                                            Edit
                                        </a> --}}

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            new DataTable('#currency-table', {
                columnDefs: [{
                    targets: '_all', // or use [0, 1, 2] for specific columns
                    className: 'text-center'
                }]
            });

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

                    const editModal = new bootstrap.Modal(document.getElementById(
                        'modalEditCurrency'));
                    editModal.show();
                });
            });
        });
    </script>
@endpush
