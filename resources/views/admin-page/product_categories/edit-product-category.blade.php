@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Product Category')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Product Category</h4>
        <a href="{{ route('admin.product_categories.list') }}" class="btn btn-dark"> <i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.product_categories.update', $product_category->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ $product_category->name }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">Featured Image</label>
                                    <input type="file" name="featured_image" class="form-control" id="featured_image" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $product_category->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <?php $organization_ids = $product_category->organization_ids != null ? json_decode($product_category->organization_ids) : [] ?>
                                    <label for="organization_ids" class="form-label">Organizations</label>
                                    <select name="organization_ids[]" multiple id="organization_ids" class="select2 form-select">
                                        @foreach ($organizations as $organization)
                                            <option {{ in_array($organization->id, $organization_ids) ? 'selected' : null }} value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Save Product Category</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6>Preview of Featured Image</h6>
                    @if($product_category->featured_image)
                        <img src="{{ URL::asset('assets/img/product_categories/' . $product_category->featured_image) }}" id="previewImage" alt="{{ $product_category->name }}" width="100%" style="border-radius: 10px;">
                    @else
                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage" alt="Default Image" width="100%" style="border-radius: 10px;">
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

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('image').addEventListener('change', handleFileSelect);
    </script>
@endpush
