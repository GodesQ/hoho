@extends('layouts.admin.layout')

@section('title', 'Consumer API Logs - Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Consumer API Logs</h4>
    </div>

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