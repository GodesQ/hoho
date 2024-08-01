@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Add Merchants Restaurant')

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
            <h4 class="fw-bold py-3 mb-4">Add Merchant Restaurant</h4>
            <a href="{{ route('admin.merchants.restaurants.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
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
                        <form action="{{ route('admin.merchants.restaurants.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> Merchant Information</h5> <hr></div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Merchant Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="">
                                        <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Merchant Code</label>
                                        <input type="text" class="form-control" name="code" id="code" value="">
                                        <span class="text-danger">@error('code'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Merchant Type <span class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select">
                                            <option value="Restaurant">Restaurant</option>
                                            <option value="Hotel">Hotel</option>
                                            <option value="Store">Store</option>
                                            <option value="Tour Provider">Tour Provider</option>
                                        </select>
                                        <span class="text-danger">@error('type'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">
                                            Featured Image 
                                            <span class="text-danger">*</span> 
                                            <span class="text-warning">(Maximum of 2MB)</span>
                                        </label>
                                        <input type="file" class="form-control" name="featured_image" id="featured_image" value="" accept="image/*">
                                        <span class="text-danger">@error('featured_image'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nature_of_business" class="form-label">Nature of Business</label>
                                        <input type="text" class="form-control" name="nature_of_business" id="nature_of_business" value="">
                                        <span class="text-danger">@error('nature_of_business'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="organizations" class="form-label">Organizations</label>
                                        <select name="organization_id" id="organization_id" class="select2 form-select" >
                                            @foreach ($organizations as $organization)
                                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('organization_id'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" id="address">
                                        <span class="text-danger">@error('address'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="latitude" class="form-label">Latitude</label>
                                                <input type="text" class="form-control" name="latitude" id="latitude">
                                                <span class="text-danger">@error('latitude'){{ $message }}@enderror</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label action="longitude" class="form-label">longitude</label>
                                                <input type="text" class="form-control" name="longitude" id="longitude">
                                                <span class="text-danger">@error('longitude'){{ $message }}@enderror</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                                        <span class="text-danger">@error('description'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> Restaurant Information</h5> <hr></div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="interests" class="form-label">Interests</label>
                                        <select name="interests[]" id="interests" class="form-select select2" multiple>
                                            @foreach ($interests as $interest)
                                                <option value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('interests'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="payment_options" class="form-label">Payment Options</label>
                                        <input type="text" class="form-control" name="payment_options" id="payment_options" value="">
                                        <span class="text-danger">@error('payment_options'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" id="contact_number" class="form-control">
                                        <span class="text-danger">@error('contact_number'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="text" name="contact_email" id="contact_email" class="form-control">
                                        <span class="text-danger">@error('contact_email'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="business_hours" class="form-label">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control"></textarea>
                                        <span class="text-danger">@error('business_hours'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <textarea name="tags" id="tags" cols="30" rows="5" class="form-control"></textarea>
                                        <span class="text-danger">@error('tags'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" />
                                            <label class="form-check-label" for="isActive">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="isFeatured" name="is_featured" />
                                        <label class="form-check-label" for="isFeatured">Featured</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 main-featured-image-container">
                                    <div class="mb-3">
                                        <label for="main_featured_image" class="form-label">Main Featured Image</label>
                                        <input type="file" class="form-control mb-2 image-input" accept="image/*" name="main_featured_image" id="main_featured_image">
                                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="preview-main-featured-image" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="brochure-field" class="form-label">Brochure <span class="text-warning">(Maximum of 2MB)</span></label>
                                        <input type="file" name="brochure" id="brochure-field" class="form-control" accept="application/pdf" onchange="previewPDF()">
                                        <span class="text-danger">@error('brochure'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Images <span class="text-warning h6">(Maximum of 2MB)</span></h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_1" onchange="handlePreviewImage(this, 'previewImage1')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage1" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_2" onchange="handlePreviewImage(this, 'previewImage2')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage2" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_3" onchange="handlePreviewImage(this, 'previewImage3')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage3" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Restaurant</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="" id="previewImage" style="border-radius: 10px;" width="100%">
                    </div>
                </div>
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
        document.getElementById('featured_image').addEventListener('change', handleFeaturedImageSelect);
        document.getElementById('main_featured_image').addEventListener('change', handleMainFeaturedImageSelect);
    </script>
@endpush
