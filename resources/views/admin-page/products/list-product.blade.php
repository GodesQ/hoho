@extends('layouts.admin.layout')

@section('title', 'Products List - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Products List</h4>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product <i class="bx bx-plus"></i></a>
        </div>

        <div class="card">
            <div class="table-responsive card-body">
                <table class="table table-striped table-borderless data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Product</th>
                            <th>Price</th>
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
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.products.index') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'product',
                        name: 'product',
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
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                    }
                ]
            });
        }

        $(document).ready(function() {
            $(document).on("click", ".remove-btn", function(e) {
                let id = $(this).attr("id");
                Swal.fire({
                    title: 'Remove Product',
                    text: "Do you really want to delete this product?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6f0d00',
                    cancelButtonColor: '#ff3e1d',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let url = `{{ route('admin.products.destroy') }}` + '/' + id;
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
