@extends('layouts.admin.layout')

@section('title', 'Test Bus Location')

@section('content')
    <style>
        #map {
            height: 400px;
            width: 100%;
            border: 1px solid #000;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Test Location</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="transport" class="form-label">Bus</label>
                            <select name="transport_id" id="transport" class="form-select">
                                <option value="">--- SELECT TRANSPORT---</option>
                                @forelse ($transports as $transport)
                                    <option value="{{ $transport->id }}">{{ $transport->name }}</option>
                                @empty
                                    <option value="">No Transport Found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="origin" class="form-label">Origin</label>
                            <select name="origin" id="origin" class="form-select"></select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <select name="destination" id="destination" class="form-select"></select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div id="map"></div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary w-100" id="get-route-btn">Get the Routes</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#transport').change(function(e) {
            let transport_id = e.target.value;
            $.ajax({
                url: '{{ route('admin.transports.get_transport_attractions', '') }}' + '/' + transport_id,
                method: "GET",
                success: function(data) {
                    let attractions = data.assigned_tour.attractions;
                    let originSelect = $('#origin');
                    let destinationSelect = $('#destination');

                    originSelect.children('option').remove()
                    destinationSelect.children('option').remove();

                    attractions.forEach((attraction, index) => {
                        let address = attraction.address;
                        let name = attraction.name;

                        // Create option element for origin
                        let originOption = $('<option>').text(name + ' - ' + address).val(
                            address);
                        originSelect.append(originOption);

                        // Create option element for destination
                        let destinationOption = $('<option>').text(name + ' - ' + address).val(
                            address);
                        destinationSelect.append(destinationOption);

                        // Default select the second option in destination if it's not the first attraction
                        if (index === 1) {
                            destinationOption.prop('selected', true);
                        }
                    });
                }
            });
        })
    </script>

    <script>
        let map, operatorMarker;
        let isActive = true;

        async function initMap() {
            const busIcon = {
                url: "{{ URL::asset('assets/img/icons/bus.png') }}",
                scaledSize: new google.maps.Size(40, 40),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 40)
            };
            createMapAndMarker(busIcon);

            // Fetch directions and simulate movement
            // getLocalDirections();
        }

        $('#get-route-btn').click(function(e) {
            getLocalDirections();
        })

        function createMapAndMarker(icon) {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 14.5889842,
                    lng: 120.9768261
                },
                zoom: 20,
                tilt: 45,
            });

            operatorMarker = new google.maps.Marker({
                map,
                position: {
                    lat: 14.5889842,
                    lng: 120.9768261
                },
                title: 'Bus Operator',
                icon: icon
            });
        }

        function resetMapAndMarker() {
            operatorMarker.setMap(null); // Remove the marker

            const busIcon = {
                url: "{{ URL::asset('assets/img/icons/bus.png') }}",
                scaledSize: new google.maps.Size(50, 50),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 40)
            };

            createMapAndMarker(busIcon); // Recreate the map and marker
        }

        function getLocalDirections() {
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                polylineOptions: {
                    strokeColor: '#b03717', // Customize the color of the polyline
                    strokeWeight: 2, // Customize the stroke weight of the polyline
                    strokeOpacity: 0.8 // Customize the opacity of the polyline
                    // You can add more styling properties here
                }
            });

            directionsService.route({
                    origin: $('#origin').val(),
                    destination: $('#destination').val(),
                    travelMode: google.maps.TravelMode.DRIVING
                },
                function(response, status) {
                    if (status === 'OK') {
                        // Get the route's overview path (waypoints)
                        directionsRenderer.setDirections(response); // Display the route on the map
                        const waypoints = response.routes[0].overview_path;
                        // Animate marker movement along waypoints
                        animateMarkerMovement(waypoints);
                    } else {
                        console.log('Directions request failed:', status);
                    }
                }
            );
        }

        function animateMarkerMovement(waypoints) {
            let currentIndex = 0;
            let polylines = []; // Array to store the polylines

            function moveMarker() {
                if (currentIndex < waypoints.length) {
                    const currentLocation = waypoints[currentIndex];
                    const nextLocation = waypoints[currentIndex + 1];
                    if (nextLocation) {
                        const rotation = google.maps.geometry.spherical.computeHeading(currentLocation, nextLocation);
                        const mapHeading = google.maps.geometry.spherical.computeHeading(currentLocation, nextLocation);
                        map.setHeading(mapHeading); // Set the map's heading to match the rotation angle
                    }

                    operatorMarker.setPosition(currentLocation);
                    map.panTo(currentLocation);

                    sendToServer(currentLocation.lat(), currentLocation.lng());

                    currentIndex++;
                    setTimeout(moveMarker, 4000); // Move to the next location every 2 seconds
                }
            }
            moveMarker();
        }

        function sendToServer(latitude, longitude) {
            $.ajax({
                url: '{{ route('admin.transports.updateLocation') }}', // Update with your backend endpoint
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: JSON.stringify({
                    id: $('#transport').val(),
                    latitude: latitude,
                    longitude: longitude
                }),
                success: function(response) {
                    console.log('Bus location sent to server:', latitude, longitude);
                },
                error: function(error) {
                    console.error('Error sending bus location:', error);
                }
            });
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcjk7zETwVNURwNgnIidjFDQzVcjGaqVU&libraries=places&callback=initMap">
    </script>
@endpush
