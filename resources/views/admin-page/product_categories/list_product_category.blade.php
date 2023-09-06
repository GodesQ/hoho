@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Product Categories List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Product Categories List</h4>
        <a href="{{ route('admin.product_categories.create') }}" class="btn btn-primary">Add Product Category <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table   data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Organizations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function loadTable() {
            let table = $('.data-table').DataTable({
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.product_categories.list') }}"
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'organizations',
                        name: 'organizations'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            });
        }
        loadTable();
    </script>
@endpush
