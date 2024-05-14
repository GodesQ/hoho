@extends('layouts.admin.layout')

@section('title', 'Rooms List - Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <section class="section-header d-flex justify-content-between align-items-center">
        <div class="title-section">
            <h4 class="fw-medium mb-2">Rooms</h4>
            <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                    class="text-muted fw-light">Dashboard /</a> Rooms</h6>
        </div>
        <div class="action-section btn-group">
            <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Room</a>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-borderless data-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Room Name</th>
                        <th>Merchant</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function loadTable() {
            let table = $('.data-table').DataTable({
                lengthChange: false,
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.rooms.index') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'room_name',
                        name: 'room_name'
                    },
                    {
                        data: 'merchant',
                        name: 'merchant',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                    }
                ],
                order: [
                    [0, 'asc'] 
                ]
            });
        }

        $(document).ready(function() {
                $(document).on("click", ".remove-btn", function(e) {
                    let id = $(this).attr("id");
                    Swal.fire({
                        title: 'Remove Room',
                        text: "Do you really want to delete this room?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#6f0d00',
                        cancelButtonColor: '#ff3e1d',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = `{{ route('admin.rooms.destroy') }}` + '/' + id;
                            $.ajax({
                                url: url,
                                method: "DELETE",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire('Removed!', response.message, 'success')
                                            .then(
                                                result => {
                                                    if (result.isConfirmed) {
                                                        toastr.success(response.message,
                                                            'Success');
                                                        location.reload();
                                                    }
                                                })
                                    }
                                }
                            })
                        }
                    })
                });
            })

        loadTable();
    </script>    
@endpush

@endsection