@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Tour Reservation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Tour Reservation</h4>
        <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>
</div>
@endsection
