@extends('layouts.admin.layout')

@section('title', 'Merchant Form')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <form enctype="multipart/form-data"
                    action="{{ optional($admin->merchant)->tour_provider_info ? route('admin.merchants.tour_providers.update', optional($admin->merchant->tour_provider_info)->id) : route('admin.merchants.tour_providers.store') }}"
                    method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            {{-- Merchant Information --}}
                            <div class="row">
                                <div class="col-lg-12">
                                    <hr>
                                    <h4>Merchant Information</h4>
                                    <hr>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Merchant Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" id="name" value="{{ $admin->merchant->name ?? null }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="code" class="form-label">Merchant Code</label>
                                                <input type="text" class="form-control" name="code" id="code" value="{{ $admin->merchant->code ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Merchant Type <span class="text-danger">*</span></label>
                                                <select name="type" id="type" class="form-select" required>
                                                    <option value="Tour Provider">Tour Provider</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="featured_image" class="form-label">Featured Image</label>
                                                <input type="file" class="form-control" name="featured_image" id="featured_image" value="" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="nature_of_business" class="form-label">Nature of Business</label>
                                                <input type="text" class="form-control" name="nature_of_business" id="nature_of_business" value="{{ $admin->merchant->nature_of_business ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="organizations" class="form-label">Organizations</label>
                                                <select name="organizations[]" id="organizations" class="select2 form-select" multiple>
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $admin->merchant->description ?? null }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <hr>
                                            <h4> Tour Provider Information</h4>
                                            <hr>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="interests" class="form-label">Interests</label>
                                                <select name="interests[]" id="interests" class="form-select select2" multiple>
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="payment_options" class="form-label">Payment Options</label>
                                                <input type="text" class="form-control" name="payment_options" id="payment_options" value="{{ $admin->merchant->tour_provider_info->payment_options ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="business_hours" class="form-label">Business Hours</label>
                                                <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $admin->merchant->tour_provider_info->business_hours ?? null }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="tags" class="form-label">Tags</label>
                                                <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $admin->merchant->tour_provider_info->tags ?? null }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="Contact Email" class="form-label">Contact Email</label>
                                                <input type="text" name="contact_email" id="contact_email" class="form-control" value="{{ $admin->merchant->tour_provider_info->contact_email ?? null }}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Preview of Featured Image</h6>
                                    @if (optional($admin->merchant)->tour_provider_info && $admin->merchant->featured_image)
                                        <img src="{{ URL::asset('/assets/img/tour_providers/' . $admin->merchant->id . '/' . $admin->merchant->featured_image) }}"
                                            alt="" style="border-radius: 10px;" width="100%" id="previewImage">
                                    @else
                                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt=""
                                            style="border-radius: 10px;" width="100%" id="previewImage">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Merchant Tour Provider</button>
                </form>
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
    </script>
@endpush
