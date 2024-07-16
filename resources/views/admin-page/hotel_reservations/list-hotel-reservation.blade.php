@extends('layouts.admin.layout')

@section('title', 'Hotel Reservations List - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Hotel Reservations</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Hotel Reservations</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.hotel_reservations.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Hotel Reservation</a>
            </div>
        </section>

        <div class="card">
            <div class="table-responsive card-body">
                <table class="table table-striped table-borderless data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Reserved User</th>
                            <th>Room</th>
                            <th>Pax</th>
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
                lengthChange: false,
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.hotel_reservations.index') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'reserved_user_id',
                        name: 'reserved_user_id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'room_id',
                        name: 'room_id'
                    },
                    {
                        data: 'number_of_pax',
                        name: 'number_of_pax'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],

            });
        }

        loadTable();

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Remove Hotel Reservation',
                text: "Do you really want to delete this hotel reservation?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.hotel_reservations.destroy', '') }}" + '/' + id,
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
                        },
                        error: function(response) {
                            let json_response = response?.responseJSON;
                            toastr.error(json_response.message, 'Failed');
                        }
                    })
                }
            })
        });
    </script>
@endpush
