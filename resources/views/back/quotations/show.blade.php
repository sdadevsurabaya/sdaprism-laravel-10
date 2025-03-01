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
                            <h5 class="card-title">No: {{ $quotation->no }}</h5>
                            <p class="card-text"><strong>Address Letter:</strong> {{ $quotation->address_letter }}</p>
                            <p class="card-text"><strong>Customer ID:</strong> {{ $quotation->customer_id }}</p>
                            <p class="card-text"><strong>Phone:</strong> {{ $quotation->phone }}</p>
                            <p class="card-text"><strong>Payment Type:</strong> {{ $quotation->payment_type }}</p>
                            <p class="card-text"><strong>Currency:</strong> {{ $quotation->currency }}</p>
                            <p class="card-text"><strong>Date:</strong> {{ $quotation->date }}</p>
                            <p class="card-text"><strong>Valid Until:</strong> {{ $quotation->valid_until }}</p>
                            <p class="card-text"><strong>Address:</strong> {{ $quotation->address }}</p>
                            <p class="card-text"><strong>Contact Person:</strong> {{ $quotation->contact_person }}</p>
                            <p class="card-text"><strong>Descriptions:</strong> {{ $quotation->descriptions }}</p>
                            <p class="card-text"><strong>Remarks:</strong> {{ $quotation->remarks }}</p>
                            <p class="card-text"><strong>Account Options:</strong> {{ $quotation->account_options }}</p>
                            <p class="card-text"><strong>Made By:</strong> {{ $quotation->made_by }}</p>
                            <p class="card-text"><strong>Sub Total:</strong> {{ $quotation->sub_total }}</p>
                            <p class="card-text"><strong>Additional Discount:</strong> {{ $quotation->additional_discount }}</p>
                            <p class="card-text"><strong>Additional Cost:</strong> {{ $quotation->additional_cost }}</p>
                            <p class="card-text"><strong>Total:</strong> {{ $quotation->total }}</p>
                            <p class="card-text"><strong>Deposit:</strong> {{ $quotation->deposit }}</p>
                            <p class="card-text"><strong>Grand Total:</strong> {{ $quotation->grand_total }}</p>
                            <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Back to List</a>
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
