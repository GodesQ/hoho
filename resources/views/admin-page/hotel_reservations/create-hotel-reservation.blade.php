@extends('layouts.admin.layout')

@section('title', 'Add Hotel Reservation - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Hotel Reservation</h4>
            <a href="{{ route('admin.hotel_reservations.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('admin.hotel_reservations.store') }}" method="post">
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
                                <label for="merchant" class="form-label">Merchant <span class="text-danger">*</span></label>
                                <select class="select2" name="hotel_id" id="merchant-field">
                                    <option value="">--- SELECT MERCHANT ---</option>
                                    @foreach ($merchant_hotels as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ $hotel->id == old('hotel_id') ? 'selected' : null }}>{{ $hotel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="room-id-field" class="form-label">Room <span
                                        class="text-danger">*</span></label>
                                <select name="room_id" id="room-id-field" class="select2"></select>
                                <div class="text-danger">
                                    @error('room_id')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="number-of-pax-field" class="form-label">Number of Pax <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="number_of_pax" id="number-of-pax-field"
                                    value="{{ old('number_of_pax') }}">
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
                                    class="form-control" value="{{ old('reservation_date') }}">
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
                                    class="form-control" value="{{ old('reservation_time') }}">
                                <div class="text-danger">
                                    @error('reservation_time')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="status-field" class="form-label">Status</label>
                                <select name="status" id="status-field" class="form-select">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : null }}>Pending
                                    </option>
                                    <option value="declined" {{ old('status') == 'declined' ? 'selected' : null }}>Declined
                                    </option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : null }}>Approved
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="approved-date-field" class="form-label">Approved Date</label>
                                <input type="text" name="approved_date" id="approved-date-field" disabled
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Hotel Reservation</button>
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

        $(function() {
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;

            // or instead:
            // var maxDate = dtToday.toISOString().substr(0, 10);
            $('#reservation-date-field').attr('min', maxDate);
        });
    </script>
@endpush
