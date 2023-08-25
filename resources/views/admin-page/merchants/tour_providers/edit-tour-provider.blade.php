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
                        <form action="{{ route('admin.merchants.tour_providers.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> Merchant Information</h5> <hr></div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Merchant Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $restaurant->merchant->name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Merchant Code</label>
                                        <input type="text" class="form-control" name="code" id="code" value="{{ $restaurant->merchant->code }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Merchant Type <span class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option {{ $restaurant->merchant->type == 'Restaurant' ? 'selected' : null }} value="Restaurant">Restaurant</option>
                                            <option {{ $restaurant->merchant->type == 'Hotel' ? 'selected' : null }} value="Hotel">Hotel</option>
                                            <option {{ $restaurant->merchant->type == 'Store' ? 'selected' : null }} value="Store">Store</option>
                                            <option {{ $restaurant->merchant->type == 'Tour Provider' ? 'selected' : null }} value="Tour Provider">Tour Provider</option>
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
                                        <input type="text" class="form-control" name="nature_of_business" id="nature_of_business" value="{{ $restaurant->merchant->nature_of_business }}">
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
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $restaurant->merchant->description }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12"><hr> <h5><i class="bx bx-box"></i> restaurant Information</h5> <hr></div>
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
                                        <input type="text" class="form-control" name="payment_options" id="payment_options" value="{{ $restaurant->payment_options }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="business_hours" class="form-label">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control">{{ $restaurant->business_hours }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <textarea name="tags" id="tags" cols="30" rows="5" class="form-control">{{ $restaurant->tags }}</textarea>
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
                            <button class="btn btn-primary">Save restaurant</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview of Featured Image</h6>
                        @if($restaurant->merchant->featured_image)
                            <img src="{{ URL::asset('/assets/img/tour_providers/' . $restaurant->merchant->id . '/' . $restaurant->merchant->featured_image) }}" alt="" style="border-radius: 10px;" width="100%">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="" style="border-radius: 10px;" width="100%">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
