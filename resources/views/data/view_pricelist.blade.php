@extends('layouts.layout')

<style>
    table.dataTable th.dt-type-numeric, table.dataTable th.dt-type-date, table.dataTable td.dt-type-numeric, table.dataTable td.dt-type-date{
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
                <div class="table-responsive">
                    <table id="pricelist" class="table table-responsive">
                        <thead style="text-align: left;">
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Create</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                            <tr>
                                <td>Tiger Nixon</td>
                                <td>System Architect</td>
                                <td>Edinburgh</td>
                                <td>61</td>
                                <td>2011-04-25</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary btn-icon-text">
                                        <i class="btn-icon-prepend" data-feather="edit"></i>
                                       Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary btn-icon-text">
                                        <i class="btn-icon-prepend" data-feather="file"></i>
                                        PDF
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Tiger Nixon</td>
                                <td>System Architect</td>
                                <td>Edinburgh</td>
                                <td>61</td>
                                <td>2011-04-25</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary btn-icon-text">
                                        <i class="btn-icon-prepend" data-feather="edit"></i>
                                       Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary btn-icon-text">
                                        <i class="btn-icon-prepend" data-feather="file"></i>
                                        PDF
                                    </button>
                                </td>
                            </tr>
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
