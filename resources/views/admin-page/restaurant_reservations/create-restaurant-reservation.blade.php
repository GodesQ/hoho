@extends('layouts.admin.layout')

@section('title', 'Add Restaurant Reservation - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Restaurant Reservation</h4>
            <a href="{{ route('admin.restaurant_reservations.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back
                to
                List</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.restaurant_reservations.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="reserved_user_id" class="form-label">Reserved User <span
                                        class="text-danger">*</span></label>
                                <select name="reserved_user_id" id="user" class="reserved_users form-select"
                                    style="width: 100%;">
                                </select>
                                <div class="text-danger">
                                    @error('reserved_user_id')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="merchant-id-field" class="form-label">Merchant</label>
                                <select name="merchant_id" id="merchant-id-field" class="select2">
                                    <option value="">--- SELECT MERCHANT ---</option>
                                    @foreach ($merchants as $merchant)
                                        <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="seats-field" class="form-label">Seats</label>
                                <input type="number" class="form-control" name="seats" id="seats-field" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reservation-date-field" class="form-label">Reservation Date</label>
                                        <input type="date" name="reservation_date" id="reservation-date-field" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reservation-time-field" class="form-label">Reservation Time</label>
                                        <input type="time" name="reservation_time" id="reservation-time-field" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="status-field" class="form-label">Status</label>
                                <select name="status" id="status-field" class="form-control">
                                    <option value="pending">Pending</option>
                                    <option value="declined">Declined</option>
                                    <option value="approved">Approved</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="approved-date-field" class="form-label">Approved Date</label>
                                <input type="date" class="form-control" name="approved_date" readonly id="approved-date-field">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Restaurant Reservation</button>
                </form>
            </div>
        </div>
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
