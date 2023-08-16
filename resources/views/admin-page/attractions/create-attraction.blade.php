@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Attraction')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Attraction</h4>
        <a href="{{ route('admin.attractions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.attractions.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Attraction Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
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
                                id="price">
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
