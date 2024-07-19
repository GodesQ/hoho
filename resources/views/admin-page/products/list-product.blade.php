@extends('layouts.admin.layout')

@section('title', 'Products List - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Products</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Products</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Product</a>
            </div>
        </section>

        <div class="card">
            <div class="table-responsive card-body">
                <table class="table table-striped table-bordered-bottom data-table">
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
                        name: 'name',
                        orderable: false,
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
