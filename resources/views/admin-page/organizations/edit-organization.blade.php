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
                        <h4>Images</h4>
                        <div class="row">
                            <div class="col-lg-12">
                                <?php $organization_images = $organization->images ? json_decode($organization->images) : [] ?>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            @if(count($organization_images) > 0 && isset($organization_images[0]))
                                                <img src="{{ URL::asset('assets/img/organizations/' . $organization->id . '/' . $organization_images[0]) }}" id="previewImage1" alt="Default Image" width="100%" height="200px" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                <button type="button" style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;" class="btn btn-primary" onclick="removeImageBtn({{ $organization->id}}, '{{ $organization_images[0] }}')">Remove <i class="bx bx-trash"></i></button>
                                            @else
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_1" onchange="handlePreviewImage(this, 'previewImage1')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage1" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            @if(count($organization_images) > 0 && isset($organization_images[1]))
                                                <img src="{{ URL::asset('assets/img/organizations/' . $organization->id . '/' . $organization_images[1]) }}" id="previewImage2" alt="Default Image" width="100%" height="200px" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                <button type="button" style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;" class="btn btn-primary" onclick="removeImageBtn({{ $organization->id}}, '{{ $organization_images[1] }}')">Remove <i class="bx bx-trash"></i></button>
                                            @else
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_2" onchange="handlePreviewImage(this, 'previewImage2')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage2" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            @if(count($organization_images) > 0 && isset($organization_images[2]))
                                                <img src="{{ URL::asset('assets/img/organizations/' . $organization->id . '/' . $organization_images[2]) }}" id="previewImage3" alt="Default Image" width="100%" height="200px" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                                <button type="button" style="display: block; width: 100%; border-radius: 0px 0px 20px 20px;" class="btn btn-primary" onclick="removeImageBtn({{ $organization->id}}, '{{ $organization_images[2] }}')">Remove <i class="bx bx-trash"></i></button>
                                            @else
                                                <input type="file" class="form-control mb-2 image-input" accept="image/*" name="images[]" id="image_3" onchange="handlePreviewImage(this, 'previewImage3')">
                                                <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage3" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                                            @endif
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

        function removeImageBtn(id, image_file_name) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove attraction image",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('admin.organizations.remove_image') }}`,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            image_file_name: image_file_name
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
                            } else {
                                Swal.fire('Failed!', response.message, 'error').then(
                                    result => {
                                        if (result.isConfirmed) {
                                            toastr.error(response.message, 'Failed');
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
        // Attach the 'handleIconFileSelect' function to the file input's change event
        document.getElementById('icon').addEventListener('change', handleIconFileSelect);
    </script>
@endpush
