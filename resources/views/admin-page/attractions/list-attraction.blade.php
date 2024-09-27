@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Attractions List')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Attractions</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Attractions</h6>
            </div>
            <div class="action-section btn-group">
                <a href="{{ route('admin.attractions.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i>
                    Add Attraction</a>
                <button class="btn btn-dark btn-sm" id="featured-attractions-btn" data-bs-toggle="modal"
                    data-bs-target="#featured-attractions">
                    <i class='bx bx-list-ol'></i> Featured Attractions
                </button>
            </div>
            <div class="modal fade" id="featured-attractions" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom pb-4">
                            <h5 class="modal-title" id="modalCenterTitle">Featured Attractions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 border-bottom py-4">
                                <label for="organization-field" class="form-label">Organization</label>
                                <select name="organization_id" id="organization-select-field" class="form-select">
                                    @foreach ($organizations as $organization)
                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="demo-inline-spacing">
                                <ul class="list-group" id="featured-attractions-list"></ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-bordered-bottom data-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th width="75">Organization</th>
                                <th>Name</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script>
        function loadTable() {
            let table = $('.data-table').DataTable({
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.attractions.list') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'organization_logo',
                        name: 'organization_logo'
                    },
                    {
                        data: 'name',
                        name: 'name'
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

                columnDefs: [{
                    targets: [1, 4], // Index of the column you want to disable sorting for
                    orderable: false
                }],
                order: [
                    [0, 'desc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove attraction from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.attractions.destroy') }}",
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

        $('#featured-attractions').on("shown.bs.modal", function(e) {
            let organization_id = $("#organization-select-field").val();
            fetchFeaturedAttractions(organization_id);
        });

        $('#organization-select-field').change(function(e) {
            let organization_id = e.target.value;
            fetchFeaturedAttractions(organization_id);
        })

        let sortableInstance; // Declare a variable to store the Sortable instance
        let currentSortedIds = [];
        const fetchFeaturedAttractions = (organization_id) => {
            $.ajax({
                method: "GET",
                url: `/admin/attractions/featured/organizations/${organization_id}`,
                success: function(response) {
                    let featured_attractions_list = document.querySelector("#featured-attractions-list");
                    let attractions = response.attractions;
                    let output = "";

                    if (attractions.length > 0) {
                        attractions.forEach(attraction => {
                            currentSortedIds.push(attraction.id.toString());
                            output += `<li class="list-group-item d-flex align-items-center" data-id="${attraction.id}">
                                        ${attraction.name}
                                    </li>`;
                        });
                    } else {
                        output += `<li class="list-group-item d-flex align-items-center">
                                        No Attraction Found
                                    </li>`;
                    }
                    featured_attractions_list.innerHTML = output;

                    // Destroy existing sortable instance (if any) before creating a new one
                    if (sortableInstance) {
                        sortableInstance.destroy();
                    }

                    // Initialize SortableJS for draggable functionality
                    sortableInstance = new Sortable(featured_attractions_list, {
                        animation: 150,
                        ghostClass: 'sortable-ghost', // Class applied to the dragging item
                        onEnd: function (event) {
                            let sortedIDs = [...featured_attractions_list.children].map(li => li
                                .dataset.id);
                            if(JSON.stringify(currentSortedIds) != JSON.stringify(sortedIDs)) {
                                saveFeaturedAttractions(sortedIDs);
                                currentSortedIds = sortedIDs;
                            }
                        }
                    });
                }
            })
        }

        const saveFeaturedAttractions = (sortedIDs) => {


            $.ajax({
                url: "/admin/attractions/featured",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    sorted_ids: sortedIDs,
                },
                success: function (response) {
                    console.log(response);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        loadTable();
    </script>
@endpush
