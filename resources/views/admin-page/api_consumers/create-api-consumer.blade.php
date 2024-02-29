@extends('layouts.admin.layout')

@section('title', 'Add Api Consumer - Philippines Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Add Consumer</h4>
        <a href="{{ route('admin.api_consumers.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.api_consumers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="consumer-name-field" class="form-label">Consumer Name</label>
                            <input type="text" class="form-control" name="consumer_name" id="consumer-name-field">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="platform-field" class="form-label">Platform</label>
                            <select name="platform" id="platform-field" class="form-select">
                                <option value="website">Website</option>
                                <option value="android">Android</option>
                                <option value="ios">IOS</option>
                                <option value="cross platform">Cross Platform Application</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="contact-email-field" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" id="contact-email-field">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="contact-phone-field" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" id="contact-phone-field">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="api-code-field" class="form-label">API Code</label>
                            <br>
                            <a href="#" id="">API Code is auto generated</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="api-key-field" class="form-label">API Key</label>
                            <br>
                            <a href="#" id="">API Key is auto generated</a>
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary">Save Consumer</button>
            </form>
        </div>
    </div>
</div>
@endsection