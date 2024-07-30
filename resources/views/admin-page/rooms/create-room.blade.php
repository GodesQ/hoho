@extends('layouts.admin.layout')

@section('title', 'Add Room - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Room</h4>
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-dark">Back to List <i class="bx bx-undo"></i></a>
        </div>

        <form action="{{ route('admin.rooms.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="room_name" class="form-label">Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="room_name" id="room_name"
                                            value="{{ old('room_name') }}">
                                        <span class="text-danger danger">
                                            @error('room_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="merchant_id" class="form-label">Merchant <span
                                                class="text-danger">*</span></label>
                                        <select name="merchant_id" id="merchant_id" class="select2">
                                            @foreach ($merchants as $merchant)
                                                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger danger">
                                            @error('merchant_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="price" id="price"
                                            value="{{ old('price') }}">
                                        <span class="text-danger danger">
                                            @error('price')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image <span
                                                class="text-danger">*</span> <span>(Max File Size: 2MB)</span></label>
                                        <input type="file" class="form-control" name="image" id="image" accept=image/*>
                                        <span class="text-danger danger">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="available_pax" class="form-label">Available Pax <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="available_pax" id="available_pax"
                                            value="{{ old('available_pax') }}">
                                        <span class="text-danger danger">
                                            @error('available_pax')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="product_categories" class="form-label">Product Categories </label>
                                        <select name="product_categories[]" id="product_categories" class="select2" multiple>
                                            @foreach ($product_categories as $product_category)
                                                <option value="{{ $product_category->id }}">{{ $product_category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span
                                                class="text-danger">*</span></label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ old('description') }}</textarea>
                                        <span class="text-danger danger">
                                            @error('description')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="amenities" class="form-label">Amenities</label>
                                        <textarea name="amenities" id="amenities" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isCancellable"
                                                name="is_cancellable" checked />
                                            <label class="form-check-label" for="isCancellable">Is Cancellable</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive"
                                                name="is_active" checked />
                                            <label class="form-check-label" for="isActive">Is Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4><i class="bx bx-images"></i> Images <span style="font-size: 14px;">( Max File Size: 2MB )</span></h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*"
                                                    name="other_images[]" id="image_1"
                                                    onchange="handlePreviewImage(this, 'previewImage1')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                    id="previewImage1" alt="Default Image" width="100%" height="200px"
                                                    style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="other_images[]" id="image_2"
                                                    onchange="handlePreviewImage(this, 'previewImage2')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                    id="previewImage2" alt="Default Image" width="100%" height="200px"
                                                    style="border-radius: 10px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="file" class="form-control mb-2 image-input"
                                                    accept="image/*" name="other_images[]" id="image_3"
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
                            <button class="btn btn-primary">Save Room</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6>Preview of Main Image</h6>
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image"
                                style="border-radius: 10px !important;" id="previewImage" width="100%">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
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

        let mainImageInput = document.querySelector('#image');
        mainImageInput.addEventListener('change', handleFileSelect);
    </script>
@endpush
