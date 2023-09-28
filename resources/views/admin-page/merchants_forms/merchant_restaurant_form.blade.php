@extends('layouts.admin.layout')

@section('title', 'Merchant Form')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <form enctype="multipart/form-data"
                    action="{{ $admin->merchant_restaurant ? route('admin.merchants.restaurants.update', optional($admin->merchant_restaurant)->id) : route('admin.merchants.restaurants.store') }}"
                    method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            {{-- Merchant Information --}}
                            <div class="row">
                                <div class="col-lg-12">
                                    <hr>
                                    <h4>Merchant Information</h4>
                                    <hr>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Merchant Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" id="name"
                                                    value="{{ $admin->merchant_restaurant->merchant->name ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="code" class="form-label">Merchant Code</label>
                                                <input type="text" class="form-control" name="code" id="code"
                                                    value="{{ $admin->merchant_restaurant->merchant->code ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Merchant Type <span
                                                        class="text-danger">*</span></label>
                                                <select name="type" id="type" class="form-select" required>
                                                    <option value="Restaurant">Restaurant</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="featured_image" class="form-label">Featured Image</label>
                                                <input type="file" class="form-control" name="featured_image"
                                                    id="featured_image" value="" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="nature_of_business" class="form-label">Nature of
                                                    Business</label>
                                                <input type="text" class="form-control" name="nature_of_business"
                                                    id="nature_of_business"
                                                    value="{{ $admin->merchant_restaurant->merchant->nature_of_business ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="organizations" class="form-label">Organizations</label>
                                                <select name="organization_id" id="organization_id"
                                                    class="select2 form-select">
                                                    @foreach ($organizations as $organization)
                                                        <option value="{{ $organization->id }}"
                                                            {{ ($admin->merchant_restaurant->merchant->organization_id ?? null) == $organization->id ? 'selected' : null }}>
                                                            {{ $organization->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" name="address" id="address"
                                                    value="{{ $admin->merchant_restaurant->merchant->address ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label action="latitude" class="form-label">Latitude</label>
                                                        <input type="text" class="form-control" name="latitude"
                                                            id="latitude"
                                                            value="{{ $admin->merchant_restaurant->merchant->latitude ?? null }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label action="longitude" class="form-label">longitude</label>
                                                        <input type="text" class="form-control" name="longitude"
                                                            id="longitude"
                                                            value="{{ $admin->merchant_restaurant->merchant->longitude ?? null }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $admin->merchant_restaurant->merchant->description ?? null }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <hr>
                                            <h4> Restaurant Information</h4>
                                            <hr>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="interests" class="form-label">Interests</label>
                                                <select name="interests[]" id="interests" class="form-select select2"
                                                    multiple>
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="payment_options" class="form-label">Payment
                                                    Options</label>
                                                <input type="text" class="form-control" name="payment_options"
                                                    id="payment_options"
                                                    value="{{ $admin->merchant_restaurant->payment_options ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="contact_number" class="form-label">Contact Number</label>
                                                <input type="text" name="contact_number" id="contact_number"
                                                    class="form-control"
                                                    value="{{ $admin->merchant_restaurant->contact_number ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="contact_email" class="form-label">Contact Email</label>
                                                <input type="text" name="contact_email" id="contact_email"
                                                    class="form-control"
                                                    value="{{ $admin->merchant_restaurant->contact_email ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="business_hours" class="form-label">Business Hours</label>
                                                <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $admin->merchant_restaurant->business_hours ?? null }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="tags" class="form-label">Tags</label>
                                                <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $admin->merchant_restaurant->tags ?? null }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h4>Images</h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?php $restaurant_images = ($admin->merchant_restaurant->images ?? false) ? json_decode($admin->merchant_restaurant->images) : []; ?>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        @if ($admin->merchant_restaurant && count($restaurant_images) > 0 && isset($restaurant_images[0]))
                                                            <img src="{{ URL::asset('assets/img/restaurants/' . $admin->merchant_restaurant->merchant->id . '/' . $restaurant_images[0]) }}"
                                                                id="previewImage1" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            <button type="button"
                                                                style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                                class="btn btn-primary"
                                                                onclick="removeImageBtn({{ $admin->merchant_restaurant->id }}, '{{ $restaurant_images[0] }}')">Remove
                                                                <i class="bx bx-trash"></i></button>
                                                        @else
                                                            <input type="file" class="form-control mb-2 image-input"
                                                                accept="image/*" name="images[]" id="image_1"
                                                                onchange="handlePreviewImage(this, 'previewImage1')">
                                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                                id="previewImage1" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px; object-fit: cover;">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        @if ($admin->merchant_restaurant && count($restaurant_images) > 0 && isset($restaurant_images[1]))
                                                            <img src="{{ URL::asset('assets/img/restaurants/' . $admin->merchant_restaurant->merchant->id . '/' . $restaurant_images[1]) }}"
                                                                id="previewImage2" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            <button type="button"
                                                                style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                                class="btn btn-primary"
                                                                onclick="removeImageBtn({{ $admin->merchant_restaurant->id }}, '{{ $restaurant_images[1] }}')">Remove
                                                                <i class="bx bx-trash"></i></button>
                                                        @else
                                                            <input type="file" class="form-control mb-2 image-input"
                                                                accept="image/*" name="images[]" id="image_2"
                                                                onchange="handlePreviewImage(this, 'previewImage2')">
                                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                                id="previewImage2" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px; object-fit: cover;">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        @if ($admin->merchant_restaurant && count($restaurant_images) > 0 && isset($restaurant_images[2]))
                                                            <img src="{{ URL::asset('assets/img/restaurants/' . $admin->merchant_restaurant->merchant->id . '/' . $restaurant_images[2]) }}"
                                                                id="previewImage3" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            <button type="button"
                                                                style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                                class="btn btn-primary"
                                                                onclick="removeImageBtn({{ $admin->merchant_restaurant->id }}, '{{ $restaurant_images[2] }}')">Remove
                                                                <i class="bx bx-trash"></i></button>
                                                        @else
                                                            <input type="file" class="form-control mb-2 image-input"
                                                                accept="image/*" name="images[]" id="image_3"
                                                                onchange="handlePreviewImage(this, 'previewImage3')">
                                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                                id="previewImage3" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px; object-fit: cover;">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Preview of Featured Image</h6>
                                    @if ($admin->merchant_restaurant && $admin->merchant_restaurant->merchant->featured_image)
                                        <img src="{{ URL::asset('/assets/img/restaurants/' . $admin->merchant_restaurant->merchant->id . '/' . $admin->merchant_restaurant->merchant->featured_image) }}"
                                            alt="" style="border-radius: 10px;" width="100%" id="previewImage">
                                    @else
                                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt=""
                                            style="border-radius: 10px;" width="100%" id="previewImage">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Merchant Restaurant</button>
                </form>
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

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('featured_image').addEventListener('change', handleFileSelect);
    </script>
@endpush
