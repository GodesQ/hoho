@extends('layouts.admin.layout')

@section('title', 'Admins List - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl container-fluid flex-grow-1 container-p-y">

        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Admins</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Admins</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Admin</a>
            </div>
        </section>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-borderless data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Is Approved</th>
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
                lengthChange: false,
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.admins.list') }}"
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
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'is_approved',
                        name: 'is_approved'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],
                columnDefs: [{
                    targets: [4, 5], // Index of the column you want to disable sorting for
                    orderable: false
                }],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }
        loadTable();
        
        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr('id');
            
            Swal.fire({
                title: 'Do you really want to delete this record?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.admins.destroy', '') }}" + '/' + id,
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
        })
        
    </script>
@endpush
