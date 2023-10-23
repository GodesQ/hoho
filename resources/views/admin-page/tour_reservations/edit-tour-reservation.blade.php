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
                                        <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : null }}>Pending</option>
                                        <option value="approved" {{ $reservation->status == 'approved' ? 'selected' : null }}>Approved</option>
                                        <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : null }}>Cancelled</option>
                                        <option value="done" {{ $reservation->status == 'done' ? 'selected' : null }}>Done</option>
                                    </select>
                                </div>
                            </div>
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
                                    <input type="text" class="form-control" name="tour" value="{{ optional($reservation->tour)->name ?? 'Deleted Tour' }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="reserved_user" class="form-label">Reserved User</label>
                                    <input type="text" class="form-control" name="reserved_user" id="reserved_user" value="{{ optional($reservation->user)->email ? optional($reservation->user)->email : 'Deleted User' }}" disabled>
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
                                        if(is_array($reservation->passengers)) {
                                            foreach ($reservation->passengers as $key => $passenger) {
                                                array_push($emails , $passenger['email']);
                                            }
                                        }
                                    ?>
                                    <label for="number_of_pass" class="form-label">Passengers</label>
                                    <input type="text" class="form-control" value="{{ implode(', ', $emails) }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-12 diy_ticket_pass {{ $reservation->type == 'DIY' ? 'active' : null }}">
                                <div class="mb-3">
                                    <div class="form-label">DIY Ticket Pass</div>
                                    @foreach ($ticket_passes as $ticket_pass)
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input diy_ticket_pass_radio" type="radio"
                                                name="ticket_pass" id="{{ $ticket_pass->name }}"
                                                value="{{ $ticket_pass->name }}" disabled
                                                data-amount="{{ $ticket_pass->price }}" {{ $ticket_pass->name == $reservation->ticket_pass ? 'checked' : null }} />
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
                                    @if(optional($reservation->transaction)->payment_status == 'success')
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
                            <button class="btn btn-primary w-100">Update Reservation <i class="bx bx-save"></i></button>
                            <div class="w-100">
                                <a href="{{ route('admin.transactions.edit', $reservation->order_transaction_id) }}" class="w-100 btn-outline-primary btn">See Transaction Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection