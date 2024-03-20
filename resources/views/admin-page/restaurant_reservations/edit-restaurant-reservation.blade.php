@extends('layouts.admin.layout')

@section('title', 'Edit Restaurant Reservation - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Restaurant Reservation</h4>
            <a href="{{ route('admin.restaurant_reservations.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back
                to List</a>
        </div>

        <form action="{{ route('admin.restaurant_reservations.update', $reservation->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xl-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4>Guest Details</h4>
                            <hr>    
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="email" class="form-label">Email</label>
                                    <h6>{{ $reservation->reserved_user->email }}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <label for="email" class="form-label">Contact Number</label>
                                    <h6>+{{ $reservation->reserved_user->countryCode }} {{ $reservation->reserved_user->contact_no }}</h6>
                                </div>
                            </div>
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="email" class="form-label">Firstname</label>
                                    <h6>{{ $reservation->reserved_user->firstname ?? 'No Firstname Found' }}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <label for="email" class="form-label">Lastname</label>
                                    <h6>{{ $reservation->reserved_user->lastname ?? 'No Lastname Found' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="seats-field" class="form-label">Seats</label>
                                        <input type="number" class="form-control" name="seats" id="seats-field"
                                            class="form-control" value="{{ $reservation->seats }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="reservation-date-field" class="form-label">Reservation
                                                    Date</label>
                                                <input type="date" name="reservation_date" id="reservation-date-field"
                                                    class="form-control"
                                                    value="{{ $reservation->reservation_date->format('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="reservation-time-field" class="form-label">Reservation
                                                    Time</label>
                                                <input type="time" name="reservation_time" id="reservation-time-field"
                                                    class="form-control" value="{{ $reservation->reservation_time }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="status-field" class="form-label">Status</label>
                                        <select name="status" id="status-field" class="form-select">
                                            <option value="pending"
                                                {{ $reservation->status == 'pending' ? 'selected' : null }}>Pending
                                            </option>
                                            <option value="declined"
                                                {{ $reservation->status == 'declined' ? 'selected' : null }}>
                                                Declined</option>
                                            <option value="approved"
                                                {{ $reservation->status == 'approved' ? 'selected' : null }}>
                                                Approved</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="approved-date-field" class="form-label">Approved Date</label>
                                        <input type="text" class="form-control" name="approved_date" readonly
                                            value="{{ $reservation->approved_date }}" id="approved-date-field">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary w-100">Save Restaurant Reservation</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('.reserved_users').select2({
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
    </script>
@endpush
