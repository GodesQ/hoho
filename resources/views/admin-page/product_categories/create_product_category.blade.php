@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Product Categories List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Product Categories List</h4>
        <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary">Add Product Category <i class="bx bx-plus"></i></a>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" value="">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="featured_image" class="form-label">Featured Image</label>
                <input type="file" name="featured_image" class="form-control" id="featured_image" value="">
            </div>
        </div>
    </div>
</div>
