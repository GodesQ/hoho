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
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="referral_name" class="form-label">Referral Name</label>
                            <input type="text" class="form-control" name="referral_name" required id="referral_name">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="referral_code" class="form-label">Referral Code</label>
                            <input type="text" class="form-control" name="referral_code" required id="referral_code">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="qrcode_input" class="form-label">QrCode Base64</label>
                            <textarea name="qrcode" id="qrcode_input" cols="30" rows="5" readonly required class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="commision" class="form-label">Commision</label>
                            <div class="input-group">
                                <input
                                  type="text"
                                  class="form-control"
                                  placeholder="Commision Percentage"
                                  aria-label="Commision Percentage"
                                  name="commision"
                                />
                                <span class="input-group-text" id="basic-addon13">%</span>
                              </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="qrcode_image" class="form-label">New QrCode Image</label>
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
        let referralCodeInput = document.querySelector('#referral_code');
        let qrCodeImage = document.querySelector('#qrcode_image');
        let qrCodeInput = document.querySelector('#qrcode_input');

        let qrCode = null;

        referralCodeInput.addEventListener('input', (e) => {
            if(qrCode == null) {
                qrCode = generateQRCode(e.target.value);
            }  else {
                if(e.target.value != '') {
                    qrCode.makeCode(e.target.value);
                    const base64Image = qrCodeImage.querySelector('img').src;
                    qrCodeInput.textContent = base64Image;
                } else {
                    qrCode.clear()
                    base64Output.textContent = '';
                }
            }
        });

        const generateQRCode = (qrContent) => {
            return new QRCode(qrCodeImage, {
                        text: qrContent,
                        width: 256,
                        height: 256,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H,
                    });
        }
    </script>
@endpush
