@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Transports List')

@section('content')
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Transports List</h4>
            <a href="{{ route('admin.transports.create') }}" class="btn btn-primary">Add Transport <i
                    class="bx bx-plus"></i></a>
        </div>

        <div class="card my-3">
            <div class="card-body">
                <div class="mb-3 w-20">
                    <label for="" class="form-label font-weight-bold">Select Transport</label>
                    <select name="" id="transport" class="form-select">
                        <option value="">--- SELECT TRANSPORT ---</option>
                        @forelse ($transports as $transport)
                            <option value="{{ $transport->id }}">{{ $transport->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div id="map"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped data-table">
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
                ]
            })
        }
        loadTable();
    </script>

    <script>
        let map, operatorMarker, INITIAL_LATITUDE, INITIAL_LONGITUDE;

        function initMap() {
            INITIAL_LATITUDE = 14.5889842;
            INITIAL_LONGITUDE = 120.9768261;

            const busIcon = {
                url: "{{ URL::asset('assets/img/icons/bus.png') }}", // Replace with the actual path to your icon image
                scaledSize: new google.maps.Size(50, 50), // Adjust the size of the icon
                origin: new google.maps.Point(0, 0), // Icon origin
                anchor: new google.maps.Point(20, 40) // Anchor point
            };

            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: INITIAL_LATITUDE,
                    lng: INITIAL_LONGITUDE
                },
                zoom: 16,
                tilt: 45
            });

            operatorMarker = new google.maps.Marker({
                map,
                position: {
                    lat: INITIAL_LATITUDE,
                    lng: INITIAL_LONGITUDE
                },
                title: 'Bus Operator',
                icon: busIcon // Set the custom icon for the marker
            });

            simulateMovement();
        }
        let lastTimestamp = Date.now();
        let isActive = true;

        document.addEventListener('visibilitychange', () => {
            isActive = !document.hidden;
            console.log(isActive);
            if (isActive) {
                // If the tab becomes active again, simulate movement immediately
                simulateMovement();
            }
        });

        const SPEED_KMH = 60; // Speed of the bus in km/h

        // Convert speed from km/h to m/s
        const SPEED_MS = SPEED_KMH * 1000 / 3600; // Convert to meters per second
        const TIME_INTERVAL = 1; // 1 second interval

        function simulateMovement() {
            // If the tab is not active, simulate the movement based on time
            const elapsedTime = Date.now() - lastTimestamp;
            const distanceTraveled = (SPEED_MS * elapsedTime) / 1000;
            const latitudeChange = distanceTraveled / 111111;
            const longitudeChange = distanceTraveled / (111111 * Math.cos(INITIAL_LATITUDE * Math.PI / 180));

            INITIAL_LATITUDE += latitudeChange;
            INITIAL_LONGITUDE += longitudeChange;

            if (!isActive) {
                const newLocation = {
                    lat: INITIAL_LATITUDE,
                    lng: INITIAL_LONGITUDE
                };

                updateMarkerPosition(newLocation);
                map.panTo(newLocation);

                lastTimestamp = Date.now();
            } else {
                const newLocation = {
                    lat: INITIAL_LATITUDE,
                    lng: INITIAL_LONGITUDE
                };
                const timestamp = Date.now();
                updateMarkerPosition(newLocation, timestamp);
                map.panTo(newLocation);

                lastTimestamp = timestamp;

                setTimeout(simulateMovement, 2000); // Update every 2 seconds
            }
        }
        // let previousLocation = null;
        // let previousTimestamp = null;

        function updateMarkerPosition(newLocation, timestamp) {
            previousLocation = newLocation;
            operatorMarker.setPosition(newLocation);
        }
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEmTK1XpJ2VJuylKczq2-49A6_WuUlfe4&libraries=places&callback=initMap">
    </script>
@endpush
