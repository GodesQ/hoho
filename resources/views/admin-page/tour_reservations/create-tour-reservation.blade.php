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
            display: flex !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Book Tour Reservation</h4>
            <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <form action="{{ route('admin.tour_reservations.store') }}" method="POST">
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
                                        <select name="reserved_user_id" id="user" class="reserved_users form-select"
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
                                        <select name="passenger_ids[]" id="passengers"
                                            class="registered_passengers form-select" style="width: 100%;" multiple
                                            max="4"></select>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="referral_codes" class="form-label">Referral Code</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Referral Code" />
                                            <button class="btn btn-primary" type="button" id="button-addon2">Verify</button>
                                        </div>
                                    </div>
                                </div> --}}
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
                                    <h6 id="pax_text">1 Pax</h6>
                                </div>
                            </div>
                            <div class="row ticket_pass_text_container">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Ticket Pass</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6 id="ticket_pass_text">N/A</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Convenience Fee</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6>₱ 99.00</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="text-primary">Travel Pass</h6>
                                </div>
                                <div class="col-xl-6">
                                    <h6>₱ 50.00</h6>
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
                            <div class="my-3 justify-content-end d-flex">
                                <button class="btn btn-primary" id="book-btn">Book Reservation</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <input type="hidden" id="referral_verified" value="1"> --}}
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

        function populateNumberOfPass() {
            let select = document.getElementById("number_of_pass");
            for (var i = 1; i <= 100; i++) {
                var option = document.createElement("option");
                option.value = i;
                option.text = i + " Pax";
                select.appendChild(option);
            }
        }

        populateNumberOfPass();

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

        let guidedTourRadio = document.getElementById("guided_tour");
        let diyTourRadio = document.getElementById("diy_tour");
        let tourSelect = document.getElementById("tour");
        let diyTicketPass = document.querySelector(".diy_ticket_pass");
        let tour = document.querySelector('#tour');
        let number_of_pass = document.querySelector('#number_of_pass');
        let tour_text = document.querySelector('#tour_text');
        let total_amount_text = document.querySelector('#total_amount_text');
        let sub_amount_text = document.querySelector('#sub_amount_text');
        let ticketPassTextContainer = document.querySelector('.ticket_pass_text_container');
        let ticketPassText = document.querySelector('#ticket_pass_text');
        let amount = document.querySelector('#amount');

        guidedTourRadio.addEventListener("change", function() {
            if (guidedTourRadio.checked) {
                fetchAndPopulateTours("{{ route('admin.tours.guided') }}", "GUIDED");
                diyTicketPass.classList.remove("active");
                ticketPassTextContainer.classList.remove("active");
                computeTotalAmount();
            }
        });

        diyTourRadio.addEventListener("change", function() {
            if (diyTourRadio.checked) {
                fetchAndPopulateTours("{{ route('admin.tours.diy') }}", "DIY");
                diyTicketPass.classList.add("active");
                ticketPassTextContainer.classList.add('active');
            }
        });

        $('#one_day_diy_ticket_pass, #two_day_diy_ticket_pass, #three_day_diy_ticket_pass').on('change', function(e) {
            computeTotalAmount();
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

            let totalAmount = 0;

            const selectedTour = tour.options[tour.selectedIndex];
            const prices = JSON.parse(selectedTour.getAttribute('data-value'));

            if (guidedTourRadio.checked && prices && prices.length > 0) {
                let priceIndex = 0;

                if (number_of_pass.value <= 9 && number_of_pass.value >= 4) {
                    priceIndex = 1;
                } else if (number_of_pass.value <= 24 && number_of_pass.value >= 10) {
                    priceIndex = 2;
                } else if (number_of_pass.value >= 25) {
                    priceIndex = 3;
                }

                const selectedPrice = prices[priceIndex] || prices[0];

                const totalAmount = selectedPrice * number_of_pass.value;

                sub_amount_text.innerHTML = `₱ ${addCommasToNumberWithDecimal(selectedPrice)}`;
                amount.value = totalAmount;
                total_amount_text.textContent = `₱ ${addCommasToNumberWithDecimal(totalAmount)}`;
            }


            if (diyTourRadio.checked) {
                const passPrices = [990, 1799, 2499];
                const selectedPassIndex = [...ticketPasses].findIndex(pass => pass.checked);

                if (selectedPassIndex !== -1) {
                    const selectedPass = ticketPasses[selectedPassIndex].value;
                    const passPrice = passPrices[selectedPassIndex];
                    const totalAmount = passPrice * number_of_pass.value;

                    ticket_pass_text.innerHTML = selectedPass;
                    sub_amount_text.innerHTML = `₱ ${addCommasToNumberWithDecimal(passPrice)}`;
                    amount.value = totalAmount;
                    total_amount_text.textContent = `₱ ${addCommasToNumberWithDecimal(totalAmount)}`;
                } else {
                    ticket_pass_text.innerHTML = 'N/A';
                    sub_amount_text.innerHTML = `₱ ${addCommasToNumberWithDecimal(0)}`;
                    amount.value = 0;
                    total_amount_text.textContent = `₱ ${addCommasToNumberWithDecimal(0)}`;
                }
            }

        }

        function addCommasToNumberWithDecimal(number) {
            var parts = number.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>
@endpush
