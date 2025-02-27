@extends('back/layouts.layout')
@section('content')


<div class="d-flex flex-column flex-column-fluid">
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            {{-- header-start --}}
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Customers</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">Master</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Customers</li>
                        </ul>
                    </div>

                </div>
            </div>
            {{-- header-end --}}

            {{-- body-start --}}
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Products-->
                    <div class="card card-flush">
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->

                                <!--end::Search-->
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <p><strong>Nama Bisnis:</strong> {{ $customer->business_name }}</p>
                            <p><strong>Alamat:</strong> {{ $customer->address }}</p>
                            <p><strong>Kota:</strong> {{ $customer->city }}</p>
                            <p><strong>Provinsi:</strong> {{ $customer->province }}</p>
                            <p><strong>Kode Pos:</strong> {{ $customer->postcode }}</p>
                            <p><strong>Negara:</strong> {{ $customer->country }}</p>
                            <p><strong>Telepon:</strong> {{ $customer->telephone }}</p>
                            <p><strong>Fax:</strong> {{ $customer->fax }}</p>
                            <p><strong>PIC:</strong> {{ $customer->pic }}</p>
                            <p><strong>Mobile:</strong> {{ $customer->mobile }}</p>
                            <p><strong>Email:</strong> {{ $customer->email }}</p>
                            <p><strong>Syarat:</strong> {{ $customer->terms }}</p>
                            <p><strong>Jenis Bisnis:</strong> {{ $customer->business_type }}</p>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">< Back to List</a>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">Hapus</button>
                            </form>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Products-->
                </div>
            </div>
            {{-- body-end --}}

        </div>
    </div>
</div>


@endsection
