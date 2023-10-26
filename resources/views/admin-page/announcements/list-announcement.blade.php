@extends('layouts.admin.layout')    

@section('title', 'Hop On Hop Off - List Announcements')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Announcements List</h4>
        @auth('admin')
            @can('create_announcement')
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">Add Announcement <i class="bx bx-plus"></i></a>
            @endcan
        @endauth
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive data-wrap">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
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
                    url: "{{ route('admin.announcements.list') }}"
                },
                columns: [
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                    }
                ],

                columnDefs: [
                    {
                    targets: [3], // Index of the column you want to disable sorting for
                    orderable: false
                    }
                ],
                order: [
                    [0, 'asc'] // Sort by the first column (index 0) in descending order
                ]
            });
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove announcement from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.announcements.destroy') }}",
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