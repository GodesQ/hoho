@extends('layouts.admin.layout')

@section('title', 'Travel Taxes List - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Travel Taxes List</h4>
            <a href="{{ route('admin.travel_taxes.create') }}" class="btn btn-primary">Add Travel Tax <i
                    class="bx bx-plus"></i></a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Transaction Number</th>
                                <th>Reference Number</th>
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
        function loadTable() {
            let table = $('.data-table').DataTable({
                processing: true,
                pageLength: 25,
                responsive: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.travel_taxes.list') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'transaction_number',
                        name: 'transaction_number'
                    },
                    {
                        data: 'reference_number',
                        name: 'reference_number'
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
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            });
        }

        loadTable();
    </script>
@endpush
