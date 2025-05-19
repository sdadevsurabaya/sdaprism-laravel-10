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
                        <tbody style="text-align: left;">

                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->symbol }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->status }}</td>
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
@endsection

@push('scripts')
    <script>
        new DataTable('#pricelist');
    </script>
@endpush
