@extends('layouts.admin.layout')

@section('title', 'Merchant Form')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                @if (Session::get('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif
                <form enctype="multipart/form-data"
                    action="{{ $admin->merchant ? route('admin.merchants.hotels.update', $admin->merchant->hotel_info->id ?? null) : route('admin.merchants.hotels.store') }}"
                    method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
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
                                                    value="{{ $admin->merchant->name ?? null }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="code" class="form-label">Merchant Code</label>
                                                <input type="text" class="form-control" name="code" id="code"
                                                    value="{{ $admin->merchant->code ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Merchant Type <span
                                                        class="text-danger">*</span></label>
                                                <select name="type" id="type" class="form-select" required>
                                                    <option value="Hotel">Hotel</option>
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
                                                    id="nature_of_business" value="{{ $admin->merchant->nature_of_business ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="organizations" class="form-label">Organizations</label>
                                                <select name="organization_id" id="organization_id"
                                                    class="select2 form-select">
                                                    @foreach ($organizations as $organization)
                                                        <option value="{{ $organization->id }}">{{ $organization->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $admin->merchant->description ?? null }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <hr>
                                    <h4> Hotel Information</h4>
                                    <hr>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
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
                                                <label for="payment_options" class="form-label">Payment Options</label>
                                                <input type="text" class="form-control" name="payment_options"
                                                    id="payment_options" value="{{ $admin->merchant->hotel_info->payment_options ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="business_hours" class="form-label">Business Hours</label>
                                                <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $admin->merchant->hotel_info->business_hours ?? null }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="tags" class="form-label">Tags</label>
                                                <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $admin->merchant->hotel_info->tags ?? null }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <hr>
                                    <h4>Images</h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?php $hotel_images = ($admin->merchant->hotel_info->images ?? false) ? json_decode($admin->merchant->hotel_info->images) : []; ?>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        @if (($admin->merchant->hotel_info ?? false) && count($hotel_images) > 0 && isset($hotel_images[0]))

                                                            @fileExists('assets/img/hotels/' . $admin->merchant->id . '/' . $hotel_images[0])
                                                                <img src="{{ URL::asset('assets/img/hotels/' . $admin->merchant->id . '/' . $hotel_images[0]) }}"
                                                                    id="previewImage1" alt="Default Image" width="100%"
                                                                    height="200px"
                                                                    style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            @elsefileExists
                                                                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                                id="previewImage1" alt="Default Image" width="100%" height="210px"
                                                                style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            @endfileExists

                                                            <button type="button"
                                                                style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                                class="btn btn-primary"
                                                                onclick="removeImageBtn({{ $admin->merchant->hotel_info->id }}, '{{ $hotel_images[0] }}')">Remove
                                                                <i class="bx bx-trash"></i></button>
                                                        @else
                                                            <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_1"
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
                                                        @if (($admin->merchant->hotel_info ?? false) && count($hotel_images) > 0 && isset($hotel_images[1]))

                                                            @fileExists('assets/img/hotels/' . $admin->merchant->id . '/' . $hotel_images[1])
                                                                <img src="{{ URL::asset('assets/img/hotels/' . $admin->merchant->id . '/' . $hotel_images[1]) }}"
                                                                    id="previewImage2" alt="Default Image" width="100%"
                                                                    height="200px"
                                                                    style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            @elsefileExists
                                                                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                                id="previewImage1" alt="Default Image" width="100%" height="210px"
                                                                style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            @endfileExists

                                                            <button type="button"
                                                                style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                                class="btn btn-primary"
                                                                onclick="removeImageBtn({{ $admin->merchant->hotel_info->id }}, '{{ $hotel_images[1] }}')">Remove
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
                                                        @if (($admin->merchant->hotel_info ?? false) && count($hotel_images) > 0 && isset($hotel_images[2]))

                                                            @fileExists('assets/img/hotels/' . $admin->merchant->id . '/' . $hotel_images[2])
                                                                <img src="{{ URL::asset('assets/img/hotels/' . $admin->merchant->id . '/' . $hotel_images[2]) }}"
                                                                    id="previewImage3" alt="Default Image" width="100%"
                                                                    height="200px"
                                                                    style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            @elsefileExists
                                                                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                                id="previewImage1" alt="Default Image" width="100%" height="210px"
                                                                style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                            @endfileExists

                                                            <button type="button"
                                                                style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;"
                                                                class="btn btn-primary"
                                                                onclick="removeImageBtn({{ $admin->merchant->hotel_info->id }}, '{{ $hotel_images[2] }}')">Remove
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
                                    @if($admin->merchant && $admin->merchant->featured_image)
                                        @fileExists('/assets/img/hotels/' . $admin->merchant->id . '/' . $admin->merchant->featured_image)
                                            <img src="{{ URL::asset('/assets/img/hotels/' . $admin->merchant->id . '/' . $admin->merchant->featured_image) }}"
                                            alt="" style="border-radius: 10px;" width="100%" id="previewImage">
                                        @elsefileExists
                                            <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                            id="previewImage1" alt="Default Image" width="100%"
                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                        @endfileExists
                                    @else
                                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="" id="previewImage" style="border-radius: 10px;" width="100%">
                                    @endif
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

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('featured_image').addEventListener('change', handleFileSelect);
    </script>
@endpush