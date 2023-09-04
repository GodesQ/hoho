@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Referral')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Referral</h4>
        <a href="{{ route('admin.referrals.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.referrals.update', $referral->id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="referral_name" class="form-label">Referral Name</label>
                            <input type="text" class="form-control" name="referral_name" required id="referral_name" value="{{ $referral->referral_name }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="referral_code" class="form-label">Referral Code</label>
                            <input type="text" class="form-control" name="referral_code" required id="referral_code" value="{{ $referral->referral_code }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="qrcode_input" class="form-label">QrCode Base64</label>
                            <textarea name="qrcode" id="qrcode_input" cols="30" rows="5" readonly required class="form-control">{{ $referral->qrcode }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="qrcode_image" class="form-label">QrCode Image</label>
                            <div id="qrcode_image"></div>
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

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const referralCodeInput = document.querySelector('#referral_code');
            const qrCodeImage = document.querySelector('#qrcode_image');
            const qrCodeInput = document.querySelector('#qrcode_input');

            let qrCode = generateQRCode(referralCodeInput.value);

            referralCodeInput.addEventListener('input', (e) => {
                const inputValue = e.target.value;
                if (inputValue !== '') {
                    qrCode.makeCode(inputValue);
                    qrCodeInput.textContent = qrCodeImage.querySelector('img').src;
                } else {
                    qrCode.clear();
                    qrCodeInput.textContent = '';
                }
            });

            function generateQRCode(qrContent) {
                return new QRCode(qrCodeImage, {
                    text: qrContent,
                    width: 256,
                    height: 256,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H,
                });
            }
        });
    </script>
@endpush
