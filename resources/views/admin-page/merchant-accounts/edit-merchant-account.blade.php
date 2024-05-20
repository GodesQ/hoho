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
            <div class="col-xxl-4 col-xl-5">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Merchant Information</h4>
                            @if($merchant_account->merchant)
                                <button class="btn btn-primary btn-sm" type="button" id="unsync-btn" data-id="{{ $merchant_account->id }}">Unsync Merchant</button>
                            @endif
                        </div>
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
                            <form action="{{ route('admin.merchant_accounts.update_merchant') }}" method="post">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" id="account-id" value="{{ $merchant_account->id }}"> 
                                <div class="d-flex justify-content-center align-items-center flex-column">
                                    <div class="mb-3 w-100">
                                        <label for="merchant-select-field" class="form-label">Select Merchant</label>
                                        <select name="merchant_id" id="merchant-select-field" class="select2">
                                            <option value="">--- SELECT MERCHANT ---</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-primary">
                                        <i class="bx bx-sync"></i> Sync Merchant 
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-8 col-xl-7">
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
                            <button class="btn btn-primary"><i class="bx bx-save"></i> Save Merchant Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let role = $('#role').val();
            $.ajax({
                url: '{{ route('admin.merchants.users.roles', '') }}' + '/' + role,
                method: 'GET',
                success: function(data) {
                    data.merchants.forEach(merchant => {
                        var newOption = new Option(merchant.name, merchant.id, false, false);
                        $('#merchant-select-field').append(newOption).trigger('change');
                    });
                }
            })
        })

        $('#unsync-btn').on('click', function (e) {
            let dataId = $(e.target).attr('data-id');
            Swal.fire({
                title: 'Unsync Merchant',
                text: "Do you really want to unsync this account to merchant?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#545454',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.merchant_accounts.unsync_merchant') }}",
                        method: "PATCH",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: dataId
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Unsync!', response.message, 'success').then(
                                    result => {
                                        if (result.isConfirmed) {
                                            toastr.success(response.message, 'Success');
                                            location.reload();
                                        }
                                    })
                            }
                        }
                    })
                }
            })
        })
    </script>
@endpush
