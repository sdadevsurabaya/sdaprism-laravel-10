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
                <!-- Tampilkan pesan sukses -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Tampilkan semua error -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="d-flex justify-content-between">
                    <a href="{{ route('currency.index') }}" id="save" class="btn btn-outline-secondary">Kembali</a>
                    <div class="btn-group" role="group" aria-label="Default button group">

                        @if (isset($currency))
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Update Data</button>
                        @else
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Simpan Data</button>
                        @endif

                    </div>
                </div>
                <h6 class="card-title mt-3">Input Form Brand</h6>
                @if (isset($currency))
                    <form id="form-currency" class="forms-sample" action="{{ route('currency.update', $currency->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @method('PUT')
                    @else
                        <form id="form-currency" class="forms-sample" action="{{ route('currency.store') }}" method="POST"
                            enctype="multipart/form-data">
                @endif
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $currency->name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Currency Code</label>
                        <input type="text" class="form-control" id="code" name="code"
                            value="{{ old('code', $currency->code ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Symbol</label>
                        <input type="text" class="form-control" id="symbol" name="symbol"
                            value="{{ old('symbol', $currency->symbol ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select text-capitalize" id="is_active">
                            <option value="1" {{ old('is_active', $currency->is_active ?? '') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $currency->is_active ?? '') == '0' ? 'selected' : '' }}>InActive</option>
                        </select>
                    </div>
                </div>
                @if (isset($currency))
                    <div class="row mb-3">
                        <div class="col d-flex justify-content-center">
                            <img src="{{ $currency->logo_path ? '/' . $currency->logo_path : '' }}" alt=""
                                width="70%" height="auto">
                        </div>
                    </div>
                @endif
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSave = document.getElementById('btnSave');
            const form = document.getElementById('form-currency');

            if (btnSave && form) {
                btnSave.addEventListener('click', function() {
                    form.submit();
                });
            }
        });
    </script>
@endpush
