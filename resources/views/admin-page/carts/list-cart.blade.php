@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Carts List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <section class="section-header d-flex justify-content-between align-items-center">
        <div class="title-section">
            <h4 class="fw-medium mb-2">Carts</h4>
            <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                    class="text-muted fw-light">Dashboard /</a> Carts</h6>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive table-wrap">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>User</th>
                            <th>Tour</th>
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
                pageLength: 50,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.carts.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'tour',
                        name: 'tour'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove cart from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.carts.destroy') }}",
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