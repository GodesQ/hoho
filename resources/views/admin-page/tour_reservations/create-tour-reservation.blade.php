@extends('layouts.admin.layout')

@section('title', 'Book Tour - Philippines Hop On Hop Off')

@section('content')
    <style>
        .tour-type-option {
            border: 1px solid lightgray;
            width: 25%;
            padding: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 10px;
            cursor: pointer;
        }

        .tour-type-option img,
        h5 {
            pointer-events: none;
        }

        .tour-type-option.active {
            border-color: #6f0d00;
            border-width: 3px;
        }

        .transit-tour-details {
            display: none;
        }

        .transit-tour-details.active {
            display: block;
        }

        .diy-ticket-pass-details {
            display: none;
        }

        .diy-ticket-pass-details.active {
            display: block;
        }

        #edit-promocode-btn {
            display: none;
        }

        #edit-promocode-btn.active {
            display: block;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y app-content">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Book Tour</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> <a href="{{ route('admin.tour_reservations.list') }}"
                        class="text-muted fw-light">Tour Reservations /</a> Book Tour</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark btn-sm"><i class="bx bx-undo"></i>
                    Back to List</a>
            </div>
        </section>

        <div class="section section-body mt-3">
            <section id="number-tabs">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-h font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="feather icon-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="feather icon-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                                        <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form action="{{ route('admin.tour_reservations.store') }}" method="POST"
                                        class="number-tab-steps wizard-circle" id="book-tour-form">
                                        @csrf
                                        <!-- Step 1 -->
                                        <h6>Tour Type</h6>
                                        <fieldset>
                                            <input type="hidden" name="type" id="tour-type" value="DIY">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <div class="tour-type-option active" data-type="DIY" id="diy-type">
                                                    <img src="{{ URL::asset('assets/img/icons/diy-tour-icon.png') }}">
                                                    <h5>DIY Tour</h5>
                                                </div>
                                                <div class="tour-type-option" data-type="Guided" id="guided-type">
                                                    <img src="{{ URL::asset('assets/img/icons/guided-tour-icon.png') }}">
                                                    <h5>Guided Tour</h5>
                                                </div>
                                                <div class="tour-type-option" data-type="Transit" id="transit-type">
                                                    <img src="{{ URL::asset('assets/img/icons/transit-tour-icon.png') }}">
                                                    <h5>Transit Tour</h5>
                                                </div>
                                                <div class="tour-type-option" data-type="Seasonal" id="seasonal-type">
                                                    <img src="{{ URL::asset('assets/img/icons/seasonal-tour-icon.png') }}">
                                                    <h5>Seasonal Tour</h5>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mt-4 transit-tour-details">
                                                <h5 class="text-bold">Additional Info for Transit Tour</h5>
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="form-group mb-2">
                                                            <label for="" class="form-label">Arrival Date and
                                                                Time</label>
                                                            <input type="date" class="form-control"
                                                                name="arrival_datetime" id="arrival-datetime-field">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="form-group mb-2">
                                                            <label for="" class="form-label">Departure Date and
                                                                Time</label>
                                                            <input type="date" class="form-control"
                                                                name="departure_datetime" id="departure-datetime-field">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="form-group mb-2">
                                                            <label for="" class="form-label">Flight From</label>
                                                            <input type="text" class="form-control" name="flight_from"
                                                                id="flight-from-field">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="form-group mb-2">
                                                            <label for="" class="form-label">Flight To</label>
                                                            <input type="text" class="form-control" name="flight_to"
                                                                id="flight-to-field">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="form-group mb-2">
                                                            <label for="" class="form-label">Passport Number</label>
                                                            <input type="text" class="form-control"
                                                                name="passport_number" id="passport-number-field">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="form-group mb-2">
                                                            <label for="" class="form-label">Special
                                                                Instruction (Optional)</label>
                                                            <textarea type="text" class="form-control" name="passport_number" id="passport-number-field"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 diy-ticket-pass-details">
                                                <div class="mb-3">
                                                    <h5 class="text-bold">DIY Ticket Pass</h5>
                                                    @foreach ($ticket_passes as $ticket_pass)
                                                        <div class="form-check form-check-inline mt-3">
                                                            <input class="form-check-input diy_ticket_pass_radio"
                                                                type="radio" name="ticket_pass"
                                                                id="{{ $ticket_pass->name }}"
                                                                value="{{ $ticket_pass->name }}"
                                                                data-amount="{{ $ticket_pass->price }}" />
                                                            <label class="form-check-label"
                                                                for="{{ $ticket_pass->name }}" style="cursor: pointer;">
                                                                <img src="{{ URL::asset('assets/img/ticket_passes/' . $ticket_pass->ticket_image) }}"
                                                                    alt="1 Day Ticket Pass" width="120px">
                                                                <h6 class="text-center my-2">₱
                                                                    {{ number_format($ticket_pass->price, 2) }}
                                                                    ({{ $ticket_pass->name }})
                                                                </h6>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </fieldset>

                                        <!-- Step 2 -->
                                        <h6>Tours</h6>
                                        <fieldset>
                                            <div
                                                class="tours d-flex justify-content-center align-items-start flex-wrap gap-4">
                                            </div>
                                        </fieldset>

                                        <!-- Step 3 -->
                                        <h6>Reservation Details</h6>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="reserved_user" class="form-label">Reserved
                                                            User</label>
                                                        <select name="reserved_user_id" id="user-id-field"
                                                            class="reserved_users form-select" style="width: 100%;"
                                                            required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="trip-date-field" class="form-label">Trip Date</label>
                                                        <input type="text" readonly placeholder="Select a Trip Date"
                                                            class="form-control" name="trip_date" id="trip-date-field"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="" class="form-label">Number of Pax</label>
                                                        <select name="number_of_pass" id="number-of-pax-field"
                                                            class="form-select" required>
                                                            <option value="1">1 pax</option>
                                                            <option value="2">2 pax</option>
                                                            <option value="3">3 pax</option>
                                                            <option value="4">4 pax</option>
                                                            <option value="5">5 pax</option>
                                                            <option value="6">6 pax</option>
                                                            <option value="7">7 pax</option>
                                                            <option value="8">8 pax</option>
                                                            <option value="9">9 pax</option>
                                                            <option value="10">10 pax</option>
                                                            <option value="11">11 pax</option>
                                                            <option value="12">12 pax</option>
                                                            <option value="13">13 pax</option>
                                                            <option value="14">14 pax</option>
                                                            <option value="15">15 pax</option>
                                                            <option value="16">16 pax</option>
                                                            <option value="17">17 pax</option>
                                                            <option value="18">18 pax</option>
                                                            <option value="19">19 pax</option>
                                                            <option value="20">20 pax</option>
                                                            <option value="21">21 pax</option>
                                                            <option value="22">22 pax</option>
                                                            <option value="23">23 pax</option>
                                                            <option value="24">24 pax</option>
                                                            <option value="25">25 pax</option>
                                                            <option value="26">26 pax</option>
                                                            <option value="27">27 pax</option>
                                                            <option value="28">28 pax</option>
                                                            <option value="29">29 pax</option>
                                                            <option value="30">30 pax</option>
                                                            <option value="31">31 pax</option>
                                                            <option value="32">32 pax</option>
                                                            <option value="33">33 pax</option>
                                                            <option value="34">34 pax</option>
                                                            <option value="35">35 pax</option>
                                                            <option value="36">36 pax</option>
                                                            <option value="37">37 pax</option>
                                                            <option value="38">38 pax</option>
                                                            <option value="39">39 pax</option>
                                                            <option value="40">40 pax</option>
                                                            <option value="41">41 pax</option>
                                                            <option value="42">42 pax</option>
                                                            <option value="43">43 pax</option>
                                                            <option value="44">44 pax</option>
                                                            <option value="45">45 pax</option>
                                                            <option value="46">46 pax</option>
                                                            <option value="47">47 pax</option>
                                                            <option value="48">48 pax</option>
                                                            <option value="49">49 pax</option>
                                                            <option value="50">50 pax</option>
                                                        </select>
                                                        {{-- <select name="number_of_pass" id="number-of-pax-field"
                                                            class="form-select" required>
                                                        </select> --}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Registered Passengers (Optional)</label>
                                                        <select name="passenger_ids[]" id="passengers"
                                                            class="registered_passengers form-select" style="width: 100%;"
                                                            multiple max="4"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <!-- Step 4 -->
                                        <h6>Summary</h6>

                                        <fieldset>
                                            <div class="row">
                                                <div class="col-xl-7">
                                                    <h4 class="text-bold mb-3">Reservation Details</h4>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">First
                                                                        Name</label>
                                                                    <h6 id="firstname-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">Last
                                                                        Name</label>
                                                                    <h6 id="lastname-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">Email</label>
                                                                    <h6 id="email-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">Contact
                                                                        Number</label>
                                                                    <h6 id="contact-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">Number
                                                                        of Pax</label>
                                                                    <h6 id="pax-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">Reservation
                                                                        Date</label>
                                                                    <h6 id="trip-date-text"></h6>
                                                                </div>
                                                                <div class="col-xl-12">
                                                                    <label for=""
                                                                        class="form-label text-primary">Tour</label>
                                                                    <h6 id="tour-name-text"></h6>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="payment_method"
                                                                        class="form-label text-primary">Payment
                                                                        Method</label>
                                                                    <div class="form-check">
                                                                        <input name="payment_method"
                                                                            class="form-check-input" type="radio"
                                                                            value="cash" id="cash-payment-method" />
                                                                        <label class="form-check-label"
                                                                            for="cash-payment-method"> Cash
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input name="payment_method"
                                                                            class="form-check-input" type="radio"
                                                                            value="aqwire" id="aqwire-payment-method"
                                                                            checked />
                                                                        <label class="form-check-label"
                                                                            for="aqwire-payment-method"> Aqwire </label>
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="col-xl-6">
                                                                    
                                                                    <div class="form-group mb-3">
                                                                        <input type="checkbox" name="payment_method"
                                                                            id="payment-method-field" value="cash">
                                                                        <label class="font-bold form-label"
                                                                            for="payment-method-field">Cash</label>
                                                                    </div>
                                                                </div> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card my-3">
                                                        <div class="card-header">
                                                            <h5 class="card-title fw-bold">Promo & Referral Codes</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <h6 style="font-weight: 600;">Promo Code</h6>
                                                                <a href="javascript:void(0);" id="edit-promocode-btn"><i
                                                                        class="bx bx-edit"></i> Change promo code?</a>
                                                            </div>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    placeholder="Apply Promo Code" name="promo_code"
                                                                    id="promocode-field" />
                                                                <button class="btn btn-primary" type="button"
                                                                    id="apply-promocode-btn">Apply</button>
                                                            </div>
                                                            <h6 class="mt-3" style="font-weight: 600;">Referral Code
                                                            </h6>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    placeholder="Apply Referral Code" name="referral_code"
                                                                    id="referral_code" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-5">
                                                    <h4 class="text-bold mb-3">Checkout Summary</h4>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">
                                                                        Convenience
                                                                        Fee</div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6>₱ 99.00 / Pax</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">Sub
                                                                        Amount
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="sub-amount-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">
                                                                        Total
                                                                        Convenience Fee </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="total-convenience-fee-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">
                                                                        Total Of
                                                                        Discount </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="total-discount-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">
                                                                        Total
                                                                        Amount</div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="total-amount-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <input type="hidden" name="amount" id="sub-amount-field">
                                        <input type="hidden" name="discounted_amount" id="discounted-amount-field">
                                        <input type="hidden" id="discount-field" value="0">
                                        <input type="hidden" id="total-amount-field">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ URL::asset('assets/js/custom/tour-reservation.js') }}"></script>
    <script>
        $(document).ready(() => {
            $('.reserved_users, .registered_passengers').select2({
                placeholder: 'Select users',
                minimumInputLength: 3,
                ajax: {
                    url: `{{ route('admin.users.lookup') }}`,
                    dataType: 'json',
                    delay: 350,
                    processResults: data => ({
                        results: data
                    })
                }
            });

        })
    </script>
@endpush
