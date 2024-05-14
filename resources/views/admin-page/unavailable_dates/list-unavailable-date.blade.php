@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Unavailable Dates List')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Unavailable Dates</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Unavailable Dates</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.unavailable_dates.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add
                    Unavailable Date</a>
            </div>
        </section>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive table-wrap">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Unavailable Date</th>
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
                pageLength: 10,
                responsive: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.unavailable_dates.list') }}",
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'unavailable_date',
                        name: 'unavailable_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ]
            });
        }

        loadTable();

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove unavailable date from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.unavailable_dates.destroy') }}",
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
    </script>
@endpush
