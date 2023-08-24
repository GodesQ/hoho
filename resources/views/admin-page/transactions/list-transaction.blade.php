@extends('layouts.admin.layout')

@section('title', 'Transactions List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Transactions List</h4>
        {{-- <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary">Add Transaction <i class="bx bx-plus"></i></a> --}}
    </div>

    <div class="row">
        <div class="col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped data-table">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>Payment Amount</th>
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
                columns: [
                    {
                        data: 'reference_no',
                        name: 'reference_no'
                    },
                    {
                        data: 'payment_amount',
                        name: 'payment_amount'
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
