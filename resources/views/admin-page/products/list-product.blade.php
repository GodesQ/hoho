@extends('layouts.admin.layout')

@section('title', 'Products List - Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Products List</h4>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product <i class="bx bx-plus"></i></a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection