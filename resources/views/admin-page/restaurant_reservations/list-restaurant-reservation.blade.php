@extends('layouts.admin.layout')

@section('title', 'Restaurant Reservations List - Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Restaurant Reservations List</h4>
        <a href="{{ route('admin.restaurant_reservations.create') }}" class="btn btn-primary">Add Restaurant Reservation <i
                class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Reserved User</th>
                        <th>Merchant</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
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
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.restaurant_reservations.index') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'reserved_user_id',
                        name: 'reserved_user_id'
                    },
                    {
                        data: 'merchant_id',
                        name: 'merchant_id'
                    },
                    {
                        data: 'seats',
                        name: 'seats'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ]
            });
        }
    </script>    
@endpush

