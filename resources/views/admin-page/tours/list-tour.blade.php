@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tours List')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Tours</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Tours</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.tours.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Tour</a>
            </div>
        </section>

        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="type-field" class="form-label">Type</label>
                            <select name="" id="type-field" class="select2">
                                <option value="">All</option>
                                <option value="City Tour">City Tour</option>
                                <option value="Guided Tour">Guided Tour</option>
                                <option value="DIY Tour">DIY Tour</option>
                                <option value="Layover Tour">Layover Tour</option>
                                <option value="Seasonal Tour">Seasonal Tour</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="" id="status-field" class="select2">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="search-field" class="form-label">Search</label>
                            <input type="search" class="form-control" id="search-field" placeholder="Search Tour...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive-lg text-nowrap">
                    <table class="table table-striped table-bordered-bottom data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th></th>
                                <th>Tour</th>
                                <th>Default Price</th>
                                <th>Status</th>
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
                lengthChange: false,
                ordering: false,
                searching: false,
                ajax: {
                    url: "{{ route('admin.tours.list') }}",
                    data: function(d) {
                        d.search = $('#search-field').val(),
                            d.type = $('#type-field').val(),
                            d.status = $('#status-field').val()
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'tour_image',
                        name: 'tour_image'
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
                ],

                columnDefs: [{
                    targets: [4], // Index of the column you want to disable sorting for
                    orderable: false
                }],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            });
        }

        $("#search-field").on('input', function(e) {
            if(table) {
                table.draw();
            }
        })

        $('#type-field').change(function(e) {
            if(table) {
                table.draw();
            }
        })

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove tour from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.tours.destroy') }}",
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
