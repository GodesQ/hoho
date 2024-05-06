@extends('layouts.admin.layout')

@section('title')
    Tour Reservation Details -
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
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Tour Reservation Details</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Tour Reservation Details</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark btn-sm"><i class="bx bx-undo"></i>
                    Back to List</a>
                <button class="btn btn-danger btn-sm"><i class="bx bx-trash"></i> Delete</button>
            </div>
        </section>

        <div class="section-body my-2">
            <form action="{{ route('admin.tour_reservations.update', $reservation->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card my-2">
                            <div class="card-body pb-2">
                                <h5 class="card-title fw-semibold">Customer</h5>
                                <ul class="d-flex justify-content-start align-items-center gap-5 list-unstyled">
                                    <li>
                                        <div class="fw-semibold text-primary form-label"><i class="bx bx-user"></i> Name
                                        </div>
                                        {{ $reservation->customer_details->firstname }}
                                        {{ $reservation->customer_details->lastname }}
                                    </li>
                                    <li>
                                        <div class="fw-semibold text-primary form-label"><i class="bx bx-phone"></i> Phone
                                        </div>
                                        {{ $reservation->customer_details->contact_no }}
                                    </li>
                                    <li>
                                        <div class="fw-semibold text-primary form-label"><i class="bx bx-envelope"></i>
                                            Email </div>
                                        {{ $reservation->customer_details->email }}
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
                                        {{ $reservation->tour->type }}
                                    </div>
                                    <div class="col-lg-4 mb-4">
                                        <div class="fw-semibold text-primary form-label">Default Price </div>
                                        {{ number_format($reservation->tour->price, 2) }}
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
                                    <div class="col-lg-6 mb-4">
                                        <div class="fw-semibold text-primary form-label">Trip Date </div>
                                        <input type="date" name="trip_date" value="{{ $reservation->start_date }}"
                                            class="form-control">
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
    @endsection
