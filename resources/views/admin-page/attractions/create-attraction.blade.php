@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Attraction')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Create Attraction</h4>
            <a href="{{ route('admin.attractions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.attractions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Attraction Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name">
                                        <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour_provider" class="form-label">Tour Provider</label>
                                        <select name="tour_provider_id" id="tour_provider" class="form-select"></select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured-image-field" class="form-label">
                                            Featured Image <span class="text-danger">*</span> <span class="text-warning">(Max Img Size: 2MB)</span>
                                        </label>
                                        <input type="file" class="form-control" name="featured_image" id="featured-image-field">
                                        <span class="text-danger">@error('featured_image'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="interests-field" class="form-label">Interests</label>
                                        <select name="interests[]" id="interests-field" multiple class="select2 form-select">
                                            @foreach ($interests as $interest)
                                                <option value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('interests'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="product-categories-field" class="form-label">Product Categories</label>
                                        <select name="product_categories[]" id="product-categories-field" multiple
                                            class="select2 form-select">
                                            @foreach ($product_categories as $product_category)
                                                <option value="{{ $product_category->id }}">{{ $product_category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('product_categories'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price-field" class="form-label">Price</label>
                                        <input type="text" class="form-control" name="price" id="price-field">
                                        <span class="text-danger">@error('price'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact-no-field" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="contact_no" id="contact-no-field">
                                        <span class="text-danger">@error('contact_no'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="youtube-id-field" class="form-label">
                                            Youtube Id <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="youtube_id" id="youtube-id-field">
                                        <span class="text-danger">@error('youtube_id'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="address-field" class="form-label">Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" id="address-field">
                                        <span class="text-danger">@error('address'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="latitude-field" class="form-label">Latitude <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="latitude" id="latitude-field">
                                                <span class="text-danger">@error('latitude'){{ $message }}@enderror</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="longitude-field" class="form-label">longitude <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="longitude"
                                                    id="longitude-field">
                                                <span class="text-danger">@error('longitude'){{ $message }}@enderror</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description-field" class="form-label">Description</label>
                                        <textarea name="description" id="description-field" cols="30" rows="5" class="form-control"></textarea>
                                        <span class="text-danger">@error('description'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="operating-hours-field" class="form-label">Operating Hours</label>
                                        <textarea name="operating_hours" id="operating-hours-field" cols="30" rows="5" class="form-control"></textarea>
                                        <span class="text-danger">@error('operating_hours'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="organization-field" class="form-label">Organizations <span class="text-danger">*</span></label>
                                        <select name="organization_id" id="organization-field" class="select2 form-select">
                                            <option value="">--- SELECT ORGANIZATION ---</option>
                                            @foreach ($organizations as $organization)
                                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('organization_id'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row my-3">
                                        <div class="col-lg-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isCancellable"
                                                    name="is_cancellable" />
                                                <label class="form-check-label" for="isCancellable">Cancellable</label>
                                                <span class="text-danger">@error('is_cancellable'){{ $message }}@enderror</span>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isRefundable"
                                                    name="is_refundable" />
                                                <label class="form-check-label" for="isRefundable">Refundable</label>
                                                <span class="text-danger">@error('is_refundable'){{ $message }}@enderror</span>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isFeatured"
                                                    name="is_featured" />
                                                <label class="form-check-label" for="isFeatured">Featured</label>
                                                <span class="text-danger">@error('is_featured'){{ $message }}@enderror</span>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isActive"
                                                    name="is_active" />
                                                <label class="form-check-label" for="isActive">Active</label>
                                                <span class="text-danger">@error('is_active'){{ $message }}@enderror</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h4><i class="bx bx-pin"></i> Nearest</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <label for="nearest-attraction-field" class="form-label">Nearest Attractions</label>
                                        <select name="nearest_attraction_ids[]" id="nearest-attraction-field" class="select2" multiple>
                                            @foreach ($attractions as $attraction)
                                                <option value="{{ $attraction->id }}">{{ $attraction->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('nearest_attraction_ids'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <label for="nearest-store-field" class="form-label">Nearest Stores</label>
                                        <select name="nearest_store_ids[]" id="nearest-store-field" class="select2" multiple>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('nearest_store_ids'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <label for="nearest-restaurant-field" class="form-label">Nearest Restaurants</label>
                                        <select name="nearest_restaurant_ids[]" id="nearest-restaurant-field" class="select2" multiple>
                                            @foreach ($restaurants as $restaurant)
                                                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('nearest_restaurant_ids'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="my-3">
                                        <label for="nearest-hotel-field" class="form-label">Nearest Hotels</label>
                                        <select name="nearest_hotel_ids[]" id="nearest-hotel-field" class="select2" multiple>
                                            @foreach ($hotels as $hotel)
                                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('nearest_hotel_ids'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4><i class="bx bx-images"></i> Images</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="images[]" id="image_1"
                                                    onchange="handlePreviewImage(this, 'previewImage1')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                    id="previewImage1" alt="Default Image" width="100%" height="200px"
                                                    style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="images[]" id="image_2"
                                                    onchange="handlePreviewImage(this, 'previewImage2')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                    id="previewImage2" alt="Default Image" width="100%" height="200px"
                                                    style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="images[]" id="image_3"
                                                    onchange="handlePreviewImage(this, 'previewImage3')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                    id="previewImage3" alt="Default Image" width="100%" height="200px"
                                                    style="border-radius: 10px; object-fit: cover;">
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
                        <h6>Featured Image</h6>
                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage"
                            alt="Default Image" width="100%" style="border-radius: 10px;">
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
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
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
