@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Permissions List')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Permissions List</h4>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">Add Permission <i
                    class="bx bx-plus"></i></a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive-lg text-nowrap">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Permission Name</th>
                                <th>Roles</th>
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
                    url: "{{ route('admin.permissions.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'permission_name',
                        name: 'permission_name,'
                    },
                    {
                        data: 'roles',
                        name: 'roles'
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
