@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Organization')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Organization</h4>
        <a href="{{ route('admin.organizations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.organizations.update', $organization->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ $organization->name }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="acronym" class="form-label">Acronym</label>
                                    <input type="text" class="form-control" name="acronym" id="acronym" value="{{ $organization->acronym }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="region" class="form-label">Region</label>
                                    <input type="text" class="form-control" name="region" id="region" value="{{ $organization->region }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <input type="file" class="form-control" name="icon" id="icon" value="{{ $organization->icon }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">Featured Image</label>
                                    <input type="file" class="form-control" name="featured_image" id="featured_image" value="{{ $organization->featured_image }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="visibility" class="form-label">Visibility</label>
                                    <select name="visibility" id="visibility" class="form-select">
                                        <option {{ $organization->visibility == 'Default' ? 'selected' : null }} value="Default">Default</option>
                                        <option {{ $organization->visibility == 'Coming Soon' ? 'selected' : null }} value="Coming Soon">Coming Soon</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" cols="30" class="form-control" rows="5">{{ $organization->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" {{ $organization->is_active ? 'checked' : null }} />
                                    <label class="form-check-label" for="isActive">Active</label>
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
                            @if($organization->featured_image)
                                <img src="{{ URL::asset('assets/img/organizations/' . $organization->id . '/' . $organization->featured_image) }}" alt="{{ $organization->featured_image }}" id="previewImage" style="border-radius: 10px" width="100%">
                            @else
                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image" id="previewImage" style="border-radius: 10px" width="100%">
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <h6>Preview Icon</h6>
                            @if($organization->icon)
                                <img src="{{ URL::asset('assets/img/organizations/' . $organization->id . '/' . $organization->icon) }}" alt="{{ $organization->icon }}" id="previewImage" style="border-radius: 10px" width="100%">
                            @else
                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image" id="previewImage" style="border-radius: 10px" width="100%">
                            @endif
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

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('featured_image').addEventListener('change', handleFileSelect);
        // Attach the 'handleIconFileSelect' function to the file input's change event
        document.getElementById('icon').addEventListener('change', handleIconFileSelect);
    </script>
@endpush
