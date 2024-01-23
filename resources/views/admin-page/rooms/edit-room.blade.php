@extends('layouts.admin.layout')

@section('title', 'Edit Room - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Room</h4>
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-primary">Back to List <i class="bx bx-undo"></i></a>
        </div>

        <form action="{{ route('admin.rooms.update', $room->id) }}" method="post" enctype="multipart/form-data">
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
                                            value="{{ $room->room_name }}">
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
                                                <option value="{{ $merchant->id }}"
                                                    {{ $room->merchant_id == $merchant->id ? 'selected' : null }}>
                                                    {{ $merchant->name }}</option>
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
                                            value="{{ $room->price }}">
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
                                        <input type="file" class="form-control" name="image" id="image">
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
                                            value="{{ $room->available_pax }}">
                                        <span class="text-danger danger">
                                            @error('available_pax')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <?php
                                    $room_product_categories = $room->product_categories ? json_decode($room->product_categories) : [];
                                    ?>
                                    <div class="mb-3">
                                        <label for="product_categories" class="form-label">Product Categories </label>
                                        <select name="product_categories[]" id="product_categories" class="select2" multiple>
                                            @foreach ($product_categories as $product_category)
                                                <option value="{{ $product_category->id }}"
                                                    {{ in_array($product_category->id, $room_product_categories) ? 'selected' : null }}>
                                                    {{ $product_category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span
                                                class="text-danger">*</span></label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $room->description }}</textarea>
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
                                        <textarea name="amenities" id="amenities" cols="30" rows="5" class="form-control">{{ $room->amenities }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isCancellable"
                                                name="is_cancellable" {{ $room->is_cancellable ? 'checked' : null }} />
                                            <label class="form-check-label" for="isCancellable">Is Cancellable</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive"
                                                name="is_active" {{ $room->is_active ? 'checked' : null }} />
                                            <label class="form-check-label" for="isActive">Is Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Images <span style="font-size: 14px;">( Max File Size: 2MB )</span></h4>
                            <?php $room_images = $room->other_images ? json_decode($room->other_images) : []; ?>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        @if (count($room_images) > 0 && isset($room_images[0]))
                                            @fileExists('assets/img/rooms/' . $room->id . '/' . $room_images[0])
                                                <img src="{{ URL::asset('assets/img/rooms/' . $room->id . '/' . $room_images[0]) }}"
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
                                                onclick="removeImageBtn({{ $room->id }}, '{{ $room_images[0] }}')">Remove
                                                <i class="bx bx-trash"></i></button>
                                        @else
                                            <input type="file" class="form-control mb-2 image-input" accept="image/*"
                                                name="other_images[]" id="image_1"
                                                onchange="handlePreviewImage(this, 'previewImage1')">
                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                id="previewImage1" alt="Default Image" width="100%" height="200px"
                                                style="border-radius: 10px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        @if (count($room_images) > 0 && isset($room_images[1]))
                                            @fileExists('assets/img/rooms/' . $room->id . '/' . $room_images[1])
                                                <img src="{{ URL::asset('assets/img/rooms/' . $room->id . '/' . $room_images[1]) }}"
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
                                                onclick="removeImageBtn({{ $room->id }}, '{{ $room_images[1] }}')">Remove
                                                <i class="bx bx-trash"></i></button>
                                        @else
                                            <input type="file" class="form-control mb-2 image-input" accept="image/*"
                                                name="other_images[]" id="image_2"
                                                onchange="handlePreviewImage(this, 'previewImage2')">
                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                id="previewImage2" alt="Default Image" width="100%" height="200px"
                                                style="border-radius: 10px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        @if (count($room_images) > 0 && isset($room_images[2]))
                                            @fileExists('assets/img/rooms/' . $room->id . '/' . $room_images[2])
                                                <img src="{{ URL::asset('assets/img/rooms/' . $room->id . '/' . $room_images[2]) }}"
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
                                                onclick="removeImageBtn({{ $room->id }}, '{{ $room_images[2] }}')">Remove
                                                <i class="bx bx-trash"></i></button>
                                        @else
                                            <input type="file" class="form-control mb-2 image-input" accept="image/*"
                                                name="other_images[]" id="image_3"
                                                onchange="handlePreviewImage(this, 'previewImage3')">
                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                id="previewImage3" alt="Default Image" width="100%" height="200px"
                                                style="border-radius: 10px; object-fit: cover;">
                                        @endif
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
                            @if ($room->image)
                                <img src="{{ URL::asset('assets/img/rooms/' . $room->id . '/' . $room->image) }}"
                                    alt="{{ $room->room_name }}" style="border-radius: 10px !important;" id="previewImage" width="100%">
                            @else
                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image"
                                style="border-radius: 10px !important;" id="previewImage" width="100%">
                            @endif
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

        let mainImageInput = document.querySelector('#image');
        mainImageInput.addEventListener('change', handleFileSelect);
    </script>
@endpush
