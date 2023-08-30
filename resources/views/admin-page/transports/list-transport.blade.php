@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Transports List')

@section('content')
    <style>
        #map {
            height: 500px;
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
                    <input type="hidden" name="user_id" id="user_id" value="{{ auth('admin')->user()->id }}">
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
        let map, operatorMarker;
        let isActive = true;

        function initMap() {
            const busIcon = {
                url: "{{ URL::asset('assets/img/icons/marker.png') }}",
                scaledSize: new google.maps.Size(30, 30),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 40)
            };
            createMapAndMarker(busIcon);

            // Fetch directions and simulate movement
            if ('{{ env('APP_ENVIRONMENT') }}' === 'TEST') {
                getLocalDirections();
            }
        }

        // $('#transport').on('change', function(e) {
        //     setUpPusher();
        // })

        function setUpPusher() {

        }
        // Set up Pusher to watch real-time updates
        let backendBaseUrl = "http://127.0.0.1:8000";

        Pusher.logToConsole = true;

        var pusher = new Pusher('aa9ec307de589143a5bc', {
            cluster: 'ap1',
        });

        let user_id = document.querySelector('#user_id');

        var channel = pusher.subscribe('bus-location');
        channel.bind('new-bus-location', function(data) {
            console.log(data.transport_id);
            if(data.transport_id ==  $('#transport').val()) {
                const newLocation = {
                    lat: data.coordinates.latitude,
                    lng: data.coordinates.longitude
                };
                operatorMarker.setPosition(newLocation);
                map.panTo(newLocation);
            }
        });

        channel.bind('pusher:subscription_succeeded', function(members) {});
        channel.bind('pusher:subscription_error', function(data) {});

        let lastTimestamp = Date.now();

        function createMapAndMarker(icon) {
            const mapStyles = [{
                    "featureType": "all",
                    "elementType": "all",
                    "stylers": [{
                            "visibility": "simplified"
                        },
                        {
                            "hue": "#ff0000"
                        }
                    ]
                },
                {
                    "featureType": "administrative",
                    "elementType": "labels.text",
                    "stylers": [{
                        "color": "#eb452f"
                    }]
                },
                {
                    "featureType": "administrative.country",
                    "elementType": "labels.text",
                    "stylers": [{
                        "color": "#2c2c2c"
                    }]
                },
                {
                    "featureType": "administrative.country",
                    "elementType": "labels.text.fill",
                    "stylers": [{
                        "color": "#606060"
                    }]
                },
                {
                    "featureType": "administrative.locality",
                    "elementType": "geometry.fill",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "administrative.locality",
                    "elementType": "labels",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "administrative.locality",
                    "elementType": "labels.text",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "administrative.neighborhood",
                    "elementType": "geometry.fill",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "administrative.neighborhood",
                    "elementType": "labels.text",
                    "stylers": [{
                            "color": "#eb452f"
                        },
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "administrative.neighborhood",
                    "elementType": "labels.text.fill",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "landscape.man_made",
                    "elementType": "geometry.fill",
                    "stylers": [{
                        "color": "#fff1f1"
                    }]
                },
                {
                    "featureType": "landscape.man_made",
                    "elementType": "labels.text",
                    "stylers": [{
                            "hue": "#ff0000"
                        },
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "landscape.man_made",
                    "elementType": "labels.icon",
                    "stylers": [{
                        "color": "#eb452f"
                    }]
                },
                {
                    "featureType": "poi",
                    "elementType": "geometry.fill",
                    "stylers": [{
                        "color": "#ffd6d6"
                    }]
                },
                {
                    "featureType": "poi",
                    "elementType": "labels",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "poi",
                    "elementType": "labels.text",
                    "stylers": [{
                        "color": "#eb452f"
                    }]
                },
                {
                    "featureType": "road",
                    "elementType": "labels",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "transit.line",
                    "elementType": "geometry.fill",
                    "stylers": [{
                            "saturation": "-7"
                        },
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry.fill",
                    "stylers": [{
                            "visibility": "on"
                        },
                        {
                            "color": "#521707"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "labels",
                    "stylers": [{
                        "visibility": "on"
                    }]
                }
            ];

            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 14.5889842,
                    lng: 120.9768261
                },
                zoom: 15,
                tilt: 45,
                styles: mapStyles
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
                url: "{{ URL::asset('assets/img/icons/marker.png') }}",
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
                    strokeWeight: 3, // Customize the stroke weight of the polyline
                    strokeOpacity: 0.8 // Customize the opacity of the polyline
                    // You can add more styling properties here
                }
            });

            directionsService.route({
                    origin: "Binondo, Manila, Metro Manila, Philippines",
                    destination: "National Museum of Anthropology, Teodoro F. Valencia Circle, Ermita, Manila, Metro Manila, Philippines",
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
                        operatorMarker.setIcon({
                            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                            scale: 6,
                            fillColor: "#29477d",
                            fillOpacity: 1,
                            strokeWeight: 0,
                            rotation: rotation
                        });
                    }

                    operatorMarker.setPosition(currentLocation);
                    // map.panTo(currentLocation);

                    currentIndex++;
                    setTimeout(moveMarker, 2000); // Move to the next location every 2 seconds
                }
            }
            moveMarker();
        }

        // setUpPusher();
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEmTK1XpJ2VJuylKczq2-49A6_WuUlfe4&libraries=places&callback=initMap">
    </script>
@endpush
