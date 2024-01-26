@extends('layouts.admin.layout')

@section('title', 'List Addons - Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Addons List</h4>
        <a href="{{ route('admin.hotel_reservations.create') }}" class="btn btn-primary">Add Addon <i
                class="bx bx-plus"></i></a>
    </div>
</div>

@endsection