@extends('layouts.layout')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quotation</li>
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
                                <th>Customer</th>
                                <th>CP</th>
                                <th>Payement</th>
                                <th>Total</th>
                                <th>Create</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                            <tr>
                               <td>1</td>
                               <td>23-02-2025</td>
                               <td>ABS</td>
                               <td>Mr Wang</td>
                               <td>Cash</td>
                               <td>20.000</td>
                               <td>Admin</td>
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
