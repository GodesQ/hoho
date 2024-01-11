@extends('layouts.admin.layout')

@section('title', 'Edit Food - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Food</h4>
            <a href="{{ route('admin.foods.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <form action="{{ route('admin.foods.update', $food->id) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ $food->title }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="merchant_id" class="form-label">Merchant</label>
                                <select name="merchant_id" id="merchant_id" class="select2">
                                    @foreach ($merchants as $merchant)
                                        <option value="{{ $merchant->id }}"
                                            {{ $merchant->id == $food->merchant_id ? 'selected' : null }}>
                                            {{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="int" class="form-control" name="price" id="price"
                                    value="{{ $food->price }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="food_category_id" class="form-label">Food Category</label>
                                <select name="food_category_id" id="food_category_id" class="select2">
                                    <option value="">-- SELECT FOOD CATEGORY --</option>
                                    @foreach ($foodCategories as $foodCategory)
                                        <option value="{{ $foodCategory->id }}"
                                            {{ $foodCategory->id == $food->food_category_id ? 'selected' : null }}>
                                            {{ $foodCategory->title }} ({{ $foodCategory->merchant->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $food->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <input type="text" class="form-control" name="note" id="note" value="{{ $food->note }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" {{ $food->is_active ? 'checked' : null }} />
                                    <label class="form-check-label" for="isActive">Is Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Food</button>
                </div>
            </div>
        </form>
    </div>
@endsection
