@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tour Providers List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <section class="section-header d-flex justify-content-between align-items-center">
        <div class="title-section">
            <h4 class="fw-medium mb-2">Merchant Tour Provider</h4>
            <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                    class="text-muted fw-light">Dashboard /</a> Merchant Tour Provider</h6>
        </div>
        <div class="action-section btn-group">
            <a href="{{ route('admin.merchants.tour_providers.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Hotel</a>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped table-borderless data-table">
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
