@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tour Reservations List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Tour Reservations List</h4>
        <a href="{{ route('admin.tour_reservations.create') }}" class="btn btn-primary">Add Reservation <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table   data-table">
                    <thead>
                        <tr>
                            <th>Reserved User</th>
                            <th>Type</th>
                            <th>Tour</th>
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
                    url: "{{ route('admin.tour_reservations.list') }}"
                },
                columns: [
                    {
                        data: 'reserved_user',
                        name: 'reserved_user'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'tour',
                        name: 'tour'
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
