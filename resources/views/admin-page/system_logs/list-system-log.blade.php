@extends('layouts.admin.layout')

@section('title', 'System Logs - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">System Logs</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> System Logs</h6>
            </div>
        </section>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless data-table w-100">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>Log At</th>
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
        let table = $('.data-table').DataTable({
            lengthChange: false,
            processing: true,
            pageLength: 25,
            responsive: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.system_logs.list') }}"
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'user',
                    name: 'user',
                },
                {
                    data: 'action',
                    name: 'action',
                },
                {
                    data: 'model',
                    name: 'model',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
            ]
        });
    </script>
@endpush
