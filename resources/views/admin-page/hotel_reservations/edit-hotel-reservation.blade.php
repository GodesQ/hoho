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
                                    <label for="email" class="form-label text-primary">Email</label>
                                    <h6>{{ $reservation->reserved_user->email ?? '' }}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <label for="email" class="form-label text-primary">Contact Number</label>
                                    <h6>+{{ $reservation->reserved_user->countryCode ?? '' }} {{ $reservation->reserved_user->contact_no ?? '' }}</h6>
                                </div>
                            </div>
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="email" class="form-label text-primary">Firstname</label>
                                    <h6>{{ $reservation->reserved_user->firstname ?? 'No Firstname Found' }}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <label for="email" class="form-label text-primary">Lastname</label>
                                    <h6>{{ $reservation->reserved_user->lastname ?? 'No Lastname Found' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4>Room Details</h4>
                            <hr>    
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="room_name" class="form-label text-primary">Room Name</label>
                                    <h6>{{ $reservation->room->room_name ?? 'No Room Name' }}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <label for="room_price" class="form-label text-primary">Room Price</label>
                                    <h6>₱ {{ number_format(($reservation->room->price ?? 0), 2) }}</h6>
                                </div>
                            </div>
                            <div class="row my-1">
                                <div class="col-xl-6">
                                    <label for="merchant" class="form-label text-primary">Merchant</label>
                                    <h6>{{ $reservation->room->merchant->name ?? 'Merchant Name' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($reservation->transaction)
                        <div class="card">
                            <div class="card-body">
                                <h4>Transaction Details</h4>
                                <hr>   
                                <div class="row my-1">
                                    <div class="col-xl-4">
                                        <label for="" class="form-label text-primary">Reference Number</label>
                                        <h6>{{ $reservation->transaction->reference_no }}</h6>
                                    </div>
                                    <div class="col-xl-4">
                                        <label for="" class="form-label text-primary">Total Amount</label>
                                        <h6>{{ number_format($reservation->transaction->payment_amount, 2) }}</h6>
                                    </div>
                                    <div class="col-xl-4">
                                        <label for="" class="form-label text-primary">Aqwire Total Amount</label>
                                        <h6>{{ $reservation->transaction->aqwire_totalAmount }}</h6>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="mb-3">
                                            <label for="payment_url" class="form-label">Payment URL</label> <br>
                                            <a href="{{ $reservation->transaction->payment_url }}"
                                                target="_blank">{{ $reservation->transaction->payment_url ?? 'No Payment URL Found' }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-xxl-4 col-xl-5 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="adult-quantity-field" class="form-label text-primary">Adult Quantity<span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="adult_quantity"
                                            id="adult-quantity-field" value="{{ $reservation->adult_quantity }}">
                                        <div class="text-danger">
                                            @error('adult_quantity')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="children-quantity-field" class="form-label text-primary">Children Quantity<span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="children_quantity"
                                            id="children-quantity-field" value="{{ $reservation->children_quantity }}">
                                        <div class="text-danger">
                                            @error('children_quantity')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="checkin-date-field" class="form-label text-primary">Check-In Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="checkin_date" id="checkin-date-field"
                                            class="form-control"
                                            value="{{ $reservation->checkin_date->format('Y-m-d') }}">
                                        <div class="text-danger">
                                            @error('checkin_date')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="checkout-date-field" class="form-label text-primary">Check-Out Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="checkout_date" id="checkout-date-field"
                                            class="form-control" value="{{ $reservation->checkout_date->format('Y-m-d') }}">
                                        <div class="text-danger">
                                            @error('checkout_date')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="status-field" class="form-label text-primary">Status</label>
                                        <select name="status" id="status-field" class="form-select select2">
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
                                        <label for="approved-date-field" class="form-label text-primary">Approved Date</label>
                                        <input type="text" name="approved_date" id="approved-date-field" disabled
                                            class="form-control" value="{{ $reservation->approved_date }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label text-primary">Payment Status</label> <br>
                                        @if($reservation->payment_status == 'paid')
                                            <div class="badge bg-label-success">{{ $reservation->payment_status }}</div>
                                        @else
                                            <div class="badge bg-label-warning">{{ $reservation->payment_status }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="btn-group">
                                <button class="btn btn-primary">Save Hotel Reservation</button>
                                @if($reservation->transaction_id && $reservation->payment_status == 'unpaid')
                                    <button class="btn btn-outline-primary">Send New Payment Link</button>
                                @endif
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
        $('#checkin-date-field, #checkout-date-field').flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d",
            minDate: 'today',
        })

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
