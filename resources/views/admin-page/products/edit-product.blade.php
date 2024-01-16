@extends('layouts.admin.layout')

@section('title', 'Edit Product - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Product</h4>
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Back to List <i class="bx bx-undo"></i></a>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ $product->name }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="merchant_id" class="form-label">Merchant</label>
                                        <select name="merchant_id" id="merchant_id" class="select2">
                                            @foreach ($merchants as $merchant)
                                                <option value="{{ $merchant->id }}"
                                                    {{ $product->merchant_id == $merchant->id ? 'selected' : null }}>
                                                    {{ $merchant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="text" class="form-control" name="price" id="price"
                                            value="{{ $product->price }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image</label>
                                        <input type="file" class="form-control" name="image" id="image">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" class="form-control"
                                            value="{{ $product->quantity }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $product->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active"
                                            {{ $product->is_active ? 'checked' : null }} />
                                        <label class="form-check-label" for="isActive">Is Active</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Product</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6>Preview of Main Image</h6>
                            @if ($product->image)
                                <img src="{{ URL::asset('assets/img/products/' . $product->id . '/' . $product->image) }}"
                                    alt="{{ $product->name }}" style="border-radius: 10px !important;" id="previewImage" width="100%">
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