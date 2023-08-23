@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Book Tour Reservation')

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
            display: block !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Book Tour Reservation</h4>
            <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <form action="" method="POST">
            @csrf
            <input type="hidden" id="amount" name="amount" value="">
            <div class="row">
                <div class="col-xl-7 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4>Enter your details</h4>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="type" id="guided_tour"
                                                value="Guided" required />
                                            <label class="form-check-label" for="guided_tour">
                                                Guided Tour
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="diy_tour"
                                                value="DIY" required />
                                            <label class="form-check-label" for="diy_tour">
                                                DIY Tour
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour" class="form-label">Tour</label>
                                        <select name="tour_id" id="tour" class="form-select" required>
                                            <option value="">--- SELECT TOUR TYPE FIRST ---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reserved_user" class="form-label">Reserved User</label>
                                        <select name="resevered_user_id" id="user" class="reserved_users form-select"
                                            style="width: 100%;" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="trip_date" class="form-label">Trip Date</label>
                                        <input type="date" class="form-control" name="trip_date" id="trip_date" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Number of Pax</label>
                                        <select name="number_of_pass" id="number_of_pass" class="form-select" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Registered Passengers</label>
                                        <select name="passenger_ids" id="passengers"
                                            class="registered_passengers form-select" style="width: 100%;" multiple
                                            max="4"></select>
                                    </div>
                                </div>
                                <div class="col-lg-12 diy_ticket_pass">
                                    <div class="mb-3">
                                        <div class="form-label">DIY Ticket Pass</div>
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                                name="ticket_pass" id="one_day_diy_ticket_pass" value="1 Day Pass" />
                                            <label class="form-check-label" for="one_day_diy_ticket_pass"
                                                style="cursor: pointer;">
                                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/1-day.png"
                                                    alt="1 Day Ticket Pass" width="120px">
                                                <h6 class="text-center my-2">₱ 990.00</h6>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                                name="ticket_pass" id="two_day_diy_ticket_pass" value="2 Day Pass" />
                                            <label class="form-check-label" for="two_day_diy_ticket_pass"
                                                style="cursor: pointer;">
                                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/2-day.png"
                                                    alt="2 Day Ticket Pass" width="120px">
                                                <h6 class="text-center my-2">₱ 1799.00</h6>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                                name="ticket_pass" id="three_day_diy_ticket_pass" value="3 Day Pass" />
                                            <label class="form-check-label" for="three_day_diy_ticket_pass"
                                                style="cursor: pointer;">
                                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/3-day.png"
                                                    alt="3 Day Ticket Pass" width="120px">
                                                <h6 class="text-center my-2">₱ 2499.00</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4>Book Reservation Summary</h4>
                            <hr>
                            <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Tour</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6 id="tour_text">N/A</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Pax</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6 id="pax_text">4 Pax</h6>
                                </div>
                            </div>
                            <div class="row ticket_pass_text_container">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Ticket Pass</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6 id="ticket_pass_text">1 Days</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Sub Amount</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6 id="sub_amount_text">₱ 0.00</h6>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Total Amount</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6 id="total_amount_text">₱ 0.00</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
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

        var select = document.getElementById("number_of_pass");

        for (var i = 4; i <= 100; i++) {
            var option = document.createElement("option");
            option.value = i;
            option.text = i + " Pax";
            select.appendChild(option);
        }

        function fetchAndPopulateTours(route, placeholder, tours) {
            $.ajax({
                url: route,
                method: "GET",
                success: function(response) {
                    let tours = response;

                    tourSelect.innerHTML = `<option value=''>--- SELECT A ${placeholder} TOUR ---</option>`;
                    tours.forEach(function(tour) {
                        const option = document.createElement("option");
                        option.value = tour.id;
                        option.textContent = tour.name;
                        option.setAttribute('data-value', JSON.stringify([tour.price, tour
                            .bracket_price_one, tour.bracket_price_two, tour
                            .bracket_price_three
                        ]));
                        tourSelect.appendChild(option);
                    });
                },
            });
        }

        const guidedTourRadio = document.getElementById("guided_tour");
        const diyTourRadio = document.getElementById("diy_tour");
        const tourSelect = document.getElementById("tour");
        const diyTicketPass = document.querySelector(".diy_ticket_pass");
        let tour = document.querySelector('#tour');
        let number_of_pass = document.querySelector('#number_of_pass');
        let tour_text = document.querySelector('#tour_text');
        let total_amount_text = document.querySelector('#total_amount_text');
        let sub_amount_text = document.querySelector('#sub_amount_text');
        const ticketPassTextContainer = document.querySelector('.ticket_pass_text_container');

        guidedTourRadio.addEventListener("change", function() {
            if (guidedTourRadio.checked) {
                fetchAndPopulateTours("{{ route('admin.tours.guided') }}", "GUIDED");
                diyTicketPass.classList.remove("active");
                ticketPassTextContainer.remove("active");
                computeTotalAmount();
            }
        });

        diyTourRadio.addEventListener("change", function() {
            if (diyTourRadio.checked) {
                fetchAndPopulateTours("{{ route('admin.tours.diy') }}", "DIY");
                diyTicketPass.classList.add("active");
                ticket_pass_text_container.add('active');
            }
        });

        tour.addEventListener("change", function(e) {
            let selectedTour = tour.options[tour.selectedIndex];
            if (selectedTour.value) {
                tour_text.innerHTML = selectedTour.textContent.length > 30 ? selectedTour.textContent.slice(0, 30) +
                    '...' : selectedTour.textContent;
            } else {
                tour_text.innerHTML = '';
            }
            computeTotalAmount();
        })

        number_of_pass.addEventListener("change", function(e) {
            let selectedNumberOfPass = number_of_pass.options[number_of_pass.selectedIndex];
            document.querySelector('#pax_text').innerHTML = selectedNumberOfPass.textContent;
            computeTotalAmount();
        })

        function computeTotalAmount() {
            const ticketPasses = [
                document.querySelector('#one_day_diy_ticket_pass'),
                document.querySelector('#two_day_diy_ticket_pass'),
                document.querySelector('#three_day_diy_ticket_pass')
            ];

            if (!guidedTourRadio.checked) {
                return;
            }

            const selectedTour = tour.options[tour.selectedIndex];
            const prices = JSON.parse(selectedTour.getAttribute('data-value'));

            if (prices && prices.length > 0) {
                let priceIndex = 0;

                if (number_of_pass.value <= 9 && number_of_pass.value >= 4) {
                    priceIndex = 1;
                } else if (number_of_pass.value <= 24 && number_of_pass.value >= 10) {
                    priceIndex = 2;
                } else if (number_of_pass.value >= 25) {
                    priceIndex = 3;
                }

                const selectedPrice = prices[priceIndex] !== 0 && prices[priceIndex] !== null ? prices[priceIndex] : prices[0];

                sub_amount_text.innerHTML = `₱ ${addCommasToNumberWithDecimal(selectedPrice)}`;
                total_amount_text.textContent = `₱ ${addCommasToNumberWithDecimal(selectedPrice * number_of_pass.value)}`;
            }
        }


        function addCommasToNumberWithDecimal(number) {
            var parts = number.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>
@endpush
