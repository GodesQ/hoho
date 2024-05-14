@extends('layouts.admin.layout')

@section('title', 'Food Categories - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Food Categories</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Food Categories</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.food_categories.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Food Category</a>
            </div>
        </section>

        <div class="card">
            <div class="table-responsive card-body">
                <table class="table table-striped table-borderless data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Merchant</th>
                            <th>Title</th>
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
                    pageLength: 10,
                    responsive: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('admin.food_categories.index') }}"
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'merchant',
                            name: 'merchant',
                            orderable: false,
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                        }
                    ],
                    order: [
                        [0, 'asc'] // Sort by the first column (index 0) in descending order
                    ]
                })
            }

            $(document).ready(function() {
                $(document).on("click", ".remove-btn", function(e) {
                    let id = $(this).attr("id");
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Remove food category from list",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = `{{ route('admin.food_categories.destroy') }}` + '/' + id;
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
