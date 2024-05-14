@extends('layouts.admin.layout')

@section('title', 'Consumer API Logs - Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <section class="section-header d-flex justify-content-between align-items-center">
        <div class="title-section">
            <h4 class="fw-medium mb-2">API Logs</h4>
            <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                    class="text-muted fw-light">Dashboard /</a> API Logs</h6>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="table table-borderless table-striped data-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Consumer</th>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
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
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.consumer_api_logs.list') }}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'consumer',
                        name: 'consumer',
                    },
                    {
                        data: 'http_method',
                        name: 'http_method',
                    },
                    {
                        data: 'request_path',
                        name: 'request_path',
                    },
                    {
                        data: 'request_timestamp',
                        name: 'request_timestamp',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                    }
                ],
            });
        }

        loadTable();
    </script>
@endpush