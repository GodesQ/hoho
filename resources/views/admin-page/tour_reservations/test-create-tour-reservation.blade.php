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
                                    <form action="#" class="number-tab-steps wizard-circle">

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
                                                        <select name="reserved_user_id" id="user"
                                                            class="reserved_users form-select" style="width: 100%;"
                                                            required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="trip_date" class="form-label">Trip Date</label>
                                                        <input type="text" readonly placeholder="Select a Trip Date"
                                                            class="form-control" name="trip_date" id="trip_date"
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
                                                        <label class="form-label">Registered Passengers</label>
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
                                                <div class="col-xl-6">
                                                    <h4 class="text-bold mb-3">Reservation Details</h4>
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <label for="" class="form-label text-primary">First
                                                                Name</label>
                                                            <h6 id="firstname-text">James Benedict</h6>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <label for="" class="form-label text-primary">Last
                                                                Name</label>
                                                            <h6 id="lastname-text">Initial</h6>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <label for=""
                                                                class="form-label text-primary">Email</label>
                                                            <h6 id="email-text">james@test.com</h6>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <label for="" class="form-label text-primary">Contact
                                                                Number</label>
                                                            <h6 id="contact-text">+639912323234</h6>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <label for="" class="form-label text-primary">Number
                                                                of Pax</label>
                                                            <h6 id="pax-text">4 Pax</h6>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <label for=""
                                                                class="form-label text-primary">Reservation
                                                                Date</label>
                                                            <h6 id="pax-text">June 20, 2024</h6>
                                                        </div>
                                                        <div class="col-xl-12">
                                                            <label for=""
                                                                class="form-label text-primary">Tour</label>
                                                            <h6 id="pax-text">Heritage Majesty Tour - Malacañang &
                                                                Intramuros Chronicles</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <h4 class="text-bold mb-3">Checkout Summary</h4>
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
                                                            <h6 id="sub_amount_text">₱ 0.00</h6>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <div class="text-primary text-uppercase form-label">Total
                                                                Convenience Fee </div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <h6 id="total_convenience_fee_text">₱ 0.00</h6>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <div class="text-primary text-uppercase form-label">Total Of
                                                                Discount </div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <h6 id="total_discount_text">₱ 0.00</h6>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <div class="text-primary text-uppercase form-label">Total
                                                                Amount</div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <h6 id="total_amount_text">₱ 0.00</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
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

            $("#trip_date").flatpickr({
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

                if (newIndex == 1) {
                    getTours();
                }

                return true;
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                if(currentIndex == 3) {
                    computeCheckoutAmount();
                }
            },
            onFinished: function(event, currentIndex) {
                alert("Form submitted.");
            }
        });

        $('.tour-type-option').click((e) => {
            let type = $(e.target).data('type');

            $('.tour-type-option').removeClass('active');
            $(e.target).addClass('active');

            toggleTourDetails(type);

            $('#tour-type').val(type);
        });

        function validateTransitDetails() {
            return ['#arrival-datetime-field',
                '#departure-datetime-field',
                '#passport-number-field',
                '#flight-from-field',
                '#flight-to-field'
            ].every(field => $(field).val());
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
            const url = `/admin/tours/${type}?arrival_datetime=${arrival_datetime}&departure_datetime=${departure_datetime}`;

            try {
                const response = await $.get(url);
                const tours = response.tours || [];
                const output = tours.length ? tours.map(tour => `
                    <div class="form-check mt-3" style="max-width: 250px; width: 250px;">
                        <input class="form-check-input tour-radio"
                            type="radio" name="tour_id"
                            id="${tour.id}"
                            value="${tour.id}"
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

        function computeCheckoutAmount() {
            let tour_type = $('#tour-type').val();
            let number_of_pax = $('#number-of-pax-field');

            if(tour_type == 'diy') {
                let ticket_pass = $('input[name="ticket_pass"]:checked').data('amount');
                
            }
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

        function addCommasToNumberWithDecimal(number) {
            var parts = number.toFixed(2).toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>
@endpush
