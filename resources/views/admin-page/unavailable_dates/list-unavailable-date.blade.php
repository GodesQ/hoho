@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Unavailable Dates List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Unavailable Dates List</h4>
        <a href="{{ route('admin.transports.create') }}" class="btn btn-primary">Add Unavailable Dates <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive table-wrap">
                <table class="table data-table">

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
                pageLength: 10,
                responsive: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.unavailable_dates.list') }}",
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'unavailable_date',
                        name: 'unavailable_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ]
            });
        }

        loadTable();
    </script>
@endpush