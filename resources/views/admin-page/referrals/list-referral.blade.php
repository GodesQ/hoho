@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Referrals List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Referrals List</h4>
        <div>
            <a href="{{ route('admin.referrals.create') }}" class="btn btn-primary">Add Referral <i class="bx bx-plus"></i></a>
            <a href="#" class="btn btn-dark" onclick="downloadCSV()">Download CSV <i class="bx bx-download"></i></a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive-lg text-nowrap">
                <table class="table data-table">
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
                serverSide: false,
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
                ],

                columnDefs: [
                    {
                    targets: [2], // Index of the column you want to disable sorting for
                    orderable: false
                    }
                ],
                order: [
                    [0, 'asc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove referral from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.referrals.destroy') }}",
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Removed!', response.message, 'success').then(
                                    result => {
                                        if (result.isConfirmed) {
                                            toastr.success(response.message, 'Success');
                                            location.reload();
                                        }
                                    })
                            }
                        }
                    })
                }
            })
        });

        function downloadCSV() {
            $.ajax({
                url: '{{ route("admin.referrals.generate_csv") }}', // Replace with your actual route URL
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // Set the response type to blob
                },
                success: function(data, status, xhr) {
                    const url = window.URL.createObjectURL(data);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'referral_data.csv';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        loadTable();
    </script>
@endpush
