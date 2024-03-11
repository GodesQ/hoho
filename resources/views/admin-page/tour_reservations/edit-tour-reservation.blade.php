@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Tour Reservation')

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
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tour-reservation-details"
                    aria-controls="navs-tour-reservation-details" aria-selected="true">
                    Tour Reservation Details
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tour-reservation-codes"
                    aria-controls="navs-tour-reservation-codes" aria-selected="false">
                    Tour Reservation Codes
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-tour-reservation-details" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold py-3 mb-4">Edit Tour Reservation</h4>
                    <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                        List</a>
                </div>
        
                <form action="{{ route('admin.tour_reservations.update', $reservation->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4>Enter your details</h4>
                                        @if ($reservation->status == 'approved')
                                            <div class="badge bg-success">{{ $reservation->status }}</div>
                                        @elseif ($reservation->status == 'done')
                                            <div class="badge bg-success">{{ $reservation->status }}</div>
                                        @elseif ($reservation->status == 'cancelled')
                                            <div class="badge bg-danger">{{ $reservation->status }}</div>
                                        @elseif ($reservation->status == 'failed')
                                            <div class="badge bg-danger">{{ $reservation->status }}</div>
                                        @elseif ($reservation->status == 'pending')
                                            <div class="badge bg-primary">{{ $reservation->status }}</div>
                                        @endif
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="" class="form-label">Reservation Status</label>
                                                <select name="status" id="status" class="form-select">
                                                    <option value="pending"
                                                        {{ $reservation->status == 'pending' ? 'selected' : null }}>Pending</option>
                                                    <option value="approved"
                                                        {{ $reservation->status == 'approved' ? 'selected' : null }}>Approved
                                                    </option>
                                                    <option value="cancelled"
                                                        {{ $reservation->status == 'cancelled' ? 'selected' : null }}>Cancelled
                                                    </option>
                                                    <option value="done"
                                                        {{ $reservation->status == 'done' ? 'selected' : null }}>Done</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="type" id="guided_tour"
                                                        value="Guided" required
                                                        {{ $reservation->type == 'Guided' ? 'checked' : null }} disabled />
                                                    <label class="form-check-label" for="guided_tour">
                                                        Guided Tour
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="diy_tour"
                                                        value="DIY" required
                                                        {{ $reservation->type == 'DIY' ? 'checked' : null }} disabled />
                                                    <label class="form-check-label" for="diy_tour">
                                                        DIY Tour
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="tour" class="form-label">Tour</label>
                                                <input type="text" class="form-control" name="tour"
                                                    value="{{ optional($reservation->tour)->name ?? 'Deleted Tour' }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="reserved_user" class="form-label">Reserved User</label>
                                                <input type="text" class="form-control" name="reserved_user"
                                                    id="reserved_user"
                                                    value="{{ optional($reservation->user)->email ? optional($reservation->user)->email : 'Deleted User' }}"
                                                    disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="" class="form-label">Trip Date</label>
                                                <input type="text" placeholder="Select a Trip Date" class="form-control"
                                                name="trip_date" id="trip_date" required value="{{ date_format(new \DateTime($reservation->start_date), 'Y-m-d') }}">
                                            </div>
                                            {{-- <div class="row d-none">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="" class="form-label">Start Date</label>
                                                        <input type="date" class="form-control"
                                                            value="{{ $reservation->start_date }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="" class="form-label">End Date</label>
                                                        <input type="date" class="form-control"
                                                            value="{{ $reservation->end_date }}" disabled>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="number_of_pass" class="form-label">Number Of Pax</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reservation->number_of_pass }} Pax" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <?php
                                                $emails = [];
                                                if (is_array($reservation->passengers)) {
                                                    foreach ($reservation->passengers as $key => $passenger) {
                                                        array_push($emails, $passenger['email']);
                                                    }
                                                }
                                                ?>
                                                <label for="number_of_pass" class="form-label">Passengers</label>
                                                <input type="text" class="form-control" value="{{ implode(', ', $emails) }}"
                                                    disabled>
                                            </div>
                                        </div>
                                        <div
                                            class="col-lg-12 diy_ticket_pass {{ $reservation->type == 'DIY' ? 'active' : null }}">
                                            <div class="mb-3">
                                                <div class="form-label">DIY Ticket Pass</div>
                                                @foreach ($ticket_passes as $ticket_pass)
                                                    <div class="form-check form-check-inline mt-3">
                                                        <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                                            name="ticket_pass" id="{{ $ticket_pass->name }}"
                                                            value="{{ $ticket_pass->name }}" disabled
                                                            data-amount="{{ $ticket_pass->price }}"
                                                            {{ $ticket_pass->name == $reservation->ticket_pass ? 'checked' : null }} />
                                                        <label class="form-check-label" for="{{ $ticket_pass->name }}"
                                                            style="cursor: pointer;">
                                                            <img src="{{ URL::asset('assets/img/ticket_passes/' . $ticket_pass->ticket_image) }}"
                                                                alt="1 Day Ticket Pass" width="120px">
                                                            <h6 class="text-center my-2">₱
                                                                {{ number_format($ticket_pass->price, 2) }}</h6>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Book Reservation Summary</h4>
                                    <hr>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <h6 class="text-primary">Tour</h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <h6 id="tour_text">
                                                {{ strlen($reservation->tour) > 30 ? substr(optional($reservation->tour)->name, 0, 30) . '...' : optional($reservation->tour)->name }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <h6 class="text-primary">Pax</h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <h6 id="pax_text">{{ $reservation->number_of_pass }} Pax</h6>
                                        </div>
                                    </div>
                                    <div class="row ticket_pass_text_container {{ $reservation->type == 'DIY' ? 'active' : '' }}">
                                        <div class="col-xl-6">
                                            <h6 class="text-primary">Ticket Pass</h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <h6 id="ticket_pass_text">{{ $reservation->ticket_pass }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <h6 class="text-primary">Promo Code</h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <h6 id="#">{{ $reservation->promo_code ?? 'No Promo Code Found' }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <h6 class="text-primary">Referral Code</h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <h6 id="#">{{ $reservation->referral_code ?? 'No Referral Code Found' }}</h6>
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
                                            <h6 id="total_amount_text">₱ {{ number_format($reservation->amount, 2) }}</h6>
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
            <div class="tab-pane fade" id="navs-tour-reservation-codes" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold py-3 mb-4">Tour Reservation Codes</h4>
                    <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                        List</a>
                </div>
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <div class="row qr-codes"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>

        $(document).ready(function () {
            $.ajax({
                url: "{{ route('admin.tour_reservations.reservation_codes', $reservation->id) }}",
                method: 'GET',
                success: function(response) {
                    if(response.reservation_codes.length > 0) {
                        response.reservation_codes.forEach(displayQRCodes);
                    } else {
                        $('.qr-codes').addClass('text-center');
                        $('.qr-codes').html('<h5>No QR Codes Found</h5>');
                    }
                }
            })

            if (["super_admin", "admin"].includes("{{ auth('admin')->user()->role }}")) {
                var dateToday = new Date();
            } else {
                var dateToday = new Date();
                dateToday.setDate(dateToday.getDate() + 5); // Add 5 days
            }

            $("#trip_date").datepicker({
                minDate: dateToday,
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                beforeShowDay: function(date) {
                    // Check if the day of the week is Monday (0 for Sunday, 1 for Monday, etc.)
                    if (date.getDay() === 1) {
                    // Disable Monday dates
                    return [false, "ui-state-disabled"];
                    } else {
                    // Enable other dates
                    return [true, ""];
                    }
                }
            });
        })

        // Function to generate QR code for each reservation code
        function displayQRCodes(reservationCode) {
            //create parent div
            let div = document.createElement('div');
            div.classList.add('col-xl-3');
            div.classList.add('my-2');
            div.classList.add('d-flex');
            div.classList.add('justify-content-center');
            div.classList.add('align-items-center');
            div.classList.add('flex-column');

            //create div for img
            let imgDiv = document.createElement('div');
            imgDiv.className = 'd-flex justify-content-center align-items-center flex-column';

            //create title for each div qr code
            let h5 = document.createElement('h5');
            h5.classList.add('mt-2');
            h5.textContent = `Code: ${reservationCode.code}`;

            let qrCode = generateQRCode(reservationCode.code, imgDiv);
            qrCode.makeCode(reservationCode.code);
            
            div.append(imgDiv);
            div.append(h5);
            $('.qr-codes').append(div);
        }

        const generateQRCode = (qrContent, imgContainer) => {
            const qrCode = new QRCode(imgContainer , {
                        text: qrContent,
                        width: 220,
                        height: 200,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H,
                    });

            imgContainer.querySelector('img').style.width = '90%';
            return qrCode;
        }
    </script>
@endpush