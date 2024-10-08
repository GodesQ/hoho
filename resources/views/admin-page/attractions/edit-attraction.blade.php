@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Attraction')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Attraction</h4>
            <a href="{{ route('admin.attractions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.attractions.update', $attraction->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Attraction Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $attraction->name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour_provider" class="form-label">Tour Provider</label>
                                        <select name="tour_provider_id" id="tour_provider" class="form-select">
                                            {{-- <option value="">HoHo Manila</option>
                                    <option value=""> Owllah Travel & Tours</option> --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image</label>
                                        <input type="file" class="form-control" name="featured_image" id="featured_image"
                                            accept="image/*">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <?php $interest_ids = $attraction->interest_ids ? json_decode($attraction->interest_ids) : []; ?>
                                    <div class="mb-3">
                                        <label for="interests" class="form-label">Interests</label>
                                        <select name="interests[]" id="interests" multiple class="select2 form-select">
                                            @foreach ($interests as $interest)
                                                <option
                                                    {{ in_array($interest->id, $interest_ids) ? 'selected' : null }}
                                                    value="{{ $interest->id }}">{{ $interest->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <?php $product_category_ids = $attraction->product_category_ids ? json_decode($attraction->product_category_ids) : []; ?>
                                    <div class="mb-3">
                                        <label for="product_category_ids" class="form-label">Product Categories</label>
                                        <select name="product_categories[]" id="product_category_ids" multiple
                                            class="select2 form-select">
                                            @foreach ($product_categories as $product_category)
                                                <option
                                                    {{ in_array($product_category->id, $product_category_ids) ? 'selected' : null }}
                                                    value="{{ $product_category->id }}">{{ $product_category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="text" class="form-control" name="price" id="price"
                                            value="{{ $attraction->price }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_no" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="contact_no" id="contact_no"
                                            value="{{ $attraction->contact_no }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="youtube_id" class="form-label">Youtube Id</label>
                                        <input type="text" class="form-control" name="youtube_id" id="youtube_id"
                                            value="{{ $attraction->youtube_id }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                            value="{{ $attraction->address }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="latitude" class="form-label">Latitude</label>
                                                <input type="text" class="form-control" name="latitude" id="latitude"
                                                    value="{{ $attraction->latitude }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="longitude" class="form-label">longitude</label>
                                                <input type="text" class="form-control" name="longitude"
                                                    id="longitude" value="{{ $attraction->longitude }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $attraction->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="operating_hours" class="form-label">Operating Hours</label>
                                        <textarea name="operating_hours" id="operating_hours" cols="30" rows="5" class="form-control">{{ $attraction->operating_hours }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="organization_id" class="form-label">Organizations </label>
                                        <select name="organization_id" id="organization_id" class="select2 form-select">
                                            <option value="">--- SELECT ORGANIZATION ---</option>
                                            @foreach ($organizations as $organization)
                                                <option
                                                    {{ $organization->id == $attraction->organization_id ? 'selected' : null }}
                                                    value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isCancellable"
                                                    name="is_cancellable"
                                                    {{ $attraction->is_cancellable ? 'checked' : null }} />
                                                <label class="form-check-label" for="isCancellable">Cancellable</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isRefundable"
                                                    name="is_refundable"
                                                    {{ $attraction->is_refundable ? 'checked' : null }} />
                                                <label class="form-check-label" for="isRefundable">Refundable</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isFeatured"
                                                    name="is_featured" {{ $attraction->is_featured ? 'checked' : null }} />
                                                <label class="form-check-label" for="isFeatured">Featured</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isActive"
                                                    name="is_active" {{ $attraction->status ? 'checked' : null }} />
                                                <label class="form-check-label" for="isActive">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h4><i class="bx bx-pin"></i> Nearest</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <?php $nearest_attraction_ids = $attraction->nearest_attraction_ids ? json_decode($attraction->nearest_attraction_ids) : [] ?>
                                        <label for="nearest_attraction_ids" class="form-label">Nearest Attractions</label>
                                        <select name="nearest_attraction_ids[]" id="nearest_attraction_ids" class="select2" multiple>
                                            @foreach ($attractions as $data)
                                                <option value="{{ $data->id }}" {{ in_array($data->id, $nearest_attraction_ids) ? 'selected' : null }}>{{ $data->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <?php $nearest_store_ids = $attraction->nearest_store_ids ? json_decode($attraction->nearest_store_ids) : []; ?>
                                        <label for="nearest_store_ids" class="form-label">Nearest Stores</label>
                                        <select name="nearest_store_ids[]" id="nearest_store_ids" class="select2" multiple>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}" {{ in_array($store->id, $nearest_store_ids) ? 'selected' : null }}>{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <?php $nearest_restaurant_ids = $attraction->nearest_restaurant_ids ? json_decode($attraction->nearest_restaurant_ids) : []; ?>
                                        <label for="nearest_restaurant_ids" class="form-label">Nearest Restaurants</label>
                                        <select name="nearest_restaurant_ids[]" id="nearest_restaurant_ids" class="select2" multiple>
                                            @foreach ($restaurants as $restaurant)
                                                <option value="{{ $restaurant->id }}" {{ in_array($restaurant->id, $nearest_restaurant_ids) ? 'selected' : null }}>{{ $restaurant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <?php $nearest_hotel_ids = $attraction->nearest_hotel_ids ? json_decode($attraction->nearest_hotel_ids) : [] ?>
                                        <label for="nearest_hotel_ids" class="form-label">Nearest Hotels</label>
                                        <select name="nearest_hotel_ids[]" id="nearest_hotel_ids" class="select2" multiple>
                                            @foreach ($hotels as $hotel)
                                                <option value="{{ $hotel->id }}" {{ in_array($hotel->id, $nearest_hotel_ids) ? 'selected' : null }}>{{ $hotel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Images</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php $attraction_images = $attraction->images ? json_decode($attraction->images) : []; ?>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                @if (count($attraction_images) > 0 && isset($attraction_images[0]))
                                                    @fileExists('assets/img/attractions/' . $attraction->id . '/' . $attraction_images[0])
                                                        <img src="{{ URL::asset('assets/img/attractions/' . $attraction->id . '/' . $attraction_images[0]) }}"
                                                            id="previewImage1" alt="Default Image" width="100%"
                                                            height="200px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @elsefileExists
                                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                            id="previewImage1" alt="Default Image" width="100%"
                                                            height="210px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @endfileExists
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $attraction->id }}, '{{ $attraction_images[0] }}')">Remove
                                                        <i class="bx bx-trash"></i></button>
                                                @else
                                                    <input type="file" class="form-control mb-2 image-input"
                                                        accept="image/*" name="images[]" id="image_1"
                                                        onchange="handlePreviewImage(this, 'previewImage1')">
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                        id="previewImage1" alt="Default Image" width="100%"
                                                        height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                @if (count($attraction_images) > 0 && isset($attraction_images[1]))
                                                    @fileExists('assets/img/attractions/' . $attraction->id . '/' . $attraction_images[1])
                                                        <img src="{{ URL::asset('assets/img/attractions/' . $attraction->id . '/' . $attraction_images[1]) }}"
                                                            id="previewImage2" alt="Default Image" width="100%"
                                                            height="200px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @elsefileExists
                                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                            id="previewImage2" alt="Default Image" width="100%"
                                                            height="210px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @endfileExists
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $attraction->id }}, '{{ $attraction_images[1] }}')">Remove
                                                        <i class="bx bx-trash"></i></button>
                                                @else
                                                    <input type="file" class="form-control mb-2 image-input"
                                                        accept="image/*" name="images[]" id="image_2"
                                                        onchange="handlePreviewImage(this, 'previewImage2')">
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                        id="previewImage2" alt="Default Image" width="100%"
                                                        height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                @if (count($attraction_images) > 0 && isset($attraction_images[2]))
                                                    @fileExists('assets/img/attractions/' . $attraction->id . '/' . $attraction_images[2])
                                                        <img src="{{ URL::asset('assets/img/attractions/' . $attraction->id . '/' . $attraction_images[2]) }}"
                                                            id="previewImage3" alt="Default Image" width="100%"
                                                            height="200px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @elsefileExists
                                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                            id="previewImage3" alt="Default Image" width="100%"
                                                            height="210px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @endfileExists
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $attraction->id }}, '{{ $attraction_images[2] }}')">Remove
                                                        <i class="bx bx-trash"></i></button>
                                                @else
                                                    <input type="file" class="form-control mb-2 image-input"
                                                        accept="image/*" name="images[]" id="image_3"
                                                        onchange="handlePreviewImage(this, 'previewImage3')">
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                        id="previewImage3" alt="Default Image" width="100%"
                                                        height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Attraction</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        @if ($attraction->featured_image)
                            <img src="{{ URL::asset('assets/img/attractions/' . $attraction->id . '/' . $attraction->featured_image) }}"
                                id="previewImage" alt="{{ $attraction->name }}" width="100%"
                                style="border-radius: 10px;">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage"
                                alt="Default Image" width="100%" style="border-radius: 10px;">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to handle file selection and display preview image
        function handleFileSelect(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        function handlePreviewImage(event, previewImageId) {
            const file = event.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById(previewImageId);
                    console.log(previewImage);
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        function removeImageBtn(id, image_path) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove attraction image",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('admin.attractions.remove_image') }}`,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            image_path: image_path
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Removed!', response.message, 'success').then(
                                    result => {
                                        if (result.isConfirmed) {
                                            toastr.success(response.message, 'Success');
                                            location.reload();
                                        }
                                    })
                            }
                        }
                    })
                }
            })
        }

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('featured_image').addEventListener('change', handleFileSelect);
    </script>

    <script>
        let address = document.querySelector('#address');
        let latitude = document.querySelector('#latitude');
        let longitude = document.querySelector('#longitude');

        function initMap() {
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
            $('#address').keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1UJdBuEc_a3P3i-efUeZIJmMQ5VXZGgU&libraries=places&callback=initMap">
    </script>
@endpush
