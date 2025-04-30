@extends('layouts.layout')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Forms</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricelist</li>
        </ol>
    </nav>
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Input Form Pricelist</h6>
                <form class="forms-sample">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Header Logo</label>
                            <input class="form-control mb-4 mb-md-0" data-inputmask="'alias': 'datetime'"
                                data-inputmask-inputformat="dd/mm/yyyy" inputmode="numeric">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" data-inputmask="'alias': 'datetime'"
                                data-inputmask-inputformat="hh:mm tt" inputmode="numeric">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Footer Text</label>
                            <input class="form-control mb-4 mb-md-0" data-inputmask="'alias': 'datetime'"
                                data-inputmask-inputformat="dd/mm/yyyy HH:MM:ss" inputmode="numeric">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <div class="input-group flatpickr  me-2 mb-2 mb-md-0" id="dashboardDate">
                                <span class="input-group-text input-group-addon bg-transparent" data-toggle><i
                                        data-feather="calendar" class="text-primary"></i></span>
                                <input type="text" class="form-control bg-transparent" placeholder="Select date"
                                    data-input>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <select class="form-select" name="currency" id="currency">
                                <option selected="" disabled="">Select</option>
                                <option>IDR</option>
                                <option>USD</option>
                                <option>SGD</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Show Payment Method</label>
                            <select class="form-select" name="payment" id="payment">
                                <option selected="" disabled="">Select</option>
                                <option>Ya</option>
                                <option>Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea id="maxlength-textarea" class="form-control" maxlength="100" rows="8"
                                placeholder="This textarea has a limit of 100 chars."></textarea>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection