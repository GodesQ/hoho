@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tour Badges')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Tour Badges List</h4>
        <a href="{{ route('admin.tour_badges.create') }}" class="btn btn-primary">Add Tour Badge <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive table-wrap">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Badge Name</th>
                            <th>Badge Code</th>
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
            $('.data-table').DataTable({
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.tour_badges.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'badge_name',
                        name: 'badge_name'
                    },
                    {
                        data: 'badge_code',
                        name: 'badge_code'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ]
            })
        }
        loadTable();
    </script>
@endpush