@extends('layouts.admin.layout')

@section('title', 'Add Food - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Food</h4>
            <a href="{{ route('admin.foods.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <form action="{{ route('admin.foods.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ old('title') }}">
                                        <span>
                                            @error('title')
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
                                            <option value="">--- SELECT MERCHANT ---</option>
                                            @foreach ($merchants as $merchant)
                                                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                            @endforeach
                                        </select>
                                        <span>
                                            @error('merchant_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image <span class="text-danger">*</span>
                                            <span>Max File Size: 2MB</span></label>
                                        <input type="file" class="form-control" name="image" id="image"
                                            accept="image/*">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span
                                                class="text-danger">*</span></label>
                                        <input type="int" class="form-control" name="price" id="price"
                                            value="{{ old('price') }}">
                                        <span>
                                            @error('merchant_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="food_category_id" class="form-label">Food Category <span
                                                class="text-danger">*</span></label>
                                        <select name="food_category_id" id="food_category_id" class="food-category">
                                            {{-- <option value="">-- SELECT MERCHANT FIRST BEFORE FOOD CATEGORY --</option> --}}
                                        </select>
                                        <span>
                                            @error('merchant_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Note</label>
                                        <input type="text" class="form-control" name="note" id="note"
                                            value="{{ old('note') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active"
                                                checked />
                                            <label class="form-check-label" for="isActive">Is Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4><i class="bx bx-images"></i> Images <span style="font-size: 14px;">( Max File Size: 2MB
                                    )</span>
                            </h4>
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
                            <button class="btn btn-primary">Save Food</button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
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

    @push('scripts')
        <script>
            $('#merchant_id').on('change', function(e) {
                let merchant_id = e.target.value;
                foodCategorySelect2(merchant_id);
            })

            function foodCategorySelect2(merchant_id) {
                $('.food-category').select2({
                    width: '100%',
                    ajax: {
                        url: "{{ route('admin.food_categories.select') }}" + '/' + merchant_id,
                        processResults: function(data) {
                            data = data.map(d => {
                                return {
                                    id: d.id,
                                    text: d.text,
                                    selected: d.id == 3
                                }
                            });

                            return {
                                results: data
                            }
                        }
                    },
                });
            }

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
                        previewImage.src = event.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            }

            let mainImageInput = document.querySelector('#image');
            mainImageInput.addEventListener('change', handleFileSelect);


            foodCategorySelect2($('#merchant_id').val());
        </script>
    @endpush
@endsection
