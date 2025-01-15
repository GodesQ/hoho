@extends('layouts.admin.layout')

@section('title', 'Transactions List')

@section('content')

    <style>
        .flatpickr-day.selected,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.endRange:hover {
            background: #ad2002 !important;
        }

        span.flatpickr-day.startRange,
        span.flatpickr-day.endRange {
            border: #ad2002 !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Transactions</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Transactions</h6>
            </div>
        </section>

        <div class="row">
            <div class="col-xl">

                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3">
                                <div class="form-group">
                                    <label for="search-field" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search-field"
                                        placeholder="Search anything...">
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="form-group">
                                    <label for="transaction-date-field" class="form-label">Transaction Date</label>
                                    <div class="input-group">
                                        <input type="date" id="transaction-date-field" class="form-control"
                                            placeholder="Select Date...">
                                        <button class="btn btn-primary" type="button" id="button-clear"
                                            onclick="onClearTripDate()"><i class="bx bx-x"></i></button>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="form-group">
                                    <label for="transaction-type-field" class="form-label">Transaction Type</label>
                                    <select id="transaction-type-field" class="form-select select2">
                                        <option value="">All</option>
                                        <option value="book_tour">Book Tour</option>
                                        <option value="travel_tax">Travel Tax</option>
                                        <option value="order">Order</option>
                                        <option value="hotel_reservation">Hotel Reservation</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="form-group">
                                    <label for="status-field" class="form-label">Status</label>
                                    <select id="status-field" class="form-select select2">
                                        <option value="">All</option>
                                        <option value="success">Success</option>
                                        <option value="inc">Incompleted</option>
                                        <option value="pending">Pending</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <label for="" class="form-label">Actions</label> <br>
                                <div class="btn-group">
                                    <button class="btn btn-primary" id="filter-btn" title="Filter"><i
                                            class='bx bx-filter'></i></button>
                                    <button type="button" class="btn btn-secondary" id="export-csv-btn"
                                        data-url="{{ route('admin.transactions.export_csv') }}" title="Export CSV"><i
                                            class='bx bx-export'></i></button>
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
                                        <th>Reference No</th>
                                        <th>User</th>
                                        <th>Payment Amount</th>
                                        <th>Transaction Type</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Transaction Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#transaction-date-field').flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d",
            mode: "range",
        })

        var table;

        function loadTable() {
            table = $('.data-table').DataTable({
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: true,
                lengthChange: false,
                ordering: false,
                searching: false,
                ajax: {
                    url: "{{ route('admin.transactions.list') }}",
                    data: function(d) {
                        d.search = $('#search-field').val(),
                            d.transaction_type = $('#transaction-type-field').val(),
                            d.status = $('#status-field').val(),
                            d.transaction_date = $('#transaction-date-field').val()
                    }
                },
                columns: [{
                        data: 'reference_no',
                        name: 'reference_no',
                        sortable: false,
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'payment_amount',
                        name: 'payment_amount'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'aqwire_paymentMethodCode',
                        name: 'aqwire_paymentMethodCode'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            });

            $('#filter-btn').click(function(e) {
                if (table) {
                    table.draw();
                }
            })
        }

        function onClearTripDate() {
            $('#transaction-date-field').val('');
            if (table) {
                table.draw();
            }
        }

        $('#export-csv-btn').click(function(e) {
            let baseURL = $(this).data('url');
            let searchValue = document.querySelector('#search-field').value;
            let transactionDateValue = document.querySelector('#transaction-date-field').value;
            let transactionTypeValue = document.querySelector('#transaction-type-field').value;
            let statusValue = document.querySelector('#status-field').value;

            let url =
                `${baseURL}?search=${searchValue}&transaction_type=${transactionTypeValue}&transaction_date=${transactionDateValue}&status=${statusValue}`;
            console.log(url); // Log the complete URL
            window.location.replace(url); // Trigger the CSV download
        });


        loadTable();
    </script>
@endpush
