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
            <div class="table-responsive-lg text-nowrap">
                <table class="table   data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Contact No</th>
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
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.list') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'contact_no',
                        name: 'contact_no'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove user from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
