@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Stores List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <section class="section-header d-flex justify-content-between align-items-center">
        <div class="title-section">
            <h4 class="fw-medium mb-2">Merchant Stores</h4>
            <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                    class="text-muted fw-light">Dashboard /</a> Merchant Stores</h6>
        </div>
        <div class="action-section btn-group">
            <a href="{{ route('admin.merchants.stores.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Store</a>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped table-bordered-bottom data-table text-wrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th></th>
                            <th width="150">Name</th>
                            <th>Location</th>
                            <th width="120">Payment Options</th>
                            <th>Is Featured</th>
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
        let table;
        function loadTable() {
            table = $('.data-table').DataTable({
                lengthChange: false,
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.merchants.stores.list') }}",
                    data: function (d) {
                        d.search = $('input[type="search"]').val();
                        d.organization_id = $('#organization_id').val();
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'featured_image',
                        name: 'featured_image',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                    },
                    {
                        data: 'location',
                        name: 'location',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'payment_options',
                        name: 'payment_options',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'is_featured',
                        name: 'is_featured',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
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
                title: 'Remove Store',
                text: "Do you really want to delete this store?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.merchants.stores.destroy') }}",
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

        $('#organization_id').change(function() {
            if (table) {
                table.draw();
            }
        });

        loadTable();
    </script>
@endpush
