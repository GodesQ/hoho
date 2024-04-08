@extends('layouts.admin.layout')

@section('title', 'Edit Merchant Account - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Merchant Account</h4>
            <a href="{{ route('admin.merchant_accounts.index') }}" class="btn btn-dark">Back to List <i
                    class="bx bx-undo"></i></a>
        </div>

        <div class="row">
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-body">
                        <h4>Merchant Information</h4>
                        <hr>
                        @if ($merchant_account->merchant)
                            <div class="row">
                                <div class="col-xl-4">
                                    <?php $type = strtolower($merchant_account->merchant->type ?? '') . 's'; ?>
                                    @if ($merchant_account->merchant->featured_image)
                                        <img src="{{ URL::asset('assets/img/' . $type . '/' . ($merchant_account->merchant->id ?? null) . '/' . ($merchant_account->merchant->featured_image ?? null)) }}"
                                            id="previewImage2" alt="{{ $merchant_account->merchant->name ?? null }}"
                                            width="100%" style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                    @else
                                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                            id="previewImage2" alt="Default Image" width="100%"
                                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="col-xl-8">
                                    <h5 style="margin-bottom: 5px;">{{ $merchant_account->merchant->name }}</h5>
                                    <h6>{{ $merchant_account->merchant->type }}</h6>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-xl-6">Address: </div>
                                <div class="col-xl-6">{{ $merchant_account->merchant->address ?? 'N/A' }}</div>
                            </div>
                            <div class="row my-2">
                                <div class="col-xl-6">Nature of Business: </div>
                                <div class="col-xl-6">{{ $merchant_account->merchant->nature_of_business ?? null }}</div>
                            </div>
                            <div class="row my-2">
                                <div class="col-xl-6">Organization: </div>
                                <div class="col-xl-6">{{ $merchant_account->merchant->organization->name ?? 'N/A' }}</div>
                            </div>
                        @else
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <h5>No Merchant Found</h5>
                                <button class="btn btn-primary">Sync Merchant</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <form action="{{ route('admin.merchant_accounts.update', $merchant_account->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" id="username"
                                            value="{{ $merchant_account->username }}" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="{{ $merchant_account->email }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">Firstname</label>
                                        <input type="text" class="form-control" name="firstname" id="firstname"
                                            value="{{ $merchant_account->firstname }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label for="middlename" class="form-label">Middlename</label>
                                        <input type="text" class="form-control" name="middlename" id="middlename"
                                            value="{{ $merchant_account->middlename }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Lastname</label>
                                        <input type="text" class="form-control" name="lastname" id="lastname"
                                            value="{{ $merchant_account->lastname }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label for="birthdate" class="form-label">Birthdate</label>
                                        <input type="date" class="form-control" name="birthdate" id="birthdate"
                                            value="{{ $merchant_account->birthdate }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" class="form-control" name="age" id="age"
                                            value="{{ $merchant_account->age }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" class="form-control" name="role" id="role"
                                            value="{{ $merchant_account->role }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Merchant Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
