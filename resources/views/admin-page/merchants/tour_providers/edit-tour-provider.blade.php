@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Tour Provider')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Tour Provider</h4>
            <a href="{{ route('admin.merchants.tour_providers.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.merchants.tour_providers.update', $tour_provider->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> Merchant Information</h5> <hr></div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Merchant Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $tour_provider->merchant->name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Merchant Code</label>
                                        <input type="text" class="form-control" name="code" id="code" value="{{ $tour_provider->merchant->code }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Merchant Type <span class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option {{ $tour_provider->merchant->type == 'tour_provider' ? 'selected' : null }} value="tour_provider">tour_provider</option>
                                            <option {{ $tour_provider->merchant->type == 'Hotel' ? 'selected' : null }} value="Hotel">Hotel</option>
                                            <option {{ $tour_provider->merchant->type == 'Store' ? 'selected' : null }} value="Store">Store</option>
                                            <option {{ $tour_provider->merchant->type == 'Tour Provider' ? 'selected' : null }} value="Tour Provider">Tour Provider</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image</label>
                                        <input type="file" class="form-control" name="featured_image" id="featured_image" value="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nature_of_business" class="form-label">Nature of Business</label>
                                        <input type="text" class="form-control" name="nature_of_business" id="nature_of_business" value="{{ $tour_provider->merchant->nature_of_business }}">
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
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $tour_provider->merchant->description }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> tour_provider Information</h5> <hr></div>
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
                                        <input type="text" class="form-control" name="payment_options" id="payment_options" value="{{ $tour_provider->payment_options }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="business_hours" class="form-label">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $tour_provider->business_hours }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $tour_provider->tags }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="text" name="contact_email" id="contact_email" class="form-control" value="{{ $tour_provider->contact_email }}">
                                    </div>
                                </div>
                                {{-- <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Images</label>
                                        <input type="file" name="images[]" id="images_1" class="form-control">
                                        <input type="file" name="images[]" id="images_2" class="form-control">
                                        <input type="file" name="images[]" id="images_3" class="form-control">
                                    </div>
                                </div> --}}
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Tour Provider</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        @if($tour_provider->merchant->featured_image)
                            <img src="{{ URL::asset('/assets/img/tour_providers/' . $tour_provider->merchant->id . '/' . $tour_provider->merchant->featured_image) }}" alt="" style="border-radius: 10px;" width="100%">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="" style="border-radius: 10px;" width="100%">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
