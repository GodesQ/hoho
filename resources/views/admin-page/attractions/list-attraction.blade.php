@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Attractions List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Attractions List</h4>
        <a href="{{ route('admin.attractions.create') }}" class="btn btn-primary">Add Attraction <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Interests</th>
                            <th>Status</th>
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
                    url: "{{ route('admin.attractions.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'interests',
                        name: 'interests'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            })
        }

        loadTable();
    </script>
@endpush
