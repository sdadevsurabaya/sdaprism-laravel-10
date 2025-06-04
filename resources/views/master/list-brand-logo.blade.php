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
                <div class="table-responsive">
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
            new DataTable('#pricelist');
            document.querySelectorAll('.btn-edit-brand').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const logo = this.dataset.logo;
                    const url = this.dataset.url;

                    document.querySelector('#form-edit-brand').action = url;
                    document.querySelector('#edit-brand-name').value = name;
                    document.querySelector('#edit-brand-preview').src = logo;

                    const editModal = new bootstrap.Modal(document.getElementById(
                        'modalEditBrand'));
                    editModal.show();
                });
            });
        });
    </script>
@endpush
