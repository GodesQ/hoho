@extends('layouts.admin.layout')

@section('title', 'Transactions List')

@section('content')
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
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive-lg text-nowrap">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th>Reference No</th>
                                        <th>User</th>
                                        <th>Payment Amount</th>
                                        <th>Transaction Type</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
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
        function loadTable() {
            let table = $('.data-table').DataTable({
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.transactions.list') }}"
                },
                columns: [{
                        data: 'reference_no',
                        name: 'reference_no'
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
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            });
        }
        loadTable();
    </script>
@endpush
