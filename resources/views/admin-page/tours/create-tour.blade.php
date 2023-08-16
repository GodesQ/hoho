@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Tour')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Create Tour</h4>
            <a href="{{ route('admin.tours.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.tours.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tour Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tour Type</label>
                                        <select name="type" id="type" class="form-select">
                                            <option value="">---- SELECT TOUR TYPE ----</option>
                                            <option value="Luxury">Luxury Tour</option>
                                            <option value="City">City Tour</option>
                                            <option value="Guided">Guided Tour</option>
                                            <option value="DIY">DIY Tour Tour</option>
                                            <option value="Others">Others</option>
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
                                        <label for="tour_provider" class="form-label">Tour Provider</label>
                                        <select name="tour_provider_id" id="tour_provider" class="form-select">
                                            {{-- <option value="">HoHo Manila</option>
                                        <option value=""> Owllah Travel & Tours</option> --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="under_age_limit" class="form-label">Under Age Limit</label>
                                                <input type="number" class="form-control" name="under_age_limit"
                                                    id="under_age_limit">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="over_age_limit" class="form-label">Over Age Limit</label>
                                                <input type="number" class="form-control" name="over_age_limit"
                                                    id="over_age_limit">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="minimum_capacity" class="form-label">Minimum Capacity</label>
                                            <input type="number" class="form-control" name="minimum_capacity"
                                                id="minimum_capacity">
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="capacity" class="form-label">Maximum Capacity</label>
                                            <input type="number" class="form-control" name="capacity" id="capacity">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="7" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="operating_hours" class="form-label">Operating Hours</label>
                                        <textarea name="operating_hours" id="operating_hours" cols="30" rows="7" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isCancellable" name="is_cancellable" />
                                                <label class="form-check-label" for="isCancellable">Is Cancellable</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="isRefundable" name="is_refundable" />
                                                <label class="form-check-label" for="isRefundable">Is Refundable</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check ">
                                                <input name="status" class="form-check-input" type="radio"
                                                    value="active" id="statusActive" checked />
                                                <label class="form-check-label" for="statusActive"> Active </label>
                                            </div>
                                            <div class="form-check">
                                                <input name="status" class="form-check-input" type="radio"
                                                    value="inactive" id="statusInactive" />
                                                <label class="form-check-label" for="statusInactive"> In Active </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Save Tour</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
