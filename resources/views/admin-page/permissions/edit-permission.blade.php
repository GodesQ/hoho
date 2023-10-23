@extends('layouts.admin.layout')

@section('title', 'Edit Permission')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Permission</h4>
            <a href="{{ route('admin.permissions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="permission_name" class="form-label">Permission Name</label>
                                <input type="text" name="permission_name" id="permission_name" class="form-control" value="{{ $permission->permission_name }}" required>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="mb-3">
                                <label for="permission_name" class="form-label">Roles</label>
                                <div class="row">
                                    <?php $selected_roles = json_decode($permission->roles); ?>
                                    @foreach ($roles as $role)
                                        <div class="col-lg-6">
                                            <div class="form-check mt-3">
                                                <input class="form-check-input" type="checkbox" value="{{ $role->slug }}"
                                                    id="role_{{ $role->slug }}" {{ in_array($role->slug, $selected_roles) ? 'checked' : null }} name="roles[]" />
                                                <label class="form-check-label" for="role_{{ $role->slug }}">
                                                    {{ $role->name }} </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary">Save Permission</button>
                </form>
            </div>
        </div>
    </div>
@endsection
