@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Merchants List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Merchants List</h4>
        <a href="{{ route('admin.merchants.list') }}" class="btn btn-primary">Add Merchant <i class="bx bx-plus"></i></a>
    </div>
</div>
@endsection
