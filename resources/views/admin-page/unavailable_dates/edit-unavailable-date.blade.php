@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Unavailable Date')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Unavailable Date</h4>
        <a href="{{ route('admin.unavailable_dates.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.unavailable_dates.update', $tourUnavailableDate->id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Unavailable Date</label>
                            <input type="date" class="form-control" name="unavailable_date" id="unavailable_date" value="{{ $tourUnavailableDate->unavailable_date }}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea class="form-control" name="reason" id="reason" cols="30" rows="5">{{ $tourUnavailableDate->reason }}</textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary">Save Unavailable Date</button>
            </form>
        </div>
    </div>
</div>
@endsection