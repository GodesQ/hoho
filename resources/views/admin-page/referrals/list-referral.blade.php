@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Referrals List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Referrals List</h4>
        <a href="{{ route('admin.referrals.create') }}" class="btn btn-primary">Add Referral <i class="bx bx-plus"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped data-table">
                    <thead>
                        <tr>
                            <th>Referral Name</th>
                            <th>Referral Code</th>
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
                    url: "{{ route('admin.referrals.list') }}"
                },
                columns: [
                    {
                        data: 'referral_name',
                        name: 'referral_name',
                    },
                    {
                        data: 'referral_code',
                        name: 'referral_code',
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            })
        }

        loadTable();
    </script>
@endpush
