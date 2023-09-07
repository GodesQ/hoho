@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Restaurant')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Restaurant</h4>
            <a href="{{ route('admin.merchants.restaurants.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.merchants.restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> Merchant Information</h5> <hr></div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Merchant Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $restaurant->merchant->name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Merchant Code</label>
                                        <input type="text" class="form-control" name="code" id="code" value="{{ $restaurant->merchant->code }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Merchant Type <span class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option {{ $restaurant->merchant->type == 'Restaurant' ? 'selected' : null }} value="Restaurant">Restaurant</option>
                                            <option {{ $restaurant->merchant->type == 'Hotel' ? 'selected' : null }} value="Hotel">Hotel</option>
                                            <option {{ $restaurant->merchant->type == 'Store' ? 'selected' : null }} value="Store">Store</option>
                                            <option {{ $restaurant->merchant->type == 'Tour Provider' ? 'selected' : null }} value="Tour Provider">Tour Provider</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image</label>
                                        <input type="file" class="form-control" name="featured_image" id="featured_image" value="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nature_of_business" class="form-label">Nature of Business</label>
                                        <input type="text" class="form-control" name="nature_of_business" id="nature_of_business" value="{{ $restaurant->merchant->nature_of_business }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="organizations" class="form-label">Organizations</label>
                                        <select name="organization_id" id="organization_id" class="select2 form-select">
                                            <option value="">-- SELECT ORGANIZATION --- </option>
                                            @foreach ($organizations as $organization)
                                                <option {{ $organization->id == $restaurant->merchant->organization_id ? 'selected' : null }} value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $restaurant->merchant->description }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> Restaurant Information</h5> <hr></div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="interests" class="form-label">Interests</label>
                                        <select name="interests[]" id="interests" class="form-select select2" multiple>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="payment_options" class="form-label">Payment Options</label>
                                        <input type="text" class="form-control" name="payment_options" id="payment_options" value="{{ $restaurant->payment_options }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="business_hours" class="form-label">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $restaurant->business_hours }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $restaurant->tags }}</textarea>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Images</label>
                                        <input type="file" name="images[]" id="images_1" class="form-control">
                                        <input type="file" name="images[]" id="images_2" class="form-control">
                                        <input type="file" name="images[]" id="images_3" class="form-control">
                                    </div>
                                </div> --}}
                            </div>
                            <hr>
                            <h4>Images</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php $restaurant_images = $restaurant->images ? json_decode($restaurant->images) : [] ?>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_1" onchange="handlePreviewImage(this, 'previewImage1')">
                                                @if(count($restaurant_images) > 0 && isset($restaurant_images[0]))
                                                    <img src="{{ URL::asset('assets/img/restaurants/' . $restaurant->merchant->id . '/' . $restaurant_images[0]) }}" id="previewImage1" alt="Default Image" width="100%" height="200px" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    <button type="button" style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;" class="btn btn-primary" onclick="removeImageBtn({{ $restaurant->id}}, '{{ $restaurant_images[0] }}')">Remove <i class="bx bx-trash"></i></button>
                                                @else
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage1" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_2" onchange="handlePreviewImage(this, 'previewImage2')">
                                                @if(count($restaurant_images) > 0 && isset($restaurant_images[1]))
                                                    <img src="{{ URL::asset('assets/img/restaurants/' . $restaurant->merchant->id . '/' . $restaurant_images[1]) }}" id="previewImage2" alt="Default Image" width="100%" height="200px" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    <button type="button" style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;" class="btn btn-primary" onclick="removeImageBtn({{ $restaurant->id}}, '{{ $restaurant_images[1] }}')">Remove <i class="bx bx-trash"></i></button>
                                                @else
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage2" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_3" onchange="handlePreviewImage(this, 'previewImage3')">
                                                @if(count($restaurant_images) > 0 && isset($restaurant_images[2]))
                                                    <img src="{{ URL::asset('assets/img/restaurants/' . $restaurant->merchant->id . '/' . $restaurant_images[2]) }}" id="previewImage3" alt="Default Image" width="100%" height="200px" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                    <button type="button" style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;" class="btn btn-primary" onclick="removeImageBtn({{ $restaurant->id}}, '{{ $restaurant_images[2] }}')">Remove <i class="bx bx-trash"></i></button>
                                                @else
                                                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage3" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save restaurant</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        @if($restaurant->merchant->featured_image)
                            <img src="{{ URL::asset('/assets/img/restaurants/' . $restaurant->merchant->id . '/' . $restaurant->merchant->featured_image) }}" alt="" style="border-radius: 10px;" width="100%">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="" style="border-radius: 10px;" width="100%">
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
                text: "Remove store image",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('admin.merchants.restaurants.remove_image') }}`,
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
