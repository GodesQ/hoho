@extends('layouts.admin.layout')

@section('title')
    Tour Reservation Details - {{ $reservation->id }}
@endsection

@section('content')
    <style>
        .diy_ticket_pass {
            display: none !important;
        }

        .diy_ticket_pass.active {
            display: block !important;
        }

        .ticket_pass_text_container {
            display: none !important;
        }

        .ticket_pass_text_container.active {
            display: flex !important;
        }

        .nav-item {
            margin-right: 10px;
        }

        .nav-link {
            border-radius: 5px !important;
        }

        .nav-link.active {
            background-color: #6f0d00 !important;
            color: #fff !important;
            border-radius: 5px !important;
        }

        .qrcode-div {
            width: 256px;
            height: auto;
        }

        @media print {
            body {
                visibility: hidden;
            }

            #print-btn {
                visibility: hidden;
            }

            .section-to-print {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                box-shadow: none;
            }
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Tour Reservation Details</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> <a href="{{ route('admin.tour_reservations.list') }}"
                        class="text-muted fw-light">Tour Reservations /</a> Tour Reservation Details</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark btn-sm"><i class="bx bx-undo"></i>
                    Back to List</a>
                {{-- <button class="btn btn-danger btn-sm"><i class="bx bx-trash"></i> Delete</button> --}}
            </div>
        </section>

        <div class="section-body my-2">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tour-reservation-information" aria-controls="tour-reservation-information"
                        aria-selected="true">
                        Information
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#ticket-pass" aria-controls="ticket-pass" aria-selected="false">
                        Ticket Pass
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tour-reservation-information" role="tabpanel">
                    <form action="{{ route('admin.tour_reservations.update', $reservation->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="reservation-id-field" value="{{ $reservation->id }}">
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="card my-2">
                                    <div class="card-body pb-2">
                                        <h5 class="card-title fw-semibold">Customer</h5>
                                        <ul class="d-flex justify-content-start align-items-center gap-5 list-unstyled">
                                            <li>
                                                <div class="fw-semibold text-primary form-label"><i class="bx bx-user"></i>
                                                    Name
                                                </div>
                                                {{ $reservation->customer_details->firstname ?? '' }}
                                                {{ $reservation->customer_details->lastname ?? '' }}
                                            </li>
                                            <li>
                                                <div class="fw-semibold text-primary form-label"><i class="bx bx-phone"></i>
                                                    Phone
                                                </div>
                                                {{ $reservation->customer_details->contact_no ?? '' }}
                                            </li>
                                            <li>
                                                <div class="fw-semibold text-primary form-label"><i
                                                        class="bx bx-envelope"></i>
                                                    Email </div>
                                                {{ $reservation->customer_details->email ?? '' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card my-2">
                                    <div class="card-body pb-2">
                                        <h5 class="card-title fw-semibold">Reservation Details</h5>
                                        <div class="row">
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Tour </div>
                                                {{ $reservation->tour->name ?? '' }}
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Tour Type </div>
                                                {{ $reservation->tour->type ?? '' }}
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Default Price </div>
                                                {{ number_format($reservation->tour->price ?? 0, 2) }}
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Reference Code </div>
                                                {{ $reservation->reference_code ?? '' }}
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Number of Passengers </div>
                                                {{ $reservation->number_of_pass }} Pax
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Ticket Pass </div>
                                                {{ $reservation->ticket_pass ?? 'No Ticket Pass' }}
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Referral Code </div>
                                                <div class="badge bg-label-primary">
                                                    {{ $reservation->referral_code ?? 'No Referral Code Found' }}</div>
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Promo Code </div>
                                                <div class="badge bg-label-primary">
                                                    {{ $reservation->promo_code ?? 'No Promo Code Found' }}
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Created At </div>
                                                {{ Carbon::parse($reservation->created_at)->format('F d, Y') }}
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Insurance ID </div>
                                                <div class="badge bg-label-primary">
                                                    {{ $reservation->insurance_id ?? 'No Insurance ID Found' }}</div>
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <div class="fw-semibold text-primary form-label">Type of Plan </div>
                                                <div class="badge bg-label-primary">
                                                    {{ $reservation->type_of_plan ?? 'No Type of Plan Found' }}</div>
                                            </div>
                                            <hr>
                                            <div class="col-lg-6 mb-4">
                                                <div class="fw-semibold text-primary form-label">Trip Date </div>
                                                <input type="date" name="trip_date" id="trip_date"
                                                    value="{{ $reservation->start_date }}" class="form-control">
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <div class="fw-semibold text-primary form-label">Reservation Status </div>
                                                <select name="status" id="status" class="form-select select2">
                                                    <option value="pending"
                                                        {{ $reservation->status == 'pending' ? 'selected' : null }}>
                                                        Pending
                                                    </option>
                                                    <option value="approved"
                                                        {{ $reservation->status == 'approved' ? 'selected' : null }}>
                                                        Approved
                                                    </option>
                                                    <option value="cancelled"
                                                        {{ $reservation->status == 'cancelled' ? 'selected' : null }}>
                                                        Cancelled
                                                    </option>
                                                    <option value="done"
                                                        {{ $reservation->status == 'done' ? 'selected' : null }}>Done
                                                    </option>
                                                </select>
                                            </div>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card mt-2">
                                    <div class="card-body">
                                        <h5 class="card-title fw-semibold">Transaction Summary</h5>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Pax</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                <h6 id="pax_text">{{ $reservation->number_of_pass }} Pax</h6>
                                            </div>
                                        </div>
                                        <div
                                            class="row ticket_pass_text_container {{ $reservation->type == 'DIY' ? 'active' : '' }}">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Ticket Pass</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                <h6 id="ticket_pass_text">{{ $reservation->ticket_pass }}</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Sub Amount</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                <h6 id="sub_amount_text">₱
                                                    {{ number_format($reservation->sub_amount, 2) }}
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Discount</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                <h6 id="sub_amount_text">₱
                                                    {{ number_format($reservation->discount, 2) }}
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Total of Additional Fees</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                <h6 id="sub_amount_text">₱
                                                    {{ number_format($reservation->total_additional_charges, 2) }}
                                                </h6>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Total Amount</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                <h6 id="total_amount_text">₱ {{ number_format($reservation->amount, 2) }}
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <h6 class="text-primary">Transaction Status</h6>
                                            </div>
                                            <div class="col-xl-6">
                                                @if (optional($reservation->transaction)->payment_status == 'success')
                                                    <div class="badge bg-success">Success</div>
                                                @elseif(optional($reservation->transaction)->payment_status == 'cancelled')
                                                    <div class="badge bg-danger">Cancelled</div>
                                                @elseif(optional($reservation->transaction)->payment_status == 'pending')
                                                    <div class="badge bg-warning">Pending</div>
                                                @elseif(optional($reservation->transaction)->payment_status == 'failed')
                                                    <div class="badge bg-danger">Pending</div>
                                                @elseif(optional($reservation->transaction)->payment_status == 'inc')
                                                    <div class="badge bg-warning">Incompleted</div>
                                                @else
                                                    <div class="badge bg-secondary">No Status Found</div>
                                                @endif
                                                {{-- {{ $reservation->transaction->payment_status }} --}}
                                            </div>
                                        </div>
                                        <div class="my-3 justify-content-between d-flex flex-column" style="gap: 10px;">
                                            <button class="btn btn-primary w-100">Update Reservation <i
                                                    class="bx bx-save"></i></button>
                                            <div class="w-100">
                                                <a href="{{ route('admin.transactions.edit', $reservation->order_transaction_id) }}"
                                                    class="w-100 btn-outline-primary btn">View Transaction Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade " id="ticket-pass" role="tabpanel">
                    <button class="btn btn-primary mb-3" id="print-btn">Print <i class="bx bx-printer"></i></button>
                    <div class="card section-to-print">
                        <div class="card-body">
                            <div class="reservation-codes d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <script>
                $('#print-btn').click(function(e) {
                    window.print()
                })

                $("#trip_date").flatpickr({
                    enableTime: false,
                    dateFormat: "Y-m-d",
                });

                $(document).ready(function() {
                    let reservation_id = document.querySelector('#reservation-id-field').value;
                    let reservation_codes_div = document.querySelector('.reservation-codes');
                    $.ajax({
                        method: "GET",
                        url: `/admin/tour-reservations/${reservation_id}/reservation-codes`,
                        success: function(response) {
                            let reservation_codes = response.reservation_codes;
                            reservation_codes.forEach(reservation_code => {
                                let qrCodeDiv = document.createElement('div');
                                qrCodeDiv.classList.add('qrcode-div', 'mx-3');
                                let codeResult = reservation_code.code + "&" + reservation_id;
                                generateQRCode(qrCodeDiv, codeResult);
                                let text_code = document.createElement('h6');
                                text_code.innerHTML = reservation_code.code;
                                text_code.classList.add('my-3', "text-center", "text-primary");
                                qrCodeDiv.appendChild(text_code);
                                reservation_codes_div.appendChild(qrCodeDiv);
                            });
                        }
                    })
                })

                const generateQRCode = (qrCodeDiv, qrContent) => {
                    return new QRCode(qrCodeDiv, {
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
