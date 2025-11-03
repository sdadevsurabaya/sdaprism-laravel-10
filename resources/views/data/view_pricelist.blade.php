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
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricelist</li>
        </ol>
    </nav>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex w-100 justify-content-end">

                    <a href="{{ route('pricelists.create') }}" type="button"
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
                                <th>Date</th>
                                <th>Title</th>
                                <th>Create By</th>
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
                                    <td>{{ $item->date }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                         <a href="{{ route('pricelists.show', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text">
                                            <i class="btn-icon-prepend" data-feather="list"></i>
                                            List
                                        </a>
                                        <a href="{{ route('pricelists.edit', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text">
                                            <i class="btn-icon-prepend" data-feather="edit"></i>
                                            Edit
                                        </a>
                                        <a href="{{ route('pricelist.pdf', $item->id) }}" class="btn btn-sm btn-primary btn-icon-text" target="_blank">
                                            <i class="btn-icon-prepend" data-feather="file"></i>
                                            PDF
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
