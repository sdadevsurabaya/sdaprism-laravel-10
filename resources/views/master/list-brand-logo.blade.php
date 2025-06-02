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

                    <a href="{{ route('brand.create') }}" type="button"
                        class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Add New
                    </a>
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
                                        <a href="{{ route('brand.edit', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text">
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

    <!-- Modal -->
    <div class="modal fade" id="createUser" tabindex="-1" aria-labelledby="createUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="createUserLabel">Create User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <form class="forms-sample">
                                    <div class="mb-3">
                                        <label for="exampleInputUsername1" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="exampleInputUsername1"
                                            autocomplete="off" placeholder="Username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="exampleInputEmail1"
                                            placeholder="Email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleInputPassword1" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1"
                                            autocomplete="off" placeholder="Password">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Departemen</label>
                                        <input type="text" class="form-control" id="exampleInputUsername1"
                                            autocomplete="off" placeholder="Departemen">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <select class="form-select mb-3">
                                            <option selected="">Select</option>
                                            <option value="1">Manager </option>
                                            <option value="2">Supervisor</option>
                                            <option value="3">Staff</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Roles</label>
                                        <select class="form-select mb-3">
                                            <option selected="">Select</option>
                                            <option value="1">Admin</option>
                                            <option value="2">Member</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select mb-3">
                                            <option selected="">Select</option>
                                            <option value="1">Active</option>
                                            <option value="2">Not Active</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button class="btn btn-secondary">Cancel</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        new DataTable('#pricelist');
    </script>
@endpush
