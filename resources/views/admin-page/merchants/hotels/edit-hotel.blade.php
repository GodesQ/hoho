@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Hotel')

@section('content')

    <style>
        .main-featured-image-container {
            display: none;
        }

        .main-featured-image-container.active {
            display: block;
        }
    </style>
    
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Hotel</h4>
            <a href="{{ route('admin.merchants.hotels.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.merchants.hotels.update', $hotel->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <hr>
                                    <h5><i class="bx bx-box"></i> Merchant Information</h5>
                                    <hr>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Merchant Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ $hotel->merchant->name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Merchant Code</label>
                                        <input type="text" class="form-control" name="code" id="code"
                                            value="{{ $hotel->merchant->code }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Merchant Type <span
                                                class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option {{ $hotel->merchant->type == 'Hotel' ? 'selected' : null }}
                                                value="Hotel">Hotel</option>
                                            <option {{ $hotel->merchant->type == 'Store' ? 'selected' : null }}
                                                value="Store">Store</option>
                                            <option {{ $hotel->merchant->type == 'Tour Provider' ? 'selected' : null }}
                                                value="Tour Provider">Tour Provider</option>
                                            <option {{ $hotel->merchant->type == 'Restaurant' ? 'selected' : null }}
                                                value="Restaurant">Restaurant</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image</label>
                                        <input type="file" class="form-control" name="featured_image" id="featured_image"
                                            value="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nature_of_business" class="form-label">Nature of Business</label>
                                        <input type="text" class="form-control" name="nature_of_business"
                                            id="nature_of_business" value="{{ $hotel->merchant->nature_of_business }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="organizations" class="form-label">Organizations</label>
                                        <select name="organization_id" id="organization_id" class="select2 form-select">
                                            <option value="">-- SELECT ORGANIZATION --- </option>
                                            @foreach ($organizations as $organization)
                                                <option
                                                    {{ $organization->id == $hotel->merchant->organization_id ? 'selected' : null }}
                                                    value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                            value="{{ $hotel->merchant->address }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="latitude" class="form-label">Latitude</label>
                                                <input type="text" class="form-control" name="latitude" id="latitude"
                                                    value="{{ $hotel->merchant->latitude }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="longitude" class="form-label">longitude</label>
                                                <input type="text" class="form-control" name="longitude" id="longitude"
                                                    value="{{ $hotel->merchant->longitude }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $hotel->merchant->description }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <hr>
                                    <h5><i class="bx bx-box"></i> Hotel Information</h5>
                                    <hr>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="interests" class="form-label">Interests</label>
                                        <select name="interests[]" id="interests" class="form-select select2" multiple>
                                            <?php $selected_interests = $hotel->interests ? json_decode($hotel->interests) : [] ?>
                                            @foreach ($interests as $interest)
                                                <option {{ in_array($interest->id, $selected_interests) ? 'selected' : null }} value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="payment_options" class="form-label">Payment Options</label>
                                        <input type="text" class="form-control" name="payment_options"
                                            id="payment_options" value="{{ $hotel->payment_options }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" id="contact_number"
                                            class="form-control" value="{{ $hotel->contact_number }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="text" name="contact_email" id="contact_email"
                                            class="form-control" value="{{ $hotel->contact_email }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="business_hours" class="form-label">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $hotel->business_hours }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $hotel->tags }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" {{ $hotel->merchant->is_active ? 'checked' : null }} />
                                            <label class="form-check-label" for="isActive">Active</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isFeatured" name="is_featured" {{ $hotel->merchant->is_featured ? 'checked' : null }} />
                                            <label class="form-check-label" for="isFeatured">Featured</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 main-featured-image-container {{ $hotel->merchant->is_featured ? 'active' : null }}">
                                    <div class="mb-3">
                                        <label for="main_featured_image" class="form-label">Main Featured Image</label>
                                        <input type="file" class="form-control mb-2 image-input" accept="image/*" name="main_featured_image" id="main_featured_image">
                                        @if ($hotel->merchant->main_featured_image)
                                            <img src="{{ URL::asset('/assets/img/hotels/' . $hotel->merchant->id . '/' . $hotel->merchant->main_featured_image) }}" id="preview-main-featured-image" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                        @else
                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="preview-main-featured-image" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Images</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php $hotel_images = $hotel->images ? json_decode($hotel->images) : []; ?>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="images[]" id="image_1"
                                                    onchange="handlePreviewImage(this, 'previewImage1')">
                                                @if (count($hotel_images) > 0 && isset($hotel_images[0]))
                                                    <img src="{{ URL::asset('assets/img/hotels/' . $hotel->merchant->id . '/' . $hotel_images[0]) }}"
                                                        id="previewImage1" alt="Default Image" width="100%"
                                                        height="200px"
                                                        style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $hotel->id }}, '{{ $hotel_images[0] }}')">Remove
                                                        <i class="bx bx-trash"></i></button>
                                                @else
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                        id="previewImage1" alt="Default Image" width="100%"
                                                        height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="images[]" id="image_2"
                                                    onchange="handlePreviewImage(this, 'previewImage2')">
                                                @if (count($hotel_images) > 0 && isset($hotel_images[1]))
                                                    <img src="{{ URL::asset('assets/img/hotels/' . $hotel->merchant->id . '/' . $hotel_images[1]) }}"
                                                        id="previewImage2" alt="Default Image" width="100%"
                                                        height="200px"
                                                        style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $hotel->id }}, '{{ $hotel_images[1] }}')">Remove
                                                        <i class="bx bx-trash"></i></button>
                                                @else
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                        id="previewImage2" alt="Default Image" width="100%"
                                                        height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="images[]" id="image_3"
                                                    onchange="handlePreviewImage(this, 'previewImage3')">
                                                @if (count($hotel_images) > 0 && isset($hotel_images[2]))
                                                    <img src="{{ URL::asset('assets/img/hotels/' . $hotel->merchant->id . '/' . $hotel_images[2]) }}"
                                                        id="previewImage3" alt="Default Image" width="100%"
                                                        height="200px"
                                                        style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $hotel->id }}, '{{ $hotel_images[2] }}')">Remove
                                                        <i class="bx bx-trash"></i></button>
                                                @else
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
                            <button class="btn btn-primary">Save Hotel</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        @if ($hotel->merchant->featured_image)
                            <img src="{{ URL::asset('/assets/img/hotels/' . $hotel->merchant->id . '/' . $hotel->merchant->featured_image) }}"
                                alt="" style="border-radius: 10px;" width="100%">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt=""
                                style="border-radius: 10px;" width="100%">
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

        function handleMainFeaturedImageSelect(e) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById('preview-main-featured-image');
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        function handleIsFeaturedChange(e) {
            let main_featured_image_con = document.querySelector('.main-featured-image-container');
            if(e.target.checked) {
                main_featured_image_con.classList.add('active');
            } else {
                main_featured_image_con.classList.remove('active');
            }
        }

        function removeImageBtn(id, image_path) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove hotel image",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('admin.merchants.hotels.remove_image') }}`,
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

        document.getElementById('isFeatured').addEventListener('change', handleIsFeaturedChange);
        document.getElementById('main_featured_image').addEventListener('change', handleMainFeaturedImageSelect);
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
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxz6s-kdoLiIM5Lr__lve7jyf9WTjlnE4&libraries=places&callback=initMap">
    </script>
@endpush
