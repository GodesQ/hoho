@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Organization')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Organization</h4>
        <a href="{{ route('admin.organizations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.organizations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="acronym" class="form-label">Acronym</label>
                                    <input type="text" class="form-control" name="acronym" id="acronym" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="region" class="form-label">Region</label>
                                    <input type="text" class="form-control" name="region" id="region" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <input type="file" class="form-control" name="icon" id="icon" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">Featured Image</label>
                                    <input type="file" class="form-control" name="featured_image" id="featured_image" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="visibility" class="form-label">Visibility</label>
                                    <select name="visibility" id="visibility" class="form-select">
                                        <option value="Default">Default</option>
                                        <option value="Coming Soon">Coming Soon</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" cols="30" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked />
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4>Images</h4>
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
                        <button class="btn btn-primary">Save Organization</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h6>Preview Featured Image</h6>
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image" id="previewImage" style="border-radius: 10px" width="100%">
                        </div>
                        <div class="col-lg-4">
                            <h6>Preview Icon</h6>
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image" id="previewIcon" style="border-radius: 10px" width="100%">
                        </div>
                    </div>
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

        function handleIconFileSelect(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewIcon = document.getElementById('previewIcon');
                    previewIcon.src = event.target.result;
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
        // Attach the 'handleIconFileSelect' function to the file input's change event
        document.getElementById('icon').addEventListener('change', handleIconFileSelect);
    </script>
@endpush
