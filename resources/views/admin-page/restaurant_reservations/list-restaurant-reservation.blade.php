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
        <div class="table-responsive card-body">
            <table class="table table-striped table-responsive data-table">
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
                serverSide: true,
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
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ]
            });
        }
        loadTable();

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Remove Restaurant Reservation',
                text: "Do you really want to delete this restaurant reservation?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.restaurant_reservations.destroy', '') }}" + '/' + id,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Removed!', response.message, 'success').then(
                                    result => {
                                        if (result.isConfirmed) {
                                            toastr.success(response.message, 'Success');
                                            location.reload();
                                        }
                                    })
                            }
                        }
                    })
                }
            })
        });
    </script>    
@endpush

