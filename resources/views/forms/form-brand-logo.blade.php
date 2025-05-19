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
                    <a href="{{ route('brand.index') }}" id="save" class="btn btn-outline-secondary">Kembali</a>
                    <div class="btn-group" role="group" aria-label="Default button group">

                        @if (isset($brand))
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Update Data</button>
                        @else
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Simpan Data</button>
                        @endif

                    </div>
                </div>
                <h6 class="card-title mt-3">Input Form Brand</h6>
                @if (isset($brand))
                    <form id="form-pricelist" class="forms-sample" action="{{ route('brand.update', $brand->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @method('PUT')
                    @else
                        <form id="form-pricelist" class="forms-sample" action="{{ route('brand.store') }}" method="POST"
                            enctype="multipart/form-data">
                @endif
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $brand->name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto</label>
                        <input type="file" class="form-control" id="logo" name="logo"
                            value="{{ old('logo', $brand->logo_path ?? '') }}">
                    </div>
                </div>
                @if (isset($brand))
                    <div class="row mb-3">
                        <div class="col d-flex justify-content-center">
                            <img src="{{ $brand->logo_path ? '/' . $brand->logo_path : '' }}" alt="" width="70%"
                                height="auto">
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
            const form = document.getElementById('form-pricelist');

            if (btnSave && form) {
                btnSave.addEventListener('click', function() {
                    form.submit();
                });
            }
        });
    </script>
@endpush
