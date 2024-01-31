@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tour Providers List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Tour Providers List</h4>
        <a href="{{ route('admin.merchants.tour_providers.create') }}" class="btn btn-primary">Add Tour Provider <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped data-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th></th>
                            <th>Name</th>
                            <th>Nature Of Business</th>
                            <th>Interests</th>
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
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.merchants.tour_providers.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'featured_image',
                        name: 'featured_image',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'nature_of_business',
                        name: 'nature_of_business'
                    },
                    {
                        data: 'interests',
                        name: 'interests'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ],

                columnDefs: [
                    {
                    targets: [3], // Index of the column you want to disable sorting for
                    orderable: false
                    }
                ],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Remove Tour Provider',
                text: "Do you really want to delete this tour provider?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.merchants.tour_providers.destroy') }}",
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
