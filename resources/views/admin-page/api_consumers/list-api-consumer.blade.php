@extends('layouts.admin.layout')

@section('title', 'List API Consumer - Philippines Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Consumers List</h4>
        <a href="{{ route('admin.api_consumers.create') }}" class="btn btn-primary">Add Consumer <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive data-wrap">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Consumer Name</th>
                            <th>Platform</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function loadTable() {
            let table = $('.data-table').DataTable({
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.api_consumers.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'consumer_name',
                        name: 'consumer_name',
                    },
                    {
                        data: 'platform',
                        name: 'platform',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
        }

        loadTable();
    </script>
@endpush
@endsection