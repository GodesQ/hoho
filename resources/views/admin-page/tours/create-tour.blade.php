@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Tour')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Create Tour</h4>
            <a href="{{ route('admin.tours.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tour Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tour Type</label>
                                        <select name="type" id="type" class="form-select">
                                            <option value="">---- SELECT TOUR TYPE ----</option>
                                            <option value="Luxury Tour">Luxury Tour</option>
                                            <option value="City Tour">City Tour</option>
                                            <option value="Guided Tour">Guided Tour</option>
                                            <option value="DIY Tour">DIY Tour</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image</label>
                                        <input type="file" class="form-control" name="featured_image"
                                            id="featured_image">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour_provider" class="form-label">Tour Provider</label>
                                        <select name="tour_provider_id" id="tour_provider" class="form-select">
                                            <option value="">--- SELECT TOUR PROVIDER ---</option>
                                            @foreach ($tour_providers as $tour_provider)
                                                <option value="{{ $tour_provider->id }}">{{ $tour_provider->merchant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="under_age_limit" class="form-label">Under Age Limit</label>
                                                <input type="number" class="form-control" name="under_age_limit"
                                                    id="under_age_limit">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="over_age_limit" class="form-label">Over Age Limit</label>
                                                <input type="number" class="form-control" name="over_age_limit"
                                                    id="over_age_limit">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="minimum_capacity" class="form-label">Minimum Capacity</label>
                                            <input type="number" class="form-control" name="minimum_capacity"
                                                id="minimum_capacity">
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="capacity" class="form-label">Maximum Capacity</label>
                                            <input type="number" class="form-control" name="capacity" id="capacity">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Default Price <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="price" id="price" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="mb-3">
                                                <label for="bracket_price_one" class="form-label">Bracket Price (Min of 4) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="bracket_price_one" id="bracket_price_one" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="mb-3">
                                                <label for="bracket_price_two" class="form-label">Bracket Price (Min of 10) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="bracket_price_two" id="bracket_price_two" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="mb-3">
                                                <label for="bracket_price_three" class="form-label">Bracket Price (Min of 25) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="bracket_price_three" id="bracket_price_three" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="attractions_assignments" class="form-label">Attractions Assignment</label>
                                        <select name="attractions_assignments_ids[]" id="attractions_assignments" class="select2 form-select" multiple>
                                            @foreach ($attractions as $attraction)
                                                <option value="{{ $attraction->id }}">{{ $attraction->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="operating_hours" class="form-label">Operating Hours</label>
                                        <textarea name="operating_hours" id="operating_hours" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour_itinerary" class="form-label">Tour Itinerary</label>
                                        <textarea name="tour_itinerary" id="tour_itinerary" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour_inclusions" class="form-label">Tour Inclusions</label>
                                        <textarea name="tour_inclusions" id="tour_inclusions" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isCancellable" name="is_cancellable" />
                                                <label class="form-check-label" for="isCancellable">Is Cancellable</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isRefundable" name="is_refundable" />
                                                <label class="form-check-label" for="isRefundable">Is Refundable</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check ">
                                                <input name="status" class="form-check-input" type="radio"
                                                    value="1" id="statusActive" checked />
                                                <label class="form-check-label" for="statusActive"> Active </label>
                                            </div>
                                            <div class="form-check">
                                                <input name="status" class="form-check-input" type="radio"
                                                    value="0" id="statusInactive" />
                                                <label class="form-check-label" for="statusInactive"> In Active </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Save Tour</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image"
                        style="border-radius: 10px !important;" id="previewImage" width="100%">
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // const dateInput = $('#date_duration');
        // const startDateInput = $('input[name="start_date_duration"]');
        // const endDateInput = $('input[name="end_date_duration"]');
        // const totalDurationInput = $('#total_duration');
        // const previewImage = document.getElementById('previewImage');

        // function formatDate(format_date) {
        //     const t = new Date(format_date);
        //     const date = ('0' + t.getDate()).slice(-2);
        //     const month = ('0' + (t.getMonth() + 1)).slice(-2);
        //     const year = t.getFullYear();
        //     const fullDate = `${month}/${date}/${year}`;
        //     return fullDate;
        // }

        // const getDateArray = (start_date, end_date) => {
        //     var arr = [];
        //     while (start_date <= end_date) {
        //         arr.push(new Date(start_date));
        //         start_date.setDate(start_date.getDate() + 1);
        //     }
        //     return arr;
        // }

        // function updateDateRange(startDate, endDate) {
        //     dateInput.val(startDate && endDate ? formatDate(startDate) + ' - ' + formatDate(endDate) : '');
        //     startDateInput.val(startDate ? formatDate(startDate) : '');
        //     endDateInput.val(endDate ? formatDate(endDate) : '');
        //     totalDurationInput.val(getDateArray(new Date(startDate.format('MM/DD/YYYY')),  new Date(endDate.format('MM/DD/YYYY'))).length);
        // }

        // dateInput.daterangepicker({
        //     autoUpdateInput: false,
        //     minDate: new Date(),
        //     locale: {
        //         cancelLabel: 'Clear'
        //     },
        // }).val(startDateInput && endDateInput ? formatDate(startDateInput.val()) + ' - ' + formatDate(endDateInput.val()) : '');

        // dateInput.on('apply.daterangepicker', function(ev, picker) {
        //     updateDateRange(picker.startDate, picker.endDate);
        // });

        // dateInput.on('cancel.daterangepicker', function() {
        //     updateDateRange(null, null);
        // });

        const featuredImageInput = document.getElementById('featured_image');

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
@endpush
