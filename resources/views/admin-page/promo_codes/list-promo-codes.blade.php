@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off - Promo Codes List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Promo Codes List</h4>
        <a href="{{ route('admin.promo_codes.create') }}" class="btn btn-primary">Add Promo Code <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive-lg text-nowrap">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Need Requirements?</th>
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
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.promo_codes.list') }}"
                },  
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'is_need_requirement',
                        name: 'is_need_requirement'
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