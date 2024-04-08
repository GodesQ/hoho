@extends('layouts.admin.layout')

@section('title', 'Add Merchant Account - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Merchant Account</h4>
            <a href="{{ route('admin.merchant_accounts.index') }}" class="btn btn-dark">Back to List <i
                    class="bx bx-undo"></i></a>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
        
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
