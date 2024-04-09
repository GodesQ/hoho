@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Transports List')

@section('content')
    <style>
        #map {
            width: 100%;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">


        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Transports List</h4>
            <a href="{{ route('admin.transports.create') }}" class="btn btn-primary">Add Transport <i
                    class="bx bx-plus"></i></a>
        </div>

        <div class="card my-1">
            <div class="card-body">
                <div class="d-flex app-transports-fleet-wrapper">
                    <div class="flex-shrink-0 position-fixed m-4 d-md-none w-auto z-1">
                        <button class="btn btn-label-white border border-2 z-2 p-2" data-bs-toggle="sidebar" data-overlay=""
                            data-target="#app-logistics-fleet-sidebar"><i class="bx bx-menu"></i></button>
                    </div>

                    <div class="app-transports-fleet-sidebar col h-100" id="app-transports-fleet-sidebar">
                        <!-- Sidebar when screen < md -->
                        <div class="card-body p-0 logistics-fleet-sidebar-body ps">
                            <!-- Menu Accordion -->
                            <div class="accordion p-2" id="fleet" data-bs-toggle="sidebar" data-overlay=""
                                data-target="#app-logistics-fleet-sidebar">
                                <!-- Fleet 1 -->

                            </div>
                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                            </div>
                            <div class="ps__rail-y" style="top: 0px; right: 0px;">
                                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col h-100 map-container">
                        <div id="map" class="w-100 h-100"></div>
                    </div>

                </div>
            </div>
        </div>

        {{-- <div class="card">
            <div class="card-body">
                <div class="table-responsive-lg text-nowrap">
                    <table class="table   data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Transport Provider</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> --}}
    </div>
@endsection

@push('scripts')
    {{-- <script>
        function loadTable() {
            let table = $('.data-table').DataTable({
                processing: true,
                pageLength: 10,
                responsive: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.transports.list') }}"
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'transport_provider',
                        name: 'transport_provider'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],

                columnDefs: [{
                    targets: [3], // Index of the column you want to disable sorting for
                    orderable: false
                }],
                order: [
                    [0, 'asc'] // Sort by the first column (index 0) in descending order
                ]
            })
        }

        $(document).on("click", ".remove-btn", function(e) {
            let id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove tour from list",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.transports.destroy') }}",
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
        loadTable();
    </script> --}}
    <script>
        let map;
        async function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 14.5889842,
                    lng: 120.9768261
                },
                zoom: 13,
                tilt: 45,
            });
        }

        function createMarker(latitude, longitude, name) {
            const busIcon = {
                url: "{{ URL::asset('assets/img/icons/bus.png') }}",
                scaledSize: new google.maps.Size(40, 40),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 40)
            };

            new google.maps.Marker({
                map,
                position: {
                    lat: parseFloat(latitude),
                    lng: parseFloat(longitude)
                },
                title: name,
                icon: busIcon
            });
        }

        function getAddressFromLatLng(latitude, longitude, apiKey) {
            return new Promise((resolve, reject) => {
                const url =
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            resolve(data.results[0].formatted_address);
                        } else {
                            reject("Address not found");
                        }
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        }

        $.ajax({
            url: "{{ route('admin.transports.lookup') }}",
            method: 'GET',
            success: function(data) {
                let html = '';
                data.transports.forEach(transport => {
                    createMarker(transport.latitude, transport.longitude, transport.name);
                    let current_stop = JSON.parse(transport.current_location);
                    let next_stop = JSON.parse(transport.next_location);
                    html += `<div class="accordion-item shadow-none border-0 mb-0" id="fl-1">
                                    <div class="accordion-header" id="fleetOne">
                                        <div role="button" class="accordion-button shadow-none" data-bs-toggle="collapse"
                                            data-bs-target="#fleet${transport.id}" aria-expanded="true" aria-controls="fleet${transport.id}">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-wrapper">
                                                    <div class="avatar me-3">
                                                        <span class="avatar-initial rounded-circle bg-primary"><i
                                                                class="bx bxs-bus text-white"></i></span>
                                                    </div>
                                                </div>
                                                <span class="d-flex flex-column">
                                                    <span class="h6 mb-0">${transport.name}</span>
                                                    <span class="text-muted">${transport.type}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fleet${transport.id}" class="accordion-collapse collapse " data-bs-parent="#fleet">
                                        <div class="accordion-body pb-0">
                                            <div class="d-flex justify-content-start mb-3">
                                                <div class="btn-group">
                                                    <a href="/admin/transports/edit/${transport.id}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i> Edit</a>
                                                    <a href="#" class="btn btn-sm btn-danger "><i class="bx bx-trash"></i> Delete</a>
                                                </div>
                                            </div>
                                            <div class="my-1 mb-3">
                                                <h5 class="small text-primary">Current Stop</h5>
                                                <div class="d-flex gap-2">
                                                    <i class="bx bx-location-plus"></i>
                                                    <div>
                                                        <h6 class="mb-1">${current_stop.address}
                                                        </h6>
                                                        <small><span style="font-weight: bold">Latitude:</span>
                                                            ${current_stop.latitude}</small> <br>
                                                        <small><span style="font-weight: bold">Longitude:</span>
                                                            ${current_stop.longitude}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-1 mb-3">
                                                <h5 class="small text-primary">Next Stop</h5>
                                                <div class="d-flex gap-2">
                                                    <i class="bx bx-location-plus"></i>
                                                    <div>
                                                        <h6 class="mb-1">${next_stop.address}
                                                        </h6>
                                                        <small><span style="font-weight: bold">Latitude:</span>
                                                            ${next_stop.latitude}</small> <br>
                                                        <small><span style="font-weight: bold">Longitude:</span>
                                                            ${next_stop.longitude}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                });
                $('#fleet').html(html);
            }
        })
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcjk7zETwVNURwNgnIidjFDQzVcjGaqVU&libraries=places&callback=initMap">
    </script>
@endpush
