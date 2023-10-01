@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Referral')

@section('content')
<style>
    button.dt-button {
            background: #233446 !important;
            border-radius: 5px !important;
            color: white !important;
        }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Referral</h4>
        <a href="{{ route('admin.referrals.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 table-responsive-xl">
                    <table class="table referral-table">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Reserved User</th>
                                <th>Amount</th>
                                <th>Total Commision</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-lg-6">
                    <div id="referalChart"></div>
                </div>
            </div>
        </div>
    </div>
    <br>

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
                            <label for="commision" class="form-label">Commision</label>
                            <div class="input-group">
                                <input
                                  type="text"
                                  class="form-control"
                                  placeholder="Commision Percentage"
                                  aria-label="Commision Percentage"
                                  name="commision"
                                  value="{{ $referral->commision }}"
                                />
                                <span class="input-group-text" id="basic-addon13">%</span>
                              </div>
                        </div>
                        <div class="mb-3">
                            <label for="merchant_id" class="form-label">Merchant</label>
                            <select name="merchant_id" id="merchant_id" class="select2" {{ auth('admin')->user()->is_merchant ? 'disabled' : null }}>
                                <option value="">--- SELECT MERCHANT ---</option>
                                @foreach ($merchants as $merchant)
                                    <option {{ $merchant->id == $referral->merchant_id ? 'selected' : null }} value="{{ $merchant->id }}">{{ $merchant->name }} ({{ $merchant->type }})</option>
                                @endforeach
                            </select>
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

        function loadTable() {
            let table = $('.referral-table').DataTable({
                processing: true,
                pageLength: 5,
                responsive: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'pdf', 'print',
                ],
                ajax: {
                    url: "{{ route('admin.referrals.tour_reservations.list', ['code' => $referral->referral_code]) }}"
                },
                columns: [
                    {
                        data: 'tour_name',
                        name: 'tour_name',
                    },
                    {
                        data: 'reserved_user_name',
                        name: 'reserved_user_name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'total_commision',
                        name: 'total_commision'
                    },
                ]
            });
        }

        loadTable();

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
