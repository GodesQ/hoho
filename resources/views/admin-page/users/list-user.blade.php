@extends('layouts.admin.layout')

@section('title', 'Guests List')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Guests List</h4>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User <i class="bx bx-plus"></i></a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Contact No</th>
                                <th>Status</th>
                                <th>Email Verified</th>
                                <th>Registered Date</th>
                                <th class="">Actions</th>
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
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.list') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'username',
                        name: 'username',
                        orderable: true,
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'contact_no',
                        name: 'contact_no',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'email_verify',
                        name: 'email_verify',
                        orderable: false,
                    },
                    {
                        data: 'registered_date',
                        name: 'registered_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false
                    }
                ],
                order: [[0, 'desc']] // Sort by the first column (index 0) in descending order
            });
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Remove User',
                text: "Do you really want to delete this user?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.users.destroy') }}",
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
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

        loadTable();
    </script>
@endpush
