@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tour Reservations List')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Tour Reservations List</h4>
            @auth('admin')
                @can('book_tour')
                    <a href="{{ route('admin.tour_reservations.create') }}" class="btn btn-primary">
                        Add Reservation <i class="bx bx-plus"></i>
                    </a>
                @endcan
            @endauth
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="" class="form-label">Status</label>
                            <select name="" id="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label for="" class="form-label">Trip Date</label>
                        <div class="input-group">
                            <input type="date" name="" id="trip_date" class="form-control">
                            <button class="btn btn-primary" type="button" id="button-clear"
                                onclick="onClearTripDate()">Clear <i class="bx bx-x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All</option>
                                <option value="Guided Tour">Guided Tour</option>
                                <option value="DIY Tour">DIY Tour</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Trip Date</th>
                                <th>Reserved User</th>
                                <th>Tour</th>
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
                lengthChange: false,
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.tour_reservations.list') }}",
                    data: function(d) {
                        d.status = $('#status').val(),
                            d.search = $('input[type="search"]').val(),
                            d.type = $('#type').val(),
                            d.trip_date = $('#trip_date').val()
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'reserved_user',
                        name: 'reserved_user',
                        orderable: false,
                    },
                    {
                        data: 'tour',
                        name: 'tour',
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
                        searchable: false
                    }
                ],
                order: [
                    [0, 'asc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $('#status').change(function() {
            if (table) {
                table.draw();
            }
        });

        $('#type').change(function() {
            if (table) {
                table.draw();
            }
        });

        $('#trip_date').change(function() {
            if (table) {
                table.draw();
            }
        });

        function onClearTripDate() {
            $('#trip_date').val('');
            if (table) {
                table.draw();
            }
        }

        loadTable();

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Remove Tour Reservation',
                text: "Do you really want to delete this remove tour reservation?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f0d00',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.tour_reservations.destroy') }}",
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
