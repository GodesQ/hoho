@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Tour')

@section('content')
    <style>
        .nav-item {
            margin-right: 10px;
        }

        .nav-link {
            border-radius: 5px !important;
        }

        .nav-link.active {
            background-color: #6f0d00 !important;
            color: #fff !important;
            border-radius: 5px !important;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Tour</h4>
            <a href="{{ route('admin.tours.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        {{-- <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                    Tour
                </button>
            </li>
        </ul> --}}

        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#navs-tour" aria-controls="navs-tour" aria-selected="true">
                                            Tour
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#navs-tour-timeslot" aria-controls="navs-tour-timeslot"
                                            aria-selected="false">
                                            Time Slot
                                        </button>
                                    </li>
                                </ul>
                                <hr>
                                <form action="{{ route('admin.tours.update', $tour->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @if ($errors->any())
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">
                                                {{ $error }}
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="navs-tour" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Tour Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ $tour->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Tour Type</label>
                                                        <select name="type" id="type" class="form-select">
                                                            <option value="">---- SELECT TOUR TYPE ----</option>
                                                            <option {{ $tour->type == 'Luxury Tour' ? 'selected' : null }}
                                                                value="Luxury Tour">Luxury Tour</option>
                                                            <option {{ $tour->type == 'City Tour' ? 'selected' : null }}
                                                                value="City Tour">
                                                                City Tour</option>
                                                            <option {{ $tour->type == 'Guided Tour' ? 'selected' : null }}
                                                                value="Guided Tour">Guided Tour</option>
                                                            <option {{ $tour->type == 'Layover Tour' ? 'selected' : null }}
                                                                value="Layover Tour">Layover
                                                                Tour</option>
                                                            <option {{ $tour->type == 'Seasonal Tour' ? 'selected' : null }}
                                                                    value="Seasonal Tour">Seasonal
                                                                    Tour</option>
                                                            <option {{ $tour->type == 'DIY Tour' ? 'selected' : null }}
                                                                value="DIY Tour">DIY
                                                                Tour</option>
                                                            <option {{ $tour->type == 'Others' ? 'selected' : null }}
                                                                value="Others">Others
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="featured_image" class="form-label">Featured
                                                            Image</label>
                                                        <input type="file" class="form-control" name="featured_image"
                                                            id="featured_image" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="tour_provider" class="form-label">Tour Provider</label>
                                                        <select name="tour_provider_id" id="tour_provider"
                                                            class="form-select select2">
                                                            <option value="">--- SELECT TOUR PROVIDER ---</option>
                                                            @foreach ($tour_providers as $tour_provider)
                                                                <option value="{{ $tour_provider->id }}"
                                                                    {{ $tour_provider->id == $tour->tour_provider_id ? 'selected' : null }}>
                                                                    {{ $tour_provider->merchant->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <?php
                                                    $tour_interests = $tour->interests ? json_decode($tour->interests) : [];
                                                    if (!is_array($tour_interests)) {
                                                        $tour_interests = [];
                                                    }
                                                    ?>
                                                    <div class="mb-3">
                                                        <label for="interests" class="form-label">Interests</label>
                                                        <select name="interests[]" id="interests" class="select2" multiple>
                                                            @foreach ($interests as $interest)
                                                                <option value="{{ $interest->id }}"
                                                                    {{ in_array($interest->id, $tour_interests) ? 'selected' : null }}>
                                                                    {{ $interest->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <?php $disabled_days = $tour->disabled_days ? json_decode($tour->disabled_days) : []; ?>
                                                        <label for="bypass_days" class="form-label">Disabled Days</label>
                                                        <select name="disabled_days[]" id="disabled_days" class="select2"
                                                            multiple>
                                                            <option {{ in_array(1, $disabled_days) ? 'selected' : null }}
                                                                value="1">Monday</option>
                                                            <option {{ in_array(2, $disabled_days) ? 'selected' : null }}
                                                                value="2">Tuesday</option>
                                                            <option {{ in_array(3, $disabled_days) ? 'selected' : null }}
                                                                value="3">Wednesday</option>
                                                            <option {{ in_array(4, $disabled_days) ? 'selected' : null }}
                                                                value="4">Thursday</option>
                                                            <option {{ in_array(5, $disabled_days) ? 'selected' : null }}
                                                                value="5">Friday</option>
                                                            <option {{ in_array(6, $disabled_days) ? 'selected' : null }}
                                                                value="6">Saturday</option>
                                                            <option {{ in_array(7, $disabled_days) ? 'selected' : null }}
                                                                value="7">Sunday</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="bypass_days" class="form-label">Number of ByPass
                                                            Days</label>
                                                        <input type="number" name="bypass_days" id="bypass_days"
                                                            class="form-control" value="{{ $tour->bypass_days }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label for="minimum_pax" class="form-label">Minimum Pax</label>
                                                        <input type="number" name="minimum_pax" id="minimum_pax"
                                                            class="form-control" value="{{ $tour->minimum_pax }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="organization-field"
                                                            class="form-label">Organization</label>
                                                        <select name="organization_id" id="organization-field"
                                                            class="form-select">
                                                            <option value="">--- SELECT ORGANIZATION ---</option>
                                                            @foreach ($organizations as $organization)
                                                                <option value="{{ $organization->id }}"
                                                                    {{ $tour->organization_id == $organization->id ? 'selected' : null }}>
                                                                    {{ $organization->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="under_age_limit" class="form-label">Under Age
                                                                    Limit</label>
                                                                <input type="number" class="form-control"
                                                                    name="under_age_limit" id="under_age_limit"
                                                                    value="{{ $tour->under_age_limit }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="over_age_limit" class="form-label">Over Age
                                                                    Limit</label>
                                                                <input type="number" class="form-control"
                                                                    name="over_age_limit" id="over_age_limit"
                                                                    value="{{ $tour->over_age_limit }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <label for="minimum_capacity" class="form-label">Minimum
                                                                Capacity</label>
                                                            <input type="number" class="form-control"
                                                                name="minimum_capacity" id="minimum_capacity"
                                                                value="{{ $tour->minimum_capacity }}">
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label for="capacity" class="form-label">Maximum
                                                                Capacity</label>
                                                            <input type="number" class="form-control" name="capacity"
                                                                id="capacity" value="{{ $tour->capacity }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                <label for="price" class="form-label">Default
                                                                    Price</label>
                                                                <input type="text" class="form-control" name="price"
                                                                    id="price" value="{{ $tour->price }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                <label for="bracket_price_one" class="form-label">Bracket
                                                                    Price
                                                                    (Min of
                                                                    4)</label>
                                                                <input type="text" class="form-control"
                                                                    name="bracket_price_one" id="bracket_price_one"
                                                                    value="{{ $tour->bracket_price_one }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                <label for="bracket_price_two" class="form-label">Bracket
                                                                    Price
                                                                    (Min of
                                                                    10)</label>
                                                                <input type="text" class="form-control"
                                                                    name="bracket_price_two" id="bracket_price_two"
                                                                    value="{{ $tour->bracket_price_two }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                <label for="bracket_price_three"
                                                                    class="form-label">Bracket Price
                                                                    (Min of
                                                                    25)</label>
                                                                <input type="text" class="form-control"
                                                                    name="bracket_price_three" id="bracket_price_three"
                                                                    value="{{ $tour->bracket_price_three }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="date_duration" class="form-label">Date
                                                            Duration</label>
                                                        <input type="text" class="form-control" name="date_duration"
                                                            id="date_duration" readonly>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="my-2">
                                                                    <input type="text" class="form-control"
                                                                        name="start_date_duration"
                                                                        id="start_date_duration"
                                                                        value="{{ $tour->start_date_duration }}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="my-2">
                                                                    <input type="text" class="form-control"
                                                                        name="end_date_duration" id="end_date_duration"
                                                                        value="{{ $tour->end_date_duration }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="total_duration" class="form-label">Total
                                                            Duration</label>
                                                        <input type="text" class="form-control" name="tour_duration"
                                                            id="total_duration" value="{{ $tour->tour_duration }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <?php $attractions_assignment_ids = $tour->attractions_assignments_ids ? json_decode($tour->attractions_assignments_ids) : []; ?>
                                                    <div class="mb-3">
                                                        <label for="attractions_assignments_ids"
                                                            class="form-label">Attractions
                                                            Assignment</label>
                                                        <select name="attractions_assignments_ids[]"
                                                            id="attractions_assignments_ids" class="select2 form-select"
                                                            multiple>
                                                            @foreach ($attractions as $attraction)
                                                                <option
                                                                    {{ in_array($attraction->id, $attractions_assignment_ids) ? 'selected' : null }}
                                                                    value="{{ $attraction->id }}">{{ $attraction->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $tour->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="operating_hours" class="form-label">Operating
                                                            Hours</label>
                                                        <textarea name="operating_hours" id="operating_hours" cols="30" rows="5" class="form-control">{{ $tour->operating_hours }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="tour_itinerary" class="form-label">Tour
                                                            Itinerary</label>
                                                        <textarea name="tour_itinerary" id="tour_itinerary" cols="30" rows="5" class="form-control">{{ $tour->tour_itinerary }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="tour_inclusions" class="form-label">Tour
                                                            Inclusions</label>
                                                        <textarea name="tour_inclusions" id="tour_inclusions" cols="30" rows="5" class="form-control">{{ $tour->tour_inclusions }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="isCancellable" name="is_cancellable"
                                                                    {{ $tour->is_cancellable ? 'checked' : null }} />
                                                                <label class="form-check-label" for="isCancellable">Is
                                                                    Cancellable</label>
                                                            </div>
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="isRefundable" name="is_refundable"
                                                                    {{ $tour->is_refundable ? 'checked' : null }} />
                                                                <label class="form-check-label" for="isRefundable">Is
                                                                    Refundable</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-check ">
                                                                <input name="status" class="form-check-input"
                                                                    type="radio" value="1" id="statusActive"
                                                                    {{ $tour->status ? 'checked' : null }} />
                                                                <label class="form-check-label" for="statusActive"> Active
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input name="status" class="form-check-input"
                                                                    type="radio" value="0" id="statusInactive"
                                                                    {{ !$tour->status ? 'checked' : null }} />
                                                                <label class="form-check-label" for="statusInactive"> In
                                                                    Active
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="navs-tour-timeslot" role="tabpanel">
                                            <div class="w-100 mb-3 d-flex justify-content-end">
                                                <button class="btn btn-primary" type="button"
                                                    onclick="addTimeSlot()">Add Time
                                                    Slot</button>
                                            </div>
                                            <div class="timeslot-container">
                                                @foreach ($tour->timeslots as $timeslot)
                                                    <div class="row timeslot my-3">
                                                        <div class="col-lg-5">
                                                            <input type="time" name="start_time[]"
                                                                class="form-control start-time-field"
                                                                value="{{ $timeslot->start_time->format('H:i') }}">
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <input type="time" name="end_time[]"
                                                                class="form-control end-time-field"
                                                                value="{{ $timeslot->end_time->format('H:i') }}">
                                                        </div>
                                                        <div
                                                            class="col-lg-2 d-flex justify-content-center align-items-center">
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="removeTimeSlot(this)"><i
                                                                    class="bx bx-trash"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" class="btn btn-primary">Save Tour</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h6>Preview of Featured Image</h6>
                                @if ($tour->featured_image)
                                    <img src="{{ URL::asset('assets/img/tours/' . $tour->id . '/' . $tour->featured_image) }}?date={{ $tour->updated_at }}"
                                        alt="{{ $tour->name }}" style="border-radius: 10px !important;"
                                        id="previewImage" width="100%">
                                @else
                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                        alt="{{ $tour->name }}" style="border-radius: 10px !important;"
                                        id="previewImage" width="100%">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

    <script>
        function addTimeSlot() {
            const timeslotContainer = document.querySelector('.timeslot-container');
            const timeslots = document.querySelectorAll('.timeslot');

            if (timeslots && timeslots.length > 0) {
                const newTimeSlot = timeslots[0].cloneNode(true);
                const startTimeInput = newTimeSlot.querySelector('.start-time-field');
                const endTimeInput = newTimeSlot.querySelector('.end-time-field');
                startTimeInput.value = '';
                endTimeInput.value = '';
                timeslotContainer.appendChild(newTimeSlot);
            } else {
                const timeslot_section = `<div class="row timeslot my-3">
                                            <div class="col-lg-5">
                                                <input type="time" name="start_time[]"
                                                    class="form-control start-time-field">
                                            </div>
                                            <div class="col-lg-5">
                                                <input type="time" name="end_time[]"
                                                    class="form-control end-time-field">
                                            </div>
                                            <div class="col-lg-2 d-flex justify-content-center align-items-center">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="removeTimeSlot(this)"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </div>`;
                timeslotContainer.innerHTML = timeslot_section;
            }
        }

        function removeTimeSlot(button) {
            const timeslotContainer = document.querySelector('.timeslot-container');
            const timeslots = document.querySelectorAll('.timeslot');

            const timeslot = button.closest('.timeslot');
            timeslotContainer.removeChild(timeslot);
        }
    </script>

    <script>
        $(document).ready(function() {
            const dateInput = $('#date_duration');
            const startDateInput = $('input[name="start_date_duration"]');
            const endDateInput = $('input[name="end_date_duration"]');
            const totalDurationInput = $('#total_duration');
            const featuredImageInput = document.getElementById('featured_image');
            const previewImage = document.getElementById('previewImage');

            function formatDate(format_date) {
                const t = new Date(format_date);
                const date = ('0' + t.getDate()).slice(-2);
                const month = ('0' + (t.getMonth() + 1)).slice(-2);
                const year = t.getFullYear();
                const fullDate = `${month}/${date}/${year}`;
                return fullDate;
            }

            const getDateArray = (start_date, end_date) => {
                var arr = [];
                while (start_date <= end_date) {
                    arr.push(new Date(start_date));
                    start_date.setDate(start_date.getDate() + 1);
                }
                return arr;
            }

            function updateDateRange(startDate, endDate) {
                dateInput.val(startDate && endDate ? formatDate(startDate) + ' - ' + formatDate(endDate) : '');
                startDateInput.val(startDate ? formatDate(startDate) : '');
                endDateInput.val(endDate ? formatDate(endDate) : '');
                totalDurationInput.val(getDateArray(new Date(startDate.format('MM/DD/YYYY')), new Date(endDate
                    .format('MM/DD/YYYY'))).length);
            }

            dateInput.daterangepicker({
                autoUpdateInput: false,
                minDate: new Date(),
                locale: {
                    cancelLabel: 'Clear'
                },
            }).val(startDateInput && endDateInput ? formatDate(startDateInput.val()) + ' - ' + formatDate(
                endDateInput.val()) : '');

            dateInput.on('apply.daterangepicker', function(ev, picker) {
                updateDateRange(picker.startDate, picker.endDate);
            });

            dateInput.on('cancel.daterangepicker', function() {
                updateDateRange(null, null);
            });

            function handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImage.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }

            featuredImageInput.addEventListener('change', handleFileSelect);
        });
    </script>

    <script>
        let address = document.querySelector('#location');
        let latitude = document.querySelector('#latitude');
        let longitude = document.querySelector('#longitude');
        // let add_badge_btn = document.querySelector('#add-badge-btn');

        // add_badge_btn.addEventListener('click', function() {
        //     console.log(true);
        // })

        function initMap() {
            console.log(false);
            // for search
            let searchBox = new google.maps.places.SearchBox(address);

            google.maps.event.addListener(searchBox, 'places_changed', function() {
                var places = searchBox.getPlaces(),
                    bounds = new google.maps.LatLngBounds(),
                    i, place, lat, long, resultArray, address = places[0].formatted_address;
                lat = places[0].geometry.location.lat()
                long = places[0].geometry.location.lng();
                latitude.value = lat;
                longitude.value = long;
                resultArray = places[0].address_components;
            });
        }

        $(document).ready(function() {
            $('#location').keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loop through each table row
            document.querySelectorAll('.qr-code').forEach(function(qrCodeContainer) {
                const badgeCode = qrCodeContainer.getAttribute('data-badge-code');
                const qrCodeImage = generateQRCode(qrCodeContainer, badgeCode);

                // Create a "Download" button element
                const downloadButton = document.createElement('button');
                downloadButton.className = 'btn btn-primary btn-sm download-qr my-1';
                downloadButton.innerText = 'Download QR';

                // Add an event listener to handle the download
                downloadButton.addEventListener('click', function() {
                    // Create a temporary anchor element to trigger the download
                    const downloadLink = document.createElement('a');
                    downloadLink.href = qrCodeImage;
                    downloadLink.download = 'qr_code.png';
                    downloadLink.style.display = 'none';

                    // Trigger the download
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                });

                // Append the "Download" button to the QR code container
                qrCodeContainer.appendChild(downloadButton);
            });

            function generateQRCode(qrCodeContainer, badgeCode) {
                // Generate the QR code
                const qrCode = new QRCode(qrCodeContainer, {
                    text: badgeCode,
                    width: 150,
                    height: 150
                });

                return qrCode._el.firstChild.toDataURL('image/png');
            }
        });
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1UJdBuEc_a3P3i-efUeZIJmMQ5VXZGgU&libraries=places&callback=initMap">
    </script>
@endpush
