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
                <div class="table-responsive">
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
            // Initialize DataTable
            new DataTable('#roles-table', {
                columnDefs: [{
                    targets: '_all', // or use [0, 1, 2] for specific columns
                    className: 'text-center'
                }]
            });

            document.querySelectorAll('.btn-edit-roles').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const code = this.dataset.code;
                    const symbol = this.dataset.symbol;
                    const name = this.dataset.name;
                    const is_active = this.dataset.is_active;
                    const url = this.dataset.url;

                    document.querySelector('#form-edit-roles').action = url;
                    document.querySelector('#edit-roles-name').value = name;

                    const editModal = new bootstrap.Modal(document.getElementById(
                        'modalEditRoles'));
                    editModal.show();
                });
            });
        });
    </script>
@endpush
