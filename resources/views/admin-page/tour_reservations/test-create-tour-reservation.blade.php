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
                                    <form action="{{ route('admin.tour_reservations.store') }}" method="POST" class="number-tab-steps wizard-circle" id="book-tour-form">
                                        @csrf
                                        <!-- Step 1 -->
                                        <h6>Tour Type</h6>
                                        <fieldset>
                                            <input type="hidden" name="type" id="tour-type" value="diy">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <div class="tour-type-option active" data-type="diy" id="diy-type">
                                                    <img src="{{ URL::asset('assets/img/icons/diy-tour-icon.png') }}">
                                                    <h5>DIY Tour</h5>
                                                </div>
                                                <div class="tour-type-option" data-type="guided" id="guided-type">
                                                    <img src="{{ URL::asset('assets/img/icons/guided-tour-icon.png') }}">
                                                    <h5>Guided Tour</h5>
                                                </div>
                                                <div class="tour-type-option" data-type="transit" id="transit-type">
                                                    <img src="{{ URL::asset('assets/img/icons/transit-tour-icon.png') }}">
                                                    <h5>Transit Tour</h5>
                                                </div>
                                                <div class="tour-type-option" data-type="seasonal" id="seasonal-type">
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
                                                        </select>
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
                                            {{-- <div class="loading-container d-flex justify-content-center align-items-center flex-column">
                                                <img src="{{ URL::asset('assets/img/icons/loading.gif') }}" alt="">
                                                <h5>Getting all information</h5>
                                            </div> --}}
                                            <div class="row">
                                                <div class="col-xl-7">
                                                    <h4 class="text-bold mb-3">Reservation Details</h4>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <label for="" class="form-label text-primary">First
                                                                        Name</label>
                                                                    <h6 id="firstname-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for="" class="form-label text-primary">Last
                                                                        Name</label>
                                                                    <h6 id="lastname-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for=""
                                                                        class="form-label text-primary">Email</label>
                                                                    <h6 id="email-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for="" class="form-label text-primary">Contact
                                                                        Number</label>
                                                                    <h6 id="contact-text"></h6>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <label for="" class="form-label text-primary">Number
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
                                                                <a href="javascript:void(0);" id="edit-promocode-btn"><i class="bx bx-edit"></i> Change promo code?</a>
                                                            </div>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" placeholder="Apply Promo Code"
                                                                    name="promo_code" id="promocode-field" />
                                                                <button class="btn btn-primary" type="button" id="apply-promocode-btn">Apply</button>
                                                            </div>
                                                            <h6 class="mt-3" style="font-weight: 600;">Referral Code</h6>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" placeholder="Apply Referral Code"
                                                                    name="referral_code" id="referral_code" />
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
                                                                    <div class="text-primary text-uppercase form-label">Convenience
                                                                        Fee</div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6>₱ 99.00 / Pax</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">Sub Amount
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="sub-amount-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">Total
                                                                        Convenience Fee </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="total-convenience-fee-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">Total Of
                                                                        Discount </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <h6 id="total-discount-text">₱ 0.00</h6>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="text-primary text-uppercase form-label">Total
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
    <script>
        $(document).ready(() => {
            let type = $('#tour-type').val();
            toggleTourDetails(type);

            $("#arrival-datetime-field, #departure-datetime-field").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            })

            $("#trip-date-field").flatpickr({
                enableTime: false,
                dateFormat: "Y-m-d",
            });

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

            populateNumberOfPax();
        })

        $('.reserved_users').change(function(e) {
            console.log(e.target.value);
        })

        $(".number-tab-steps").steps({
            headerTag: "h6",
            bodyTag: "fieldset",
            transitionEffect: "fade",
            titleTemplate: '<span class="step">#index#</span> #title#',
            labels: {
                finish: 'Submit'
            },
            onStepChanging: function(event, currentIndex, newIndex) {
                if (currentIndex == 0 && $('#tour-type').val() == 'transit') {
                    if (!validateTransitDetails()) {
                        toastr.error(
                            'Please fill out the transit details fields before proceeding to the next step.'
                        )
                        return false;
                    }
                }

                if (currentIndex == 0 && $('#tour-type').val() == 'diy' && !$(
                        'input[name="ticket_pass"]:checked').val() && newIndex == 1) {
                    toastr.error('Please select ticket pass before proceeding to the next step.')
                    return false;
                }

                if (currentIndex == 1 && !$('input[name="tour_id"]:checked').val() && newIndex == 2) {
                    toastr.error('Please select tour before proceeding to the next step.')
                    return false;
                }

                if(currentIndex == 2) {
                    if(!validateReservationDetails()) {
                        toastr.error('Please complete the reservation details before proceeding to the next step.', 'Fail');
                        return false;
                    }
                }

                if (newIndex == 1) {
                    getTours();
                }

                return true;
            },
            onStepChanged: function(event, currentIndex, priorIndex) {
                if (currentIndex == 3) {
                    computeAndDisplayCheckoutAmount();
                    fetchAndDisplayUserInfo();
                    displayReservationDetails();
                }
            },
            onFinished: function(event, currentIndex) {
                $('#book-tour-form').submit();
            }
        });

        $('#apply-promocode-btn').click(function (e) {
            let sub_amount = $('#sub-amount-field').val();
            let promocode = $('#promocode-field').val();

            if (sub_amount == '') return toastr.error('Invalid Amount Value', 'Fail');
            if (promocode == '') return toastr.error('Invalid Promocode Value', 'Fail');

            $.ajax({
                url: `/api/promocodes/verify/${promocode}`,
                method: 'GET',
                success: function(response) {
                    if(!response.is_promocode_exist) return toastr.error('Invalid PromoCode', 'Fail');

                    toastr.success('PromoCode Exist', 'Success');

                    // set the promocode field to readonly
                    $('#promocode-field').prop('readonly', true);
                    $('#edit-promocode-btn').addClass('active');

                    computeDiscount(response.promocode);
                    computeAndDisplayCheckoutAmount();
                }
            });
        })

        $('#edit-promocode-btn').click(function (e) {
            let sub_amount = $('#sub-amount-field').val();

            $('#edit-promocode-btn').removeClass('active');
            $('#promocode-field').prop('readonly', false);
            $('#promocode-field').val('');
            $('#total-discount-text').text(`₱ ${addCommasToNumberWithDecimal(0)}`);
            $('#discount-field').val(0);
            $('#discounted-amount-field').val(parseInt(sub_amount) - 0);

            computeAndDisplayCheckoutAmount();
        })

        $('.tour-type-option').click((e) => {
            let type = $(e.target).data('type');

            $('.tour-type-option').removeClass('active');
            $(e.target).addClass('active');

            toggleTourDetails(type);

            $('#tour-type').val(type);
        });

        $('#number-of-pax-field').change((e) => {
            let pax = e.target.value;
            computeConvenienceFee(pax);
        });

        function validateTransitDetails() {
            return ['#arrival-datetime-field',
                '#departure-datetime-field',
                '#passport-number-field',
                '#flight-from-field',
                '#flight-to-field'
            ].every(field => $(field).val());
        }

        function validateReservationDetails() {
            return ['#user-id-field', '#number-of-pax-field', '#trip-date-field',].every(field => $(field).val());
        }

        function populateNumberOfPax() {
            let select = document.getElementById("number-of-pax-field");
            for (var i = 1; i <= 100; i++) {
                var option = document.createElement("option");
                option.value = i;
                option.text = i + " Pax";
                select.appendChild(option);
            }
        }

        async function getTours() {
            const toursContainer = document.querySelector('.tours');
            const type = $('#tour-type').val();
            const arrival_datetime = $('#arrival-datetime-field').val();
            const departure_datetime = $('#departure-datetime-field').val();
            const url =
                `/admin/tours/${type}?arrival_datetime=${arrival_datetime}&departure_datetime=${departure_datetime}`;

            try {
                const response = await $.get(url);
                const tours = response.tours || [];
                const output = tours.length ? tours.map(tour => `
                    <div class="form-check mt-3" style="max-width: 250px; width: 250px;">
                        <input class="form-check-input tour-radio"
                            type="radio" name="tour_id"
                            id="${tour.id}"
                            value="${tour.id}"
                            data-name="${tour.name}"
                            data-prices="${JSON.stringify([tour.price, tour.bracket_price_one, tour.bracket_price_two, tour.bracket_price_three])}" />
                        <label for="${tour.id}" style="cursor: pointer;">
                            <img src="../../../assets/img/tours/${tour.id}/${tour.featured_image}" style="width: 100%;" class="rounded" />
                            <h6 class="my-2">${tour.name}</h6>
                        </label>
                    </div>
                `).join('') : '';
                toursContainer.innerHTML = output;
            } catch (error) {
                const responseJSON = error.responseJSON;
                toastr.error(responseJSON?.message || 'Failed', 'Failed');
                toursContainer.innerHTML = '';
            }
        }

        function computeConvenienceFee(pax) {
            let sum = 99 * pax;
            $('#total-convenience-fee-text').html(`₱ ${addCommasToNumberWithDecimal(sum)}`);

            return sum;
        }

        function computeSubAmount(amount, pax) {
            let sum = amount * pax;
            $('#sub-amount-text').html(`₱ ${addCommasToNumberWithDecimal(sum)}`);
            $('#sub-amount-field').val(sum);

            return sum;
        }

        function computeDiscount(promocode) {
            if (promocode.discount_type == 'percentage') {
                let sub_amount = $('#sub-amount-field').val();
                let percentage = promocode.discount_amount / 100

                // Compute the total discount when the type is percentage
                let discount = sub_amount * percentage;
                
                
                $('#total-discount-text').text(`₱ ${addCommasToNumberWithDecimal(discount)}`);
                $('#discount-field').val(discount);
                $('#discounted-amount-field').val(parseInt(sub_amount) - discount);
            }
        }

        function computeAndDisplayCheckoutAmount() {
            let tour_type = $('#tour-type').val();
            let number_of_pax = $('#number-of-pax-field').val();
            let discount = $('#discount-field').val();
            let total_of_convenience_fee = computeConvenienceFee(number_of_pax);
            let total_amount;
            let sub_amount;
            

            if (tour_type === 'diy') {

                let ticket_pass_price = $('input[name="ticket_pass"]:checked').data('amount');
                sub_amount = computeSubAmount(ticket_pass_price, number_of_pax);
                total_amount = (sub_amount - discount) + total_of_convenience_fee;

            } else if (tour_type === 'guided' || tour_type === 'seasonal') {

                let tour_selected_bracket_price = getTourSelectedBracketPrice(number_of_pax);
                sub_amount = computeSubAmount(tour_selected_bracket_price, number_of_pax);
                total_amount = (sub_amount - discount) + total_of_convenience_fee;

            }

            // When calculating the checkout amount for the initial amount, add the sub amount to the discounted amount field.
            $('#discounted-amount-field').val(sub_amount);
            
            $('#total-amount-field').val(total_amount);
            $('#total-amount-text').html(`₱ ${addCommasToNumberWithDecimal(total_amount)}`);
        }

        function getTourSelectedBracketPrice(number_of_pax) {
            let tour = $('input[name="tour_id"]:checked');
            let tour_prices = tour.data('prices');

            if (Array.isArray(tour_prices) && tour_prices.length > 0) {

                let priceIndex = 0;

                if (number_of_pax <= 9 && number_of_pax >= 4) {
                    priceIndex = 1;
                } else if (number_of_pax <= 24 && number_of_pax >= 10) {
                    priceIndex = 2;
                } else if (number_of_pax >= 25) {
                    priceIndex = 3;
                }

                const selectedPrice = tour_prices[priceIndex] ?? tour_prices[0];
                console.log(selectedPrice);
                return selectedPrice;
            }

            return 0;
        }

        function fetchAndDisplayUserInfo() {
            let user_id = $('#user-id-field').val();
            if(!user_id) return toastr.error('Failed to get the user information. Please select a valid user.', 'Fail');

            $.ajax({
                method: "GET",
                url: `/admin/users/show/${user_id}`,
                success: function (response) {
                    if(!response.user) toastr.error('No user found.', 'Fail');
                    $('#firstname-text').text(response.user.firstname ?? 'First Name Not Found');
                    $('#lastname-text').text(response.user.lastname ?? 'Last Name Not Found');
                    $('#email-text').text(response.user.email);
                    $('#contact-text').text(response.user.contact_no ?? 'Contact Number Not Found');

                }
            })
        }

        function displayReservationDetails() {
            let number_of_pax = $('#number-of-pax-field').val();
            let tour = $('input[name="tour_id"]:checked');
            let trip_date = $('#trip-date-field').val(); 

            $('#pax-text').text(`${number_of_pax} Pax`);
            $('#trip-date-text').text(trip_date);
            $('#tour-name-text').text(tour.data('name'));
        }

        function clearFields(fields) {
            fields.forEach(field => $(field).val(''));
        }

        function toggleTourDetails(type) {
            const fields = [
                '#arrival-datetime-field',
                '#departure-datetime-field',
                '#passport-number-field',
                '#flight-from-field',
                '#flight-to-field'
            ];

            $('.transit-tour-details, .diy-ticket-pass-details').removeClass('active');
            $('input[name="ticket_pass"]').prop('checked', false);

            switch (type) {
                case 'transit':
                    $('.transit-tour-details').addClass('active');
                    break;

                case 'diy':
                    $('.diy-ticket-pass-details').addClass('active');
                    clearFields(fields);
                    break;

                case 'guided':
                case 'seasonal':
                    clearFields(fields);
                    break;
            }
        }

        function addCommasToNumberWithDecimal(number = 0) {
            var parts = number.toFixed(2).toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>
@endpush
