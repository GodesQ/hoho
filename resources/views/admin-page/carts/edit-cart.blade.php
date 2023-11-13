@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Cart Details</h4>
        <a href="{{ route('admin.carts.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="user" class="form-label">Reserved User</label>
                        <input type="text" class="form-control" id="user" name="user" value="{{ $cart->user->email ?? null }}" disabled>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="tour" class="form-label">Tour Name</label>
                        <input type="text" name="tour" id="tour" class="form-control" value="{{ $cart->tour->name ?? null }}" disabled>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="trip_date" class="form-label">Trip Date</label>
                        <input type="date" class="form-control" value="{{ $cart->trip_date }}" disabled>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="tour_type" class="form-label">Tour Type</label>
                        <input type="text" class="form-control" value="{{ $cart->type }}" disabled>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="ticket_pass" class="form-label">Ticket Pass</label>
                        <input type="text" class="form-control" value="{{ $cart->ticket_pass }}" disabled>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="number_of_pass" class="form-label">Number Of Pass</label>
                        <input type="text" class="form-control" value="{{ $cart->number_of_pass }} Pax" disabled>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" value="{{ number_format($cart->amount, 2) }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection