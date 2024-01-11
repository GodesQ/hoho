@extends('layouts.admin.layout')

@section('title', 'Edit Food Category - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Food Category</h4>
            <a href="{{ route('admin.food_categories.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                List</a>
        </div>

        <form action="{{ route('admin.food_categories.update', $foodCategory->id) }}" method="post">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="title c"lass="form-label">Title</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ $foodCategory->title }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="merchant_id" class="form-label">Merchant</label>
                                <select name="merchant_id" id="merchant_id" class="select2">
                                    @foreach ($merchants as $merchant)
                                        <option value="{{ $merchant->id }}"
                                            {{ $foodCategory->merchant_id == $merchant->id ? 'selected' : null }}>
                                            {{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $foodCategory->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Food Category</button>
                </div>
            </div>
        </form>
    </div>
@endsection
