@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Hotels List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Hotels List</h4>
        <a href="{{ route('admin.merchants.hotels.create') }}" class="btn btn-primary">Add Hotel <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="" class="form-label">Organization</label>
                        <select name="organization_id" id="organization_id" class="form-select">
                            <option value="">--- ALL ---</option>
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Nature Of Business</th>
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
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.merchants.hotels.list') }}",
                    data: function (d) {
                        d.search = $('input[type="search"]').val();
                        d.organization_id = $('#organization_id').val();
                    }
                },
                columns: [
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
                        data: 'is_featured',
                        name: 'is_featured'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ],
                
                columnDefs: [
                    {
                    targets: [0, 4], // Index of the column you want to disable sorting for
                    orderable: false
                    }
                ],
                order: [
                    [1, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove hotel from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.merchants.hotels.destroy') }}",
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
