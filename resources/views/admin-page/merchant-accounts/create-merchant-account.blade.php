@extends('layouts.admin.layout')

@section('title', 'Add Merchant Account - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Merchant Account</h4>
            <a href="{{ route('admin.merchant_accounts.index') }}" class="btn btn-dark">Back to List <i
                    class="bx bx-undo"></i></a>
        </div>

        <form action="" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Select Merchant & Role</h4>
                            <hr>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select select2">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger">
                                    @error('role')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 w-100">
                                <label for="merchant-select-field" class="form-label">Select Merchant</label>
                                <select name="merchant_id" id="merchant-select-field" class="select2">
                                    <option value="">--- SELECT ROLE FIRST ---</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="username" value="">
                                        <div class="text-danger">
                                            @error('username')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" value="">
                                        <div class="text-danger">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3 form-password-toggle">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="password">Password</label>
                                        </div>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="***********" aria-describedby="password" />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">Firstname</label>
                                        <input type="text" class="form-control" name="firstname" value="">
                                        <div class="text-danger">
                                            @error('firstname')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="middlename" class="form-label">Middlename</label>
                                        <input type="text" class="form-control" name="middlename" value="">
                                        <div class="text-danger">
                                            @error('middlename')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Lastname</label>
                                        <input type="text" class="form-control" name="lastname" value="">
                                        <div class="text-danger">
                                            @error('lastname')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="birthdate" class="form-label">Birthdate</label>
                                        <input type="date" onchange="FindAge()" class="form-control" name="birthdate"
                                            id="birthdate">
                                        <div class="text-danger">
                                            @error('birthdate')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="text" class="form-control" name="age" id="age"
                                            placeholder="Input the birthdate to get the age" readonly>
                                        <div class="text-danger">
                                            @error('age')
                                                {{ $message }}
                                            @enderror
                                        </div>
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
                            <button class="btn btn-primary">Save Merchant Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let role = $('#role').val();
            fetchMerchants(role);
        })

        $('#role').on('change', function(e) {
            let role = e.target.value;
            fetchMerchants(role);
        })

        function fetchMerchants(role) {
            $.ajax({
                url: '{{ route('admin.merchants.users.roles', '') }}' + '/' + role,
                method: 'GET',
                success: function(data) {
                    $('#merchant-select-field').empty();
                    data.merchants.forEach(merchant => {
                        var newOption = new Option(merchant.name, merchant.id, false, false);
                        $('#merchant-select-field').append(newOption).trigger('change');
                    });
                }
            })
        }
    </script>
@endpush
