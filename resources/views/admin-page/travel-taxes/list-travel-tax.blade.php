@extends('layouts.admin.layout')

@section('title', 'Travel Tax List - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Travel Tax</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Travel Tax</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.travel_taxes.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add
                    Travel Tax</a>
            </div>
        </section>

        <div class="card my-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="transaction-date-field" class="form-label">Transaction Date</label>
                            <input type="text" placeholder="Select Transaction Date" class="form-control"
                                id="transaction-date-field">
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="status-field" class="form-label">Status</label>
                            <select name="" id="status-field" class="form-select">
                                <option value="">All</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="search-field" class="form-label">Search</label>
                            <input type="text" placeholder="Search..." class="form-control" id="search-field">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered-bottom data-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Transaction Number</th>
                                <th>Reference Number</th>
                                <th>Total Passengers</th>
                                <th>Total Amount</th>
                                <th>Transaction At</th>
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
                searching: false,
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.travel_taxes.list') }}",
                    data: function(d) {
                        d.search_value = $('#search-field').val(),
                            d.transaction_date = $('#transaction-date-field').val(),
                            d.status = $('#status-field').val(),
                            d.class = $('#class-field').val()
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'transaction_number',
                        name: 'transaction_number',
                        orderable: false,
                    },
                    {
                        data: 'reference_number',
                        name: 'reference_number',
                        orderable: false,
                    },
                    {
                        data: 'total_passengers',
                        name: 'total_passengers',
                        orderable: false,
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'transaction_at',
                        name: 'transaction_at'
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
                ],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            });
        }

        $('#status-field').change(function() {
            if (table) {
                table.draw();
            }
        })

        $('#search-field').on('input', function() {
            if (table) {
                table.draw();
            }
        });

        loadTable();

        $('#transaction-date-field').flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            onClose: function(selectedDates, dateStr, instance) {
                table.draw();
            },
        })
    </script>
@endpush
