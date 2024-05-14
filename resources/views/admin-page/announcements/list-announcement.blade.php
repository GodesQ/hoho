@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - List Announcements')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Announcements</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Announcements</h6>
            </div>
            @auth('admin')
                @can('create_announcement')
                    <div class="action-section btn-group">
                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Announcement</a>
                    </div>
                @endcan
            @endauth
        </section>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive data-wrap">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Added At</th>
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
                columns: [{
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
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                    }
                ],

                columnDefs: [{
                    targets: [3], // Index of the column you want to disable sorting for
                    orderable: false
                }],
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
