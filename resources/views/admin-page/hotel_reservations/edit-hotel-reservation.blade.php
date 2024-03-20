@extends('layouts.admin.layout')

@section('title', 'Edit Hotel Reservation - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Hotel Reservation</h4>
            <a href="{{ route('admin.hotel_reservations.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <form action="{{ route('admin.hotel_reservations.update', $reservation->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xxl-8 col-xl-7 col-lg-6">
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
                    <div class="card">
                        <div class="card-body">
                            <h4>Room Details</h4>
                            <hr>    
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="room_name" class="form-label">Room Name</label>
                                    <h6>{{ $reservation->room->room_name }}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <label for="room_price" class="form-label">Room Price</label>
                                    <h6>₱ {{ number_format($reservation->room->price, 2) }}</h6>
                                </div>
                            </div>
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="merchant" class="form-label">Merchant</label>
                                    <h6>{{ $reservation->room->merchant->name }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-5 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reserved_user_id" class="form-label">Reserved User <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" disabled
                                            value="{{ $reservation->reserved_user->email ?? null }}">
                                        <div class="text-danger">
                                            @error('reserved_user_id')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="merchant" class="form-label">Merchant <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" disabled
                                            value="{{ $reservation->room->merchant->name ?? null }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="room-id-field" class="form-label">Room <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" disabled
                                            value="{{ $reservation->room->room_name ?? null }}">
                                        <div class="text-danger">
                                            @error('room_id')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="number-of-pax-field" class="form-label">Number of Pax <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="number_of_pax"
                                            id="number-of-pax-field" value="{{ $reservation->number_of_pax }}">
                                        <div class="text-danger">
                                            @error('number_of_pax')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reservation-date-field" class="form-label">Reservation Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="reservation_date" id="reservation-date-field"
                                            class="form-control"
                                            value="{{ $reservation->reservation_date->format('Y-m-d') }}">
                                        <div class="text-danger">
                                            @error('reservation_date')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reservation-time-field" class="form-label">Reservation Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" name="reservation_time" id="reservation-time-field"
                                            class="form-control" value="{{ $reservation->reservation_time }}">
                                        <div class="text-danger">
                                            @error('reservation_time')
                                                {{ $message }}
                                            @enderror
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
                                                {{ $reservation->status == 'declined' ? 'selected' : null }}>Declined
                                            </option>
                                            <option value="approved"
                                                {{ $reservation->status == 'approved' ? 'selected' : null }}>Approved
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="approved-date-field" class="form-label">Approved Date</label>
                                        <input type="text" name="approved_date" id="approved-date-field" disabled
                                            class="form-control" value="{{ $reservation->approved_date }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Hotel Reservation</button>
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

        $('#merchant-field').on('change', function(e) {
            let merchant_id = e.target.value;
            $.ajax({
                url: '{{ route('admin.rooms.lookup') }}' + '/' + merchant_id + '?type=merchant',
                method: 'GET',
                success: function(data) {
                    data.rooms.forEach(room => {
                        var newOption = new Option(room.room_name, room.id, false, false);
                        $('#room-id-field').append(newOption).trigger('change');
                    });
                }
            })
        })
    </script>
@endpush
