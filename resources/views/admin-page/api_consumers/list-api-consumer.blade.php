@extends('layouts.admin.layout')

@section('title', 'List API Consumer - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">API Consumers</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> API Consumers</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.unavailable_dates.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add
                    API Consumer</a>
            </div>
        </section>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive data-wrap">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Consumer Name</th>
                                <th>Platform</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function loadTable() {
                let table = $('.data-table').DataTable({
                    processing: true,
                    pageLength: 10,
                    responsive: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.api_consumers.list') }}"
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                        },
                        {
                            data: 'consumer_name',
                            name: 'consumer_name',
                        },
                        {
                            data: 'platform',
                            name: 'platform',
                        },
                        {
                            data: 'status',
                            name: 'status',
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ]
                });
            }

            loadTable();
        </script>
    @endpush
@endsection
