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
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Book Tour Reservation</h4>
            <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <form action="" method="POST">
            <div class="row">
                <div class="col-xl-7 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4>Enter your details</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="type"
                                                id="guided_tour" value="Guided" />
                                            <label class="form-check-label" for="guided_tour">
                                                Guided Tour
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type"
                                                id="diy_tour" value="DIY" />
                                            <label class="form-check-label" for="diy_tour">
                                                DIY Tour
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="Tour" class="form-label">Tour</label>
                                        <select name="tour_id" id="tour" class="form-select">
                                            <option value="">--- SELECT TOUR TYPE FIRST ---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reserved_user" class="form-label">Reserved User</label>
                                        <select name="resevered_user_id" id="user" class="reserved_users form-select" style="width: 100%;">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Registered Passengers</label>
                                        <select name="passenger_ids" id="passengers" class="registered_passengers form-select" style="width: 100%;" multiple max="4"></select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Number of Pax</label>
                                        <select name="number_of_pass" id="number_of_pass" class="form-select">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="trip_date" class="form-label">Trip Date</label>
                                        <input type="date" class="form-control" name="trip_date" id="trip_date">
                                    </div>
                                </div>
                                <div class="col-lg-12 diy_ticket_pass">
                                    <div class="mb-3">
                                        <div class="form-label">DIY Ticket Pass</div>
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="ticket_pass"
                                                id="one_day_diy_ticket_pass" value="1 Day Pass" />
                                            <label class="form-check-label" for="one_day_diy_ticket_pass">
                                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/1-day.png" alt="1 Day Ticket Pass" width="120px">
                                                <h6 class="text-center my-2">₱ 990.00</h6>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="ticket_pass"
                                                id="two_day_diy_ticket_pass" value="2 Day Pass" />
                                            <label class="form-check-label" for="two_day_diy_ticket_pass">
                                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/2-day.png" alt="2 Day Ticket Pass" width="120px">
                                                <h6 class="text-center my-2">₱ 1799.00</h6>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="ticket_pass"
                                                id="three_day_diy_ticket_pass" value="3 Day Pass" />
                                            <label class="form-check-label" for="three_day_diy_ticket_pass">
                                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/3-day.png" alt="3 Day Ticket Pass" width="120px">
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

        const guidedTourRadio = document.getElementById("guided_tour");
        const diyTourRadio = document.getElementById("diy_tour");
        const tourSelect = document.getElementById("tour");
        const diyTicketPass = document.querySelector(".diy_ticket_pass");

        guidedTourRadio.addEventListener("change", function () {
            if (guidedTourRadio.checked) {
                // Make an AJAX call to get the guided tours and populate the select input
                // For demonstration purposes, let's assume you have an array of guided tours
                let guidedTours = [];
                $.ajax({
                    url: "{{ route('admin.tours.guided') }}",
                    method: "GET",
                    success: function(response) {
                        guidedTours = response;

                        tourSelect.innerHTML = "<option value=''>--- SELECT A GUIDED TOUR ---</option>";
                        guidedTours.forEach(function (tour) {
                            const option = document.createElement("option");
                            option.value = tour.id;
                            option.textContent = tour.name;
                            tourSelect.appendChild(option);
                        });
                    }
                })

                // Hide the DIY Ticket Pass options
                diyTicketPass.classList.remove("active");
            }
        });

        diyTourRadio.addEventListener("change", function () {
            if (diyTourRadio.checked) {
               // Make an AJAX call to get the diy tours and populate the select input
                // For demonstration purposes, let's assume you have an array of diy tours
                let diyTours = [];
                $.ajax({
                    url: "{{ route('admin.tours.diy') }}",
                    method: "GET",
                    success: function(response) {
                        diyTours = response;

                        tourSelect.innerHTML = "<option value=''>--- SELECT A DIY TOUR ---</option>";
                        diyTours.forEach(function (tour) {
                            const option = document.createElement("option");
                            option.value = tour.id;
                            option.textContent = tour.name;
                            tourSelect.appendChild(option);
                        });
                    }
                })

                // Show the DIY Ticket Pass options
                diyTicketPass.classList.add("active");
            }
        });


    </script>
@endpush
