@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit User')

@section('content')
    <style>
        .input-group {
                display: flex;
                align-items: center;
            }

        .input-group select {
            width: 35%;
            padding: 0.5000rem 0.875rem;
            font-size: 0.9375rem;
            color: #495057;
            background: #fff;
            border-radius: 0.375rem 0 0 0.375rem;
            border: 1px solid #d9dee3;
            line-height: 1.53;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit User</h4>
            <a href="{{ route('admin.users.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <h5 class="card-title"><i class="bx bx-lock"></i> Account Information</h5>
                            <hr>
                            <div class="row my-2">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="username">UserName <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username"
                                            placeholder="Type the username" name="username" required value="{{ $user->username }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Type the email" required value="{{ $user->email }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="password">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Type the password" required value="{{ $user->password }}" disabled />
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-title"><i class="bx bx-user"></i> General Information</h5>
                            <hr>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">FirstName</label>
                                        <input type="text" class="form-control" name="firstname" id="firstname"
                                            placeholder="Ex. John" value="{{ $user->firstname }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="middlename" class="form-label">Middlename</label>
                                        <input type="text" class="form-control" name="middlename" id="middlename"
                                            placeholder="Ex. Mid" value="{{ $user->middlename }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Lastname</label>
                                        <input type="text" class="form-control" name="lastname" id="lastname"
                                            placeholder="Ex. Doe" value="{{ $user->lastname }}">
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="birthdate" class="form-label">Birthdate</label>
                                        <input type="date" class="form-control" name="birthdate" id="birthdate"
                                            onchange="FindAge()" value="{{ $user->birthdate }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="text" class="form-control" name="age" id="age"
                                            placeholder="Input the birthdate to get the age" readonly value="{{ $user->age }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select name="gender" id="gender" class="form-select">
                                            <option value="">---- Select Gender ----</option>
                                            <option {{ $user->gender == 'Male' ? 'selected' : null }} value="Male">Male</option>
                                            <option {{ $user->gender == 'Female' ? 'selected' : null }} value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="contact_no" class="form-label">Contact Number</label>
                                        <div class="input-group">
                                            <select id="countryCode" name="countryCode"></select>
                                            <input type="text" class="form-control" name="contact_no" id="contact_no"
                                            placeholder="Ex. 9123215342" value="{{ $user->contact_no }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <?php $interest_ids = $user->interest_ids != null ? json_decode($user->interest_ids) : [] ?>
                                        <label for="interests" class="form-label">Interests</label>
                                        <select name="interest_ids[]" id="interest_ids" class="select2 form-select" multiple>
                                            @foreach ($interests as $interest)
                                                <option {{ in_array($interest->id, $interest_ids) ? 'selected' : null }} value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <div class="form-check ">
                                            <input name="role" class="form-check-input" type="radio"
                                                value="guest" id="roleGuest" {{ $user->role == 'guest' ? 'checked' : null }} />
                                            <label class="form-check-label" for="roleGuest"> Guest </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="role" class="form-check-input" type="radio"
                                                value="anonymous" id="roleAnonymous" {{ $user->role == 'anonymous' ? 'checked' : null }} />
                                            <label class="form-check-label" for="roleAnonymous"> Anonymous </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="isVerify" value="1" {{ $user->is_verify ? 'checked' : null }} name="is_verify" />
                                        <label class="form-check-label" for="isVerify">Is Verify?</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="isOldUser" value="1" name="is_old_user" {{ $user->is_old_user ? 'checked' : null }} />
                                        <label class="form-check-label" for="isOldUser">Is Old User?</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="isFirstTimeInPhilippines" value="1" name="is_first_time_philippines" {{ $user->is_first_time_philippines ? 'checked' : null }} />
                                        <label class="form-check-label" for="isFirstTimeInPhilippines">Is First Time in Philippines?</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="isInternationalTourist" value="1" name="is_international_tourist" {{ $user->is_international_tourist ? 'checked' : null }} />
                                        <label class="form-check-label" for="isInternationalTourist">Is International Tourist?</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <div class="form-check ">
                                            <input name="status" class="form-check-input" type="radio"
                                                value="active" id="statusActive" {{ $user->status == 'active' ? 'checked' : null }} />
                                            <label class="form-check-label" for="statusActive"> Active </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="status" class="form-check-input" type="radio"
                                                value="inactive" id="statusInactive" {{ $user->status == 'inactive' ? 'checked' : null }} />
                                            <label class="form-check-label" for="statusInactive"> In Active </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" name="status" type="radio" value="locked"
                                                id="statusLocked" {{ $user->status == 'locked' ? 'checked' : null }} />
                                            <label class="form-check-label" for="statusLocked"> Locked
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Save User</button>
                            <a href="{{ route('admin.users.resend_email', ['username' => $user->username, 'email' => $user->email]) }}" class="btn btn-outline-primary">
                                Resend Email Verification
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const selectElement = document.getElementById('countryCode');

        fetch("{{ URL::asset('assets/data/phonecountrycodes.json') }}")
        .then(response => response.json())
        .then(data => {
            for (const [countryAB, countryCode] of Object.entries(data)) {
                const option = document.createElement('option');
                option.value = countryCode;
                option.text = `${countryAB} (${countryCode})`;
                if (countryCode == "{{ $user->countryCode }}") { // Set your desired country code here
                    option.selected = true; // Set the 'selected' attribute for the desired option
                }
                selectElement.add(option);
            }
        })

        function FindAge() {
            var day = document.getElementById("birthdate").value;
            var birthdate = new Date(day);
            var today = new Date();
            var Age = today.getTime() - birthdate.getTime();
                if (isNaN(Age))
                    Age = '';
                else
                    Age = Math.floor(Age / (1000*60*60*24*365.25));
                    document.getElementById("age").value = Age;
        }
    </script>
@endpush
