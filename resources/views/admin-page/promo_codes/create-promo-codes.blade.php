@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off - Create Promo Codes')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Promo Codes</h4>
        <a href="{{ route('admin.promo_codes.list') }}" class="btn btn-dark"> <i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.promo_codes.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Promo Code</label>
                            <input type="text" class="form-control" name="code" id="code">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Promo Description</label>
                            <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Promo Type</label>
                            <select name="typ" id="typ" class="form-select">
                                <option value="discount">Discount</option>
                                <option value="free">Free</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 discount-con">
                        <div class="form-group mb-3">
                            <label for="discount_type" class="form-label">Discount Type</label>
                            <select name="discount_type" id="discount_type" class="form-select">
                                <option value="percentage">Percentage</option>
                                <option value="amount">Amount</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 discount-con">
                        <div class="form-group mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount</label>
                            <input type="text" class="form-control" id="discount_amount" name="discount_amount">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="isNeedRequirment" name="is_need_requirement" checked />
                            <label class="form-check-label" for="isNeedRequirment">Need Requirment?</label>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="isNeedApproval" name="is_need_requirement" checked />
                            <label class="form-check-label" for="isNeedApproval">Need Approval?</label>
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary">Save Promo Code</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $('#discount_type').on('change', function(e) {
            
        })
    </script>
@endpush