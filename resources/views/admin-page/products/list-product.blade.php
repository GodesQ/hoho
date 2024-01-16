@extends('layouts.admin.layout')

@section('title', 'Products List - Philippine Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Products List</h4>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product <i class="bx bx-plus"></i></a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Merchant</th>
                            <th>Name</th>
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
                pageLength: 10,
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
                        data: 'merchant',
                        name: 'merchant'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            });
        }

        $(document).ready(function() {
            $(document).on("click", ".remove-btn", function(e) {
                let id = $(this).attr("id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Remove product from list",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
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
