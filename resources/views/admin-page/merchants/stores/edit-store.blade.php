@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Store')

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
            <h4 class="fw-bold py-3 mb-4">Edit Store</h4>
            <a href="{{ route('admin.merchants.stores.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger my-2" style="border-left: 5px solid red;">
                                Invalid Fields. Please check all fields before submitting the form.
                            </div>
                        @endif
                        <form action="{{ route('admin.merchants.stores.update', $store->id) }}" method="POST"
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
                                            value="{{ $store->merchant->name }}" required>
                                        <div class="text-danger">@error('name'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Merchant Code</label>
                                        <input type="text" class="form-control" name="code" id="code"
                                            value="{{ $store->merchant->code }}">
                                        <div class="text-danger">@error('code'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Merchant Type <span
                                                class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option {{ $store->merchant->type == 'Store' ? 'selected' : null }}
                                                value="Store">Store</option>
                                        </select>
                                        <div class="text-danger">@error('type'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">
                                            Featured Image <span class="text-danger">*</span>
                                            <span class="text-warning">(Maximum of 2MB)</span>
                                        </label>
                                        <input type="file" class="form-control" name="featured_image" id="featured_image"
                                            value="">
                                        <div class="text-danger">@error('featured_image'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nature_of_business" class="form-label">Nature of Business</label>
                                        <input type="text" class="form-control" name="nature_of_business"
                                            id="nature_of_business" value="{{ $store->merchant->nature_of_business }}">
                                        <div class="text-danger">@error('nature_of_business'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="organization_id" class="form-label">Organization</label>
                                        <select name="organization_id" id="organization_id" class="select2 form-select">
                                            <option value="">-- SELECT ORGANIZATION --- </option>
                                            @foreach ($organizations as $organization)
                                                <option
                                                    {{ $organization->id == $store->merchant->organization_id ? 'selected' : null }}
                                                    value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger">@error('organization_id'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                            value="{{ $store->merchant->address }}">
                                        <div class="text-danger">@error('address'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="latitude" class="form-label">Latitude</label>
                                                <input type="text" class="form-control" name="latitude" id="latitude"
                                                    value="{{ $store->merchant->latitude }}">
                                                <div class="text-danger">@error('latitude'){{ $message }}@enderror</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="longitude" class="form-label">longitude</label>
                                                <input type="text" class="form-control" name="longitude" id="longitude"
                                                    value="{{ $store->merchant->longitude }}">
                                                <div class="text-danger">@error('longitude'){{ $message }}@enderror</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $store->merchant->description }}</textarea>
                                        <div class="text-danger">@error('description'){{ $message }}@enderror</div>
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
                                            <?php $selected_interests = $store->interests ? json_decode($store->interests) : [] ?>
                                            @foreach ($interests as $interest)
                                                <option {{ in_array($interest->id, $selected_interests) ? 'selected' : null }} value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger">@error('interests'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="payment_options" class="form-label">Payment Options</label>
                                        <input type="text" class="form-control" name="payment_options"
                                            id="payment_options" value="{{ $store->payment_options }}">
                                        <div class="text-danger">@error('payment_options'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" id="contact_number"
                                            class="form-control" value="{{ $store->contact_number }}">
                                        <div class="text-danger">@error('contact_number'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="text" name="contact_email" id="contact_email"
                                            class="form-control" value="{{ $store->contact_email }}">
                                        <div class="text-danger">@error('contact_email'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="business_hours" class="form-label">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $store->business_hours }}</textarea>
                                        <div class="text-danger">@error('business_hours'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $store->tags }}</textarea>
                                        <div class="text-danger">@error('tags'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" {{ $store->merchant->is_active ? 'checked' : null }} />
                                            <label class="form-check-label" for="isActive">Active</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isFeatured" name="is_featured" {{ $store->merchant->is_featured ? 'checked' : null }} />
                                            <label class="form-check-label" for="isFeatured">Featured</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 main-featured-image-container {{ $store->merchant->is_featured ? 'active' : null }}">
                                    <div class="mb-3">
                                        <label for="main_featured_image" class="form-label">Main Featured Image</label>
                                        <input type="file" class="form-control mb-2 image-input" accept="image/*" name="main_featured_image" id="main_featured_image">
                                        @if ($store->merchant->main_featured_image)
                                            <img src="{{ URL::asset('/assets/img/stores/' . $store->merchant->id . '/' . $store->merchant->main_featured_image) }}" id="preview-main-featured-image" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                        @else
                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="preview-main-featured-image" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="brochure-field" class="form-label">Brochure <span class="text-warning">(Maximum of 2MB)</span></label>
                                        <input type="file" name="brochure" id="brochure-field" class="form-control" accept="application/pdf" onchange="previewPDF()">
                                        <div class="text-danger">@error('brochure'){{ $message }}@enderror</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Images <span class="text-warning h6">(MAXIMUM OF 2MB)</span></h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php $store_images = $store->images ? json_decode($store->images) : []; ?>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                @if (count($store_images) > 0 && isset($store_images[0]))
                                                    @fileExists('assets/img/stores/' . $store->merchant->id . '/' . $store_images[0])
                                                        <img src="{{ URL::asset('assets/img/stores/' . $store->merchant->id . '/' . $store_images[0]) }}"
                                                            id="previewImage1" alt="Default Image" width="100%" height="200px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @elsefileExists
                                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                            id="previewImage1" alt="Default Image" width="100%" height="210px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @endfileExists
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $store->id }}, '{{ $store_images[0] }}')">Remove
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
                                                @if (count($store_images) > 0 && isset($store_images[1]))
                                                    @fileExists('assets/img/stores/' . $store->merchant->id . '/' . $store_images[1])
                                                        <img src="{{ URL::asset('assets/img/stores/' . $store->merchant->id . '/' . $store_images[1]) }}"
                                                            id="previewImage2" alt="Default Image" width="100%" height="200px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @elsefileExists
                                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                            id="previewImage2" alt="Default Image" width="100%" height="210px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @endfileExists
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $store->id }}, '{{ $store_images[1] }}')">Remove
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
                                                @if (count($store_images) > 0 && isset($store_images[2]))
                                                    @fileExists('assets/img/stores/' . $store->merchant->id . '/' . $store_images[2])
                                                        <img src="{{ URL::asset('assets/img/stores/' . $store->merchant->id . '/' . $store_images[2]) }}"
                                                            id="previewImage3" alt="Default Image" width="100%" height="200px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @elsefileExists
                                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                            id="previewImage3" alt="Default Image" width="100%" height="210px"
                                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    @endfileExists
                                                    <button type="button"
                                                        style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                        class="btn btn-primary"
                                                        onclick="removeImageBtn({{ $store->id }}, '{{ $store_images[2] }}')">Remove
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
                            <button class="btn btn-primary">Save Store</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        @if ($store->merchant->featured_image)
                            @fileExists('assets/img/stores/' . $store->merchant->id . '/' . $store->merchant->featured_image)
                                <img src="{{ URL::asset('assets/img/stores/' . $store->merchant->id . '/' . $store->merchant->featured_image) }}"
                                    id="previewImage2" alt="Default Image" width="100%" height="200px"
                                    style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                            @elsefileExists
                                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                    id="previewImage2" alt="Default Image" width="100%" height="210px"
                                    style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                            @endfileExists
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt=""
                                style="border-radius: 10px;" width="100%" id="previewImage">
                        @endif
                    </div>
                </div>
                {{-- <div class="card mt-2">
                    <div class="card-body">
                        <h6>Preview of Brochure</h6>
                        @if($store->brochure) 
                            <iframe id="pdfPreview" width="100%" height="500px" src="{{ URL::asset('/assets/img/stores/' . $store->merchant->id . '/' . $store->brochure) }}" frameborder="0"></iframe>
                            <a target="_blank" href="{{ URL::asset('/assets/img/stores/' . $store->merchant->id . '/' . $store->brochure) }}">{{ URL::asset('/assets/img/stores/' . $store->merchant->id . '/' . $store->brochure) }}</a>
                        @else
                            <iframe id="pdfPreview" width="100%" height="500px" frameborder="0"></iframe>
                        @endif
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to handle file selection and display preview image
        function handleFeaturedImageSelect(event) {
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
                title: 'Remove Store Image?',
                text: "Are you sure you want to remove this image? You can't revert it.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('admin.merchants.stores.remove_image') }}`,
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
        
        function previewPDF() {
            var input = document.getElementById('brochure-field');
            var iframe = document.getElementById('pdfPreview');

            // Check if a file is selected
            if (input.files.length > 0) {
                var file = input.files[0];
                console.log(file.type);

                // Check if the selected file is a PDF
                if (file.type === 'application/pdf') {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        // Set the source of the iframe to the PDF data
                        iframe.src = e.target.result;
                    };

                    // Read the file as data URL
                    reader.readAsDataURL(file);
                } else {
                    alert('Please select a valid PDF file.');
                    // Clear the input field to allow selecting another file
                    input.value = '';
                }
            } else {
                // Clear the iframe source if no file is selected
                iframe.src = '';
            }
        }

        document.getElementById('isFeatured').addEventListener('change', handleIsFeaturedChange);
        document.getElementById('main_featured_image').addEventListener('change', handleMainFeaturedImageSelect);
        document.getElementById('featured_image').addEventListener('change', handleFeaturedImageSelect);

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
