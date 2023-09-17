@extends('layouts.admin.layout')

@section('title', 'Admin Profile')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Admin Profile</h4>

        <div class="card mb-4">
            <h5 class="card-header">Profile Details</h5>
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    @if ($user->admin_profile)
                        <img src="{{ URL::asset('assets/img/admin_profiles/' . $user->admin_profile) }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                    @else
                        <img src="https://philippines-hoho.ph/philippines_hoho.3b7019f3d8ced762.jpg" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                    @endif

                    <div class="button-wrapper">
                        <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="bx bx-upload d-block d-sm-none"></i>
                        </label>
                            {{-- <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button> --}}
                        <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 1 MB</p>
                    </div>
                </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <form id="formAccountSettings" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" hidden value="{{ $user->id }}">
                    <input type="file" id="upload" class="account-file-input" name="admin_profile" hidden accept="image/png, image/jpeg" />
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input class="form-control" type="text" id="email" name="email"
                                value="{{ $user->email }}" placeholder="john.doe@example.com" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" disabled type="text" id="username" name="username"
                                value="{{ $user->username }}" placeholder="john.doe@example.com" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="firstname" class="form-label">First Name</label>
                            <input class="form-control" type="text" id="firstname" name="firstname"
                                value="{{ $user->firstname }}" autofocus />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input class="form-control" type="text" name="lastname" id="lastname"
                                value="{{ $user->lastname }}" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="contact_no">Phone Number</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">PH (+63)</span>
                                <input type="text" id="contact_no" name="contact_no" class="form-control"
                                    value="{{ $user->contact_no }}" placeholder="9123124567" />
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input class="form-control" type="date" id="birthdate" name="birthdate" oninput="calculateAge()" value="{{ $user->birthdate }}" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="age" class="form-label">Age</label>
                            <input type="text" class="form-control" id="age" name="age" placeholder="" value="{{ $user->age }}" readonly />
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Change Password</h5>
            <div class="card-body">
                <form action="{{ route('admin.change_password.post') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="old_password" class="form-label">Old Password</label>
                                <input type="password" name="old_password" class="form-control">
                                <span class="text-danger danger">@error('old_password'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control">
                                <span class="text-danger danger">@error('new_password'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Password Confirmation</label>
                                <input type="password" name="confirm_password" class="form-control">
                                <span class="text-danger danger">@error('confirm_password'){{ $message }}@enderror</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary">Save New Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function calculateAge() {
            // Get the input element
            var birthdateInput = document.getElementById('birthdate');
            // Get the selected date from the input field
            var selectedDate = new Date(birthdateInput.value);

            // Get the current date
            var currentDate = new Date();

            // Calculate the age
            var age = currentDate.getFullYear() - selectedDate.getFullYear();

            // Check if the birthday for this year has occurred or not
            if (currentDate.getMonth() < selectedDate.getMonth() || (currentDate.getMonth() === selectedDate.getMonth() &&
                    currentDate.getDate() < selectedDate.getDate())) {
                age--;
            }

            // Display the age
            document.getElementById('age').value = age;
        }
    </script>
@endpush
