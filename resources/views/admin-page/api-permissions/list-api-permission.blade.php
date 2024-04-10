@extends('layouts.admin.layout')

@section('title', 'API Permissions - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">'
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">API Permissions List</h4>
            <a href="{{ route('admin.api_permissions.create') }}" class="btn btn-primary">Add Permission <i
                    class="bx bx-plus"></i></a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Added At</th>
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
                    url: "{{ route('admin.api_permissions.index') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            })
        }

        loadTable();
    </script>
@endpush
