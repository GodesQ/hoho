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
                            @elseif ($reservation->status == 'pending')
                                <div class="badge bg-primary">{{ $reservation->status }}</div>
                            @endif
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="type" id="guided_tour"
                                            value="Guided" required {{ $reservation->type == 'Guided' ? 'checked' : null }} disabled/>
                                        <label class="form-check-label" for="guided_tour">
                                            Guided Tour
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="diy_tour"
                                            value="DIY" required {{ $reservation->type == 'DIY' ? 'checked' : null }} disabled/>
                                        <label class="form-check-label" for="diy_tour">
                                            DIY Tour
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="tour" class="form-label">Tour</label>
                                    <input type="text" class="form-control" name="tour" value="{{ optional($reservation->tour)->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="reserved_user" class="form-label">Reserved User</label>
                                    <input type="text" class="form-control" name="reserved_user" id="reserved_user" value="{{ optional($reservation->user)->email }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" value="{{ $reservation->start_date }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">End Date</label>
                                            <input type="date" class="form-control" value="{{ $reservation->end_date }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="number_of_pass" class="form-label">Number Of Pax</label>
                                    <input type="text" class="form-control" value="{{ $reservation->number_of_pass }} Pax" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <?php
                                        $emails = [];
                                        foreach ($reservation->passengers as $key => $passenger) {
                                            array_push($emails , $passenger['email']);
                                        }
                                    ?>
                                    <label for="number_of_pass" class="form-label">Passengers</label>
                                    <input type="text" class="form-control" value="{{ implode(', ', $emails) }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-12 diy_ticket_pass {{ $reservation->type == 'DIY' ? 'active' : null }}">
                                <div class="mb-3">
                                    <div class="form-label">DIY Ticket Pass</div>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                            name="ticket_pass" id="one_day_diy_ticket_pass" value="1 Day Pass" disabled {{ $reservation->ticket_pass == '1 Day Pass' ? 'checked' : null }} />
                                        <label class="form-check-label" for="one_day_diy_ticket_pass"
                                            style="cursor: pointer;">
                                            <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/1-day.png"
                                                alt="1 Day Ticket Pass" width="120px">
                                            <h6 class="text-center my-2">₱ 990.00</h6>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                            name="ticket_pass" id="two_day_diy_ticket_pass" value="2 Day Pass" disabled {{ $reservation->ticket_pass == '2 Day Pass' ? 'checked' : null }} />
                                        <label class="form-check-label" for="two_day_diy_ticket_pass"
                                            style="cursor: pointer;">
                                            <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/2-day.png"
                                                alt="2 Day Ticket Pass" width="120px">
                                            <h6 class="text-center my-2">₱ 1799.00</h6>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                            name="ticket_pass" id="three_day_diy_ticket_pass" value="3 Day Pass" disabled {{ $reservation->ticket_pass == '3 Day Pass' ? 'checked' : null }} />
                                        <label class="form-check-label" for="three_day_diy_ticket_pass"
                                            style="cursor: pointer;">
                                            <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/tickets/3-day.png"
                                                alt="3 Day Ticket Pass" width="120px">
                                            <h6 class="text-center my-2">₱ 2499.00</h6>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : null }}>Pending</option>
                                        <option value="approved" {{ $reservation->status == 'approved' ? 'selected' : null }}>Approved</option>
                                        <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : null }}>Cancelled</option>
                                        <option value="done" {{ $reservation->status == 'done' ? 'selected' : null }}>Done</option>
                                    </select>
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
                                <h6 id="tour_text">{{ strlen($reservation->tour) > 30 ? substr(optional($reservation->tour)->name, 0, 30) . '...' :  optional($reservation->tour)->name}}</h6>
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
                        <div class="row ticket_pass_text_container">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Ticket Pass</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="ticket_pass_text">{{ $reservation->ticket_pass }}</h6>
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
                                <h6 id="sub_amount_text">₱
                                    <?php
                                        $price = 0;
                                        if($reservation->number_of_pass <= 9 || $reservation->number_of_pass >= 4) {
                                            $price = optional($reservation->tour)->bracket_price_one;
                                        } else if($reservation->number_of_pass <= 24 || $reservation->number_of_pass >= 10) {
                                            $price = optional($reservation->tour)->bracket_price_two;
                                        } else if($reservation->number_of_pass >= 25) {
                                            $price = optional($reservation->tour)->bracket_price_three;
                                        }
                                    ?>
                                    {{ number_format($price) }}
                                </h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Total Amount</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="total_amount_text">₱ {{ number_format($reservation->amount) }}</h6>
                            </div>
                        </div>
                        <div class="my-3 justify-content-end d-flex">
                            <button class="btn btn-primary">Update Reservation</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
