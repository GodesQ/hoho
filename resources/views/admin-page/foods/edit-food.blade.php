@extends('layouts.admin.layout')

@section('title', 'Edit Food - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Food</h4>
            <a href="{{ route('admin.foods.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <form action="{{ route('admin.foods.update', $food->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ $food->title }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="merchant_id" class="form-label">Merchant</label>
                                        <select name="merchant_id" id="merchant_id" class="select2">
                                            @foreach ($merchants as $merchant)
                                                <option value="{{ $merchant->id }}"
                                                    {{ $merchant->id == $food->merchant_id ? 'selected' : null }}>
                                                    {{ $merchant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image <span class="text-danger">*</span>
                                            <span>(Max File Size: 2MB)</span></label>
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
                                        <label for="price" class="form-label">Price</label>
                                        <input type="int" class="form-control" name="price" id="price"
                                            value="{{ $food->price }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="food_category_id" class="form-label">Food Category</label>
                                        <select name="food_category_id" id="food_category_id" class="select2">
                                            <option value="">-- SELECT FOOD CATEGORY --</option>
                                            @foreach ($foodCategories as $foodCategory)
                                                <option value="{{ $foodCategory->id }}"
                                                    {{ $foodCategory->id == $food->food_category_id ? 'selected' : null }}>
                                                    {{ $foodCategory->title }} ({{ $foodCategory->merchant->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $food->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Note</label>
                                        <input type="text" class="form-control" name="note" id="note"
                                            value="{{ $food->note }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active"
                                                {{ $food->is_active ? 'checked' : null }} />
                                            <label class="form-check-label" for="isActive">Is Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Images <span style="font-size: 14px;">( Max File Size: 2MB )</span></h4>
                            <?php $food_images = $food->other_images ? json_decode($food->other_images) : []; ?>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        @if (count($food_images) > 0 && isset($food_images[0]))
                                            @fileExists('assets/img/foods/' . $food->id . '/' . $food_images[0])
                                                <img src="{{ URL::asset('assets/img/foods/' . $food->id . '/' . $food_images[0]) }}"
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
                                                onclick="removeImageBtn({{ $food->id }}, '{{ $food_images[0] }}')">Remove
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
                                        @if (count($food_images) > 0 && isset($food_images[1]))
                                            @fileExists('assets/img/foods/' . $food->id . '/' . $food_images[1])
                                                <img src="{{ URL::asset('assets/img/foods/' . $food->id . '/' . $food_images[1]) }}"
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
                                                onclick="removeImageBtn({{ $food->id }}, '{{ $food_images[1] }}')">Remove
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
                                        @if (count($food_images) > 0 && isset($food_images[2]))
                                            @fileExists('assets/img/foods/' . $food->id . '/' . $food_images[2])
                                                <img src="{{ URL::asset('assets/img/foods/' . $food->id . '/' . $food_images[2]) }}"
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
                                                onclick="removeImageBtn({{ $food->id }}, '{{ $food_images[2] }}')">Remove
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
                            <button class="btn btn-primary">Save Food</button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h6>Preview of Main Image</h6>
                            @if ($food->image)
                                <img src="{{ URL::asset('assets/img/foods/' . $food->id . '/' . $food->image) }}"
                                    alt="{{ $food->title }}" style="border-radius: 10px !important;" id="previewImage"
                                    width="100%">
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

            function removeImageBtn(id, image_path) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Remove food image",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('admin.foods.remove_image') }}`,
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

            let mainImageInput = document.querySelector('#image');
            mainImageInput.addEventListener('change', handleFileSelect);
        </script>
    @endpush
@endsection
