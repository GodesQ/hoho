@extends('layouts.admin.layout')

@section('title', 'Edit Api Consumer - Philippines Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Consumer</h4>
        <a href="{{ route('admin.api_consumers.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.api_consumers.update', $consumer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="consumer-name-field" class="form-label">Consumer Name</label>
                            <input type="text" class="form-control" name="consumer_name" id="consumer-name-field" value="{{ $consumer->consumer_name }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="platform-field" class="form-label">Platform</label>
                            <select name="platform" id="platform-field" class="form-select">
                                <option {{ $consumer->platform == 'website' ? 'selected' : null }} value="website">Website</option>
                                <option {{ $consumer->platform == 'android' ? 'selected' : null }} value="android">Android</option>
                                <option {{ $consumer->platform == 'ios' ? 'selected' : null }} value="ios">IOS</option>
                                <option {{ $consumer->platform == 'cross platform' ? 'selected' : null }} value="cross platform">Cross Platform Application</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="contact-email-field" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" id="contact-email-field" value="{{ $consumer->contact_email }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="contact-phone-field" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" id="contact-phone-field" value="{{ $consumer->contact_phone }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="api-code-field" class="form-label">API Code</label>
                            <input type="text" id="api-code-field" readonly class="form-control" value="{{ $consumer->api_code }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="api-key-field" class="form-label">API Key</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="api-key-field" class="form-control"
                                    placeholder="***********" aria-describedby="api-key-field" value="{{ $consumer->api_key }}" />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary">Save Consumer</button>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        var inputField = document.getElementById("api-key-field");
        var icon = document.querySelector(".bx-hide");

        if (inputField.type === "password") {
            inputField.type = "text";
            icon.classList.remove("bx-hide");
            icon.classList.add("bx-show"); // Add class for showing eye icon
        } else {
            inputField.type = "password";
            icon.classList.remove("bx-show"); // Remove class for showing eye icon
            icon.classList.add("bx-hide");
        }
    }
</script>
@endsection