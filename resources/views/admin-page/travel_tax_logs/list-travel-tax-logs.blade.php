@extends('layouts.admin.layout')

@section('title', 'Travel Tax Logs')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Travel Tax Logs</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Travel Tax Logs</h6>
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
                                        <th>Id</th>
                                        <th>Travel Tax AR</th>
                                        <th>Status Code</th>
                                        <th>Submission Date</th>
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
                    url: "{{ route('admin.travel_tax_logs.list') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'ar_number',
                        name: 'ar_number'
                    },
                    {
                        data: 'status_code',
                        name: 'status_code'
                    },
                    {
                        data: 'date_of_submission',
                        name: 'date_of_submission'
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
