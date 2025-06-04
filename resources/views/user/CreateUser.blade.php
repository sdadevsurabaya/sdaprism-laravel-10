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
            <div class="table-responsive">
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
                                        data-userroleid="{{ $item->rolesUsers->first()->id }}"
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

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal Create roles -->
    <div class="modal fade" id="modalCreateUser" tabindex="-1" aria-labelledby="modalCreateUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-create-roles" action="{{ route('user.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateUserLabel">Create Role User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="text" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">User Role</label>
                            <select class="form-select text-capitalize" name="roles_id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
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


    <!-- Modal Edit roles -->
    <div class="modal fade" id="modalEditRoles" tabindex="-1" aria-labelledby="modalEditRolesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-edit-roles" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateUserLabel">Edit Role User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-user-name" required>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" id="edit-user-email" required>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-primary">(optional)</span></label>
                            <input type="text" class="form-control" name="password">
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">User Role</label>
                            <select class="form-select text-capitalize" name="roles_id" id="edit-user-roles-id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
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
            // Initialize DataTable
            new DataTable('#user', {
                columnDefs: [{
                    targets: '_all', // or use [0, 1, 2] for specific columns
                    className: 'text-center'
                }]
            });

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

                    const editModal = new bootstrap.Modal(document.getElementById(
                        'modalEditRoles'));
                    editModal.show();
                });
            });
        });
    </script>
@endpush
