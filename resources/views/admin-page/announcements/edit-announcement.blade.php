@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Announcement')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Announcement</h4>
        <a href="{{ route('admin.announcements.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $announcement->name }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option {{ $announcement->type == 'operation' ? 'selected' : null }} value="operation">Operation Announcement</option>
                                <option {{ $announcement->type == 'safety' ? 'selected' : null }} value="safety">Safety Announcement</option>
                                <option {{ $announcement->type == 'holiday_greeting' ? 'selected' : null }} value="holiday_greeting">Holiday Greeting Announcement</option>
                                <option {{ $announcement->type == 'subscription' ? 'selected' : null }} value="subscription">News Letter Subscription Announcement</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" cols="30" rows="5" class="form-control">{{ $announcement->message }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" {{ $announcement->is_active ? 'checked' : null}} />
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="isImportant" name="is_important" {{ $announcement->is_important ? 'checked' : null}} />
                            <label class="form-check-label" for="isImportant">Is Important?</label>
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary">Save Announcement</button>
            </form>
        </div>
    </div>
</div>

@endsection