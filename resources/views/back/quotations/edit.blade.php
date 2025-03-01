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
                                Quotations List</h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">Master</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">Quotations</li>
                            </ul>
                        </div>

                    </div>
                </div>
                {{-- header-end --}}

                {{-- body-start --}}
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
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
                                <form action="{{ route('quotations.update', $quotation->id) }}" method="POST">

                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="no">No</label>
                                            <input type="text" class="form-control @error('no') is-invalid @enderror" id="no" name="no" value="{{ old('no', $quotation->no) }}" required>
                                            @error('no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="address_letter">Address Letter</label>
                                            <textarea class="form-control @error('address_letter') is-invalid @enderror" id="address_letter" name="address_letter">{{ old('address_letter', $quotation->address_letter) }}</textarea>
                                            @error('address_letter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="customer_id">Customer ID</label>
                                            <input type="text" class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" value="{{ old('customer_id', $quotation->customer_id) }}">
                                            @error('customer_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $quotation->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="payment_type">Payment Type</label>
                                            <select class="form-control @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                                <option value="CASH" {{ old('payment_type', $quotation->payment_type) == 'CASH' ? 'selected' : '' }}>CASH</option>
                                                <option value="BANK TRANSFER" {{ old('payment_type', $quotation->payment_type) == 'BANK TRANSFER' ? 'selected' : '' }}>BANK TRANSFER</option>
                                                <option value="PAY NOW" {{ old('payment_type', $quotation->payment_type) == 'PAY NOW' ? 'selected' : '' }}>PAY NOW</option>
                                            </select>
                                            @error('payment_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="currency">Currency</label>
                                            <select class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                                <option value="IDR" {{ old('currency', $quotation->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                                                <option value="USD" {{ old('currency', $quotation->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="SGD" {{ old('currency', $quotation->currency) == 'SGD' ? 'selected' : '' }}>SGD</option>
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $quotation->date) }}">
                                            @error('date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="valid_until">Valid Until</label>
                                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" id="valid_until" name="valid_until" value="{{ old('valid_until', $quotation->valid_until) }}">
                                            @error('valid_until')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address', $quotation->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="contact_person">Contact Person</label>
                                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person', $quotation->contact_person) }}">
                                            @error('contact_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="descriptions">Descriptions</label>
                                            <textarea class="form-control @error('descriptions') is-invalid @enderror" id="descriptions" name="descriptions">{{ old('descriptions', $quotation->descriptions) }}</textarea>
                                            @error('descriptions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks">{{ old('remarks', $quotation->remarks) }}</textarea>
                                            @error('remarks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="account_options">Account Options</label>
                                            <textarea class="form-control @error('account_options') is-invalid @enderror" id="account_options" name="account_options">{{ old('account_options', $quotation->account_options) }}</textarea>
                                            @error('account_options')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="made_by">Made By</label>
                                            <input type="text" class="form-control @error('made_by') is-invalid @enderror" id="made_by" name="made_by" value="{{ old('made_by', $quotation->made_by) }}">
                                            @error('made_by')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="sub_total">Sub Total</label>
                                            <input type="number" step="0.01" class="form-control @error('sub_total') is-invalid @enderror" id="sub_total" name="sub_total" value="{{ old('sub_total', $quotation->sub_total) }}">
                                            @error('sub_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="additional_discount">Additional Discount</label>
                                            <input type="number" step="0.01" class="form-control @error('additional_discount') is-invalid @enderror" id="additional_discount" name="additional_discount" value="{{ old('additional_discount', $quotation->additional_discount) }}">
                                            @error('additional_discount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="additional_cost">Additional Cost</label>
                                            <input type="number" step="0.01" class="form-control @error('additional_cost') is-invalid @enderror" id="additional_cost" name="additional_cost" value="{{ old('additional_cost', $quotation->additional_cost) }}">
                                            @error('additional_cost')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="total">Total</label>
                                            <input type="number" step="0.01" class="form-control @error('total') is-invalid @enderror" id="total" name="total" value="{{ old('total', $quotation->total) }}">
                                            @error('total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="deposit">Deposit</label>
                                            <input type="number" step="0.01" class="form-control @error('deposit') is-invalid @enderror" id="deposit" name="deposit" value="{{ old('deposit', $quotation->deposit) }}">
                                            @error('deposit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="grand_total">Grand Total</label>
                                            <input type="number" step="0.01" class="form-control @error('grand_total') is-invalid @enderror" id="grand_total" name="grand_total" value="{{ old('grand_total', $quotation->grand_total) }}">
                                            @error('grand_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Quotation</button>
                                    </form>

                            </div>
                        </div>
                    </div>
                </div>
                {{-- body-end --}}

            </div>
        </div>
    </div>
@endsection
