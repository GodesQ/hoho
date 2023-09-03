@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Referral')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Referral</h4>
        <a href="{{ route('admin.referrals.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.referrals.store') }}" method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="referral_name" class="form-label">Referral Name</label>
                            <input type="text" class="form-control" name="referral_name" id="referral_name">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="referral_code" class="form-label">Referral Code</label>
                            <input type="text" class="form-control" name="referral_code" id="referral_code">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="qrcode_input" class="form-label">QrCode Base64</label>
                            <textarea name="qrcode" id="qrcode_input" cols="30" rows="5" readonly class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="qrcode_image" class="form-label">QrCode Image</label>
                            <div class="qrcode_image"></div>
                        </div>
                    </div>
                </div>
                <div class="my-3 justify-content-end d-flex">
                    <button class="btn btn-primary">Save Referral</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
