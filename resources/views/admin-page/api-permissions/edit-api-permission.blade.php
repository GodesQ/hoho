@extends('layouts.admin.layout')

@section('title', 'Edit Api Permission - Philippines Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Permission</h4>
        <a href="{{ route('admin.api_permissions.index') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.api_permissions.update', $permission->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-xl-12">
                        <div class="mb-3">
                            <label for="name-field" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" name="name" id="name-field" value="{{ $permission->name }}">
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="mb-3">
                            <label for="description-field" class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="description-field" cols="30" rows="5">{{ $permission->description }}</textarea>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary">Save Permission</button>
            </form>
        </div>
    </div>
</div>
@endsection