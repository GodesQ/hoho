@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off - Edit Promo Codes')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Edit Promo Codes</h4>
        <a href="{{ route('admin.promo_codes.list') }}" class="btn btn-dark"> <i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.promo_codes.update', $promocode->id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $promocode->name }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Promo Code</label>
                            <input type="text" class="form-control" name="code" id="code" value="{{ $promocode->code }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Promo Description</label>
                            <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ $promocode->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="isNeedRequirment" name="is_need_requirement" {{ $promocode->is_need_requirement ? 'checked' : null }} />
                            <label class="form-check-label" for="isNeedRequirment">Need Requirment?</label>
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary">Save Promo Code</button>
            </form>
        </div>
    </div>
</div>
@endsection