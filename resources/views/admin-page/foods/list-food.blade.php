@extends('layouts.admin.layout')

@section('title', 'Foods List - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Foods</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Foods</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.foods.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Food</a>
            </div>
        </section>

        <div class="card">
            <div class="table-responsive card-body">
                <table class="table table-striped table-bordered-bottom data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Merchant</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Food Category</th>
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
                    serverSide: false,
                    ajax: {
                        url: "{{ route('admin.foods.index') }}"
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'merchant',
                            name: 'merchant',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'food_category',
                            name: 'food_category',
                            orderable: false,
                            searchable: false,
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
                        [0, 'asc'] // Sort by the first column (index 0) in descending order
                    ]
                });
            }

            $(document).ready(function() {
                $(document).on("click", ".remove-btn", function(e) {
                    let id = $(this).attr("id");
                    Swal.fire({
                        title: 'Remove Food',
                        text: "Do you really want to delete this food?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#6f0d00',
                        cancelButtonColor: '#ff3e1d',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = `{{ route('admin.foods.destroy') }}` + '/' + id;
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
