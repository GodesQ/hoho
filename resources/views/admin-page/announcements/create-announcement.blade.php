@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Announcement')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Announcement</h4>
        <a href="{{ route('admin.announcements.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.announcements.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="announcement_image" class="form-label">Announcement Image</label>
                                    <input type="file" class="form-control" name="announcement_image" id="announcement_image">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="operation">Operation Announcement</option>
                                        <option value="safety">Safety Announcement</option>
                                        <option value="holiday_greeting">Holiday Greeting Announcement</option>
                                        <option value="subscription">News Letter Subscription Announcement</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Mesage</label>
                                    <textarea name="message" id="message" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked />
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isImportant" name="is_important" />
                                    <label class="form-check-label" for="isImportant">Is Important?</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-primary">Save Announcement</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6>Featured Image</h6>
                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage"
                            alt="Default Image" width="100%" style="border-radius: 10px;">
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
        document.getElementById('announcement_image').addEventListener('change', handleFileSelect);
    </script>
@endpush