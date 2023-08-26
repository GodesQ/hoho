@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Attraction')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Attraction</h4>
        <a href="{{ route('admin.attractions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.attractions.update', $attraction->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Attraction Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $attraction->name }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="tour_provider" class="form-label">Tour Provider</label>
                            <select name="tour_provider_id" id="tour_provider" class="form-select">
                                {{-- <option value="">HoHo Manila</option>
                            <option value=""> Owllah Travel & Tours</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" name="featured_image"
                                id="featured_image">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="interests" class="form-label">Interests</label>
                            <select name="interests[]" id="interests" multiple class="select2 form-select">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="product_categories" class="form-label">Product Categories</label>
                            <select name="product_categories[]" id="product_categories" multiple class="select2 form-select">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" name="price"
                                id="price" value="{{ $attraction->price }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="contact_no" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_no"
                                id="contact_no" value="{{ $attraction->contact_no }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="youtube_id" class="form-label">Youtube Id</label>
                            <input type="text" class="form-control" name="youtube_id"
                                id="youtube_id" value="{{ $attraction->youtube_id }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" id="address" value="{{ $attraction->address }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label action="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" name="latitude" id="latitude" value="{{ $attraction->latitude }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label action="longitude" class="form-label">longitude</label>
                                    <input type="text" class="form-control" name="longitude" id="longitude" value="{{ $attraction->longitude }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $attraction->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="operating_hours" class="form-label">Operating Hours</label>
                            <textarea name="operating_hours" id="operating_hours" cols="30" rows="5" class="form-control">{{ $attraction->operating_hours }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="organization_id" class="form-label">Organizations </label>
                            <select name="organization_id" id="organization_id" class="select2 form-select">
                                <option value="">--- SELECT ORGANIZATION ---</option>
                                @foreach ($organizations as $organization)
                                    <option {{ $organization->id == $attraction->organization_id ? 'selected' : null }} value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isCancellable" name="is_cancellable" {{ $attraction->is_cancellable ? 'checked' : null }} />
                                    <label class="form-check-label" for="isCancellable">Cancellable</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isRefundable" name="is_refundable" {{ $attraction->is_refundable ? 'checked' : null }} />
                                    <label class="form-check-label" for="isRefundable">Refundable</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" {{ $attraction->status ? 'checked' : null }} />
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <button class="btn btn-primary">Save Attraction</button>
            </form>
        </div>
    </div>
</div>
@endsection
