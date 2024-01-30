@extends('layouts.admin.layout')

@section('title', 'Create Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Admin</h4>
        <a href="{{ route('admin.admins.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger my-2 mb-3" style="border-left: 5px solid red;">
                            Invalid Fields. Please check all fields before submitting the form.
                        </div>
                    @endif
                    <form action="{{ route('admin.admins.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" value="">
                                    <div class="text-danger">@error('username'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="">
                                    <div class="text-danger">@error('email'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" value="">
                                    <div class="text-danger">@error('password'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">Firstname</label>
                                    <input type="text" class="form-control" name="firstname" value="">
                                    <div class="text-danger">@error('firstname'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="middlename" class="form-label">Middlename</label>
                                    <input type="text" class="form-control" name="middlename" value="">
                                    <div class="text-danger">@error('middlename'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Lastname</label>
                                    <input type="text" class="form-control" name="lastname" value="">
                                    <div class="text-danger">@error('lastname'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="birthdate" class="form-label">Birthdate</label>
                                    <input type="date" onchange="FindAge()"  class="form-control" name="birthdate" id="birthdate">
                                    <div class="text-danger">@error('birthdate'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="text" class="form-control" name="age" id="age"
                                            placeholder="Input the birthdate to get the age" readonly>
                                    <div class="text-danger">@error('age'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select name="role" id="role" class="form-select">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger">@error('role'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isApproved"
                                        name="is_approved" />
                                    <label class="form-check-label" for="isApproved">Approve</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-primary">Save Admin</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6>Preview of Admin Profile</h6>
                    <img src="{{ URL::asset('assets/img/default-image.jpg') }}" id="previewImage" alt="Default Image" width="100%" height="200px" style="border-radius: 10px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

    <script>
        function FindAge() {
            var day = document.getElementById("birthdate").value;
            var birthdate = new Date(day);
            var today = new Date();
            var Age = today.getTime() - birthdate.getTime();
            Age = Math.floor(Age / (1000*60*60*24*365.25));
            document.getElementById("age").value = Age;
        }
    </script>

@endpush
