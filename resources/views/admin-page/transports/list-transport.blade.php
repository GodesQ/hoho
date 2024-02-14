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

        {{-- <div class="card my-3">
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
        </div> --}}

        <div class="card">
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

                columnDefs: [
                    {
                    targets: [3], // Index of the column you want to disable sorting for
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
    </script>

    {{-- <script>
        let map, operatorMarker;
        let isActive = true;

        function initMap() {
            const busIcon = {
                url: "{{ URL::asset('assets/img/icons/bus.png') }}",
                scaledSize: new google.maps.Size(40, 40),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 40)
            };
            createMapAndMarker(busIcon);

            // Fetch directions and simulate movement
            if ('{{ env('APP_ENVIRONMENT') }}' === 'TEST') {
                // getLocalDirections();
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
            // Convert latitude and longitude to numbers
            const latitude = parseFloat(data.coordinates.latitude);
            const longitude = parseFloat(data.coordinates.longitude);

            if (!isNaN(latitude) && !isNaN(longitude) && data.transport_id == $('#transport').val()) {
                const newLocation = {
                    lat: latitude,
                    lng: longitude
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
                    "featureType": "administrative",
                    "elementType": "all",
                    "stylers": [{
                        "saturation": "-100"
                    }]
                },
                {
                    "featureType": "administrative.province",
                    "elementType": "all",
                    "stylers": [{
                        "visibility": "off"
                    }]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [{
                            "saturation": -100
                        },
                        {
                            "lightness": 65
                        },
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "landscape.man_made",
                    "elementType": "geometry",
                    "stylers": [{
                            "visibility": "on"
                        },
                        {
                            "color": "#f2f0f3"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [{
                            "saturation": -100
                        },
                        {
                            "lightness": "50"
                        },
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "geometry",
                    "stylers": [{
                            "color": "#eaeaea"
                        },
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [{
                            "saturation": "-100"
                        },
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "labels",
                    "stylers": [{
                        "visibility": "simplified"
                    }]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [{
                        "visibility": "simplified"
                    }]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#ffffff"
                    }]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "all",
                    "stylers": [{
                        "lightness": "30"
                    }]
                },
                {
                    "featureType": "road.local",
                    "elementType": "all",
                    "stylers": [{
                        "lightness": "40"
                    }]
                },
                {
                    "featureType": "road.local",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#ffffff"
                    }]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [{
                            "saturation": -100
                        },
                        {
                            "visibility": "simplified"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [{
                            "lightness": -25
                        },
                        {
                            "saturation": "100"
                        },
                        {
                            "color": "#b1d6fa"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "labels",
                    "stylers": [{
                            "lightness": -25
                        },
                        {
                            "saturation": -100
                        }
                    ]
                }
            ];


            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 14.5889842,
                    lng: 120.9768261
                },
                zoom: 20,
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
                    origin: "Binondo, Manila, Metro Manila, Philippines",
                    destination: "National Museum of Anthropology, Teodoro F. Valencia Circle, Ermita, Manila, Metro Manila, Philippines",
                    travelMode: google.maps.TravelMode.DRIVING
                },
                function(response, status) {
                    if (status === 'OK') {
                        // Get the route's overview path (waypoints)
                        directionsRenderer.setDirections(response); // Display the route on the map
                        const waypoints = response.routes[0].overview_path;
                        console.log(waypoints);
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
                        // operatorMarker.setIcon({
                        //     // url: "{{ URL::asset('assets/img/icons/car.svg') }}",
                        //     // path: 'M0.618585 68.2177C0.763501 68.228 2.50138 68.1826 3.06443 68.1449L3.4506 68.131L3.47603 69.5463L3.50146 70.9615L4.14572 71.0365C9.09055 71.6267 21.9495 71.5646 26.9828 70.9312L28.1245 70.7878L28.0828 69.3387C28.0419 68.0255 28.0572 67.9009 28.3151 67.9558C28.4602 67.9888 29.1363 68.0183 29.7963 68.0366L31.0035 68.0626L30.8714 40.7065C30.7326 14.7658 30.7071 13.3392 30.4485 13.1711C30.1737 13.003 30.158 13.0484 30.2131 14.0898C30.3044 15.7311 29.7264 23.2649 29.4724 23.7987C29.3139 24.1621 29.1537 24.2764 28.7038 24.3926C28.3503 24.4742 28.0767 24.4873 27.9797 24.42C27.8827 24.3527 27.8107 23.1982 27.815 21.3978C27.798 18.8275 27.8585 18.2383 28.2326 16.3902C28.8559 13.2948 28.9983 12.9202 29.5304 13.0639C29.6593 13.097 29.7557 13.0624 29.7552 12.9945C29.7548 12.9265 29.6739 12.8704 29.5935 12.871C29.4969 12.8716 29.4315 12.7135 29.4302 12.521C29.428 12.1813 29.444 12.1699 30.0881 12.211L30.7482 12.2519L30.6438 8.61782C30.5823 6.62533 30.4903 4.87083 30.4249 4.71274C30.3595 4.55464 30.1488 4.34089 29.9389 4.24037C29.5675 4.05032 29.5514 4.05043 29.0708 4.38197C28.6703 4.66769 28.4776 4.72558 27.8177 4.72993C26.9648 4.73555 26.384 4.53556 25.5271 3.94108C24.6055 3.29041 23.4377 1.92799 23.0293 1.0135C22.9315 0.821652 21.6262 0.569824 19.6935 0.367425C18.3407 0.229143 12.3056 0.268937 11.0674 0.424304C8.91256 0.698948 7.75532 0.933044 7.69214 1.11463C7.42288 1.77316 6.19361 3.28726 5.42492 3.86981C4.36812 4.69206 3.88659 4.88773 2.90488 4.8942C2.29332 4.89823 2.06764 4.8431 1.67953 4.56258C1.19456 4.2374 1.17846 4.23751 0.80958 4.43244C0.601035 4.53572 0.376994 4.7297 0.313441 4.85467C0.249888 4.97965 0.180993 6.73521 0.16217 8.76219L0.106041 12.4539L0.765579 12.4043C1.40902 12.3548 1.42519 12.366 1.42743 12.7057C1.42877 12.9095 1.34928 13.0572 1.2206 13.0694C1.12404 13.07 1.18872 13.1149 1.3983 13.1701C1.91427 13.3139 2.02872 13.5849 2.60961 16.2534C3.07464 18.4357 3.12606 18.9109 3.15962 21.5604C3.18766 23.3719 3.13075 24.5046 3.03464 24.5732C2.93852 24.6418 2.66493 24.6436 2.34261 24.5777C1.42416 24.414 1.358 24.1426 0.973849 19.5819C0.781962 17.3298 0.637685 14.9755 0.665691 14.3412C0.706804 13.2539 0.690337 13.1974 0.417867 13.3691C0.161489 13.5406 0.155027 15.0014 0.3257 40.8852C0.424774 55.9105 0.554136 68.2068 0.618585 68.2177ZM1.44066 26.9163C1.80827 26.5289 2.54746 26.3541 3.12795 26.5202C4.17604 26.819 4.08572 25.3249 4.22033 45.7399C4.30679 58.8516 4.29517 64.4114 4.16754 64.5821C4.0718 64.7073 3.75105 64.8793 3.44587 64.9719C2.98005 65.1108 2.81911 65.1119 2.3515 64.9791C2.04513 64.8905 1.72221 64.7341 1.62483 64.6102C1.51128 64.4751 1.42567 58.814 1.32364 45.7816C1.2171 29.624 1.23279 27.1215 1.44066 26.9163ZM2.43756 9.68702C2.8203 9.15231 3.46232 8.88764 5.13396 8.55956C10.1972 7.58635 17.068 7.35987 22.5926 8.00284C26.3293 8.43113 27.7802 8.80655 28.348 9.4822C28.8993 10.1127 28.8131 11.6759 28.105 14.1263C27.2872 17.0192 27.2109 17.6538 27.2367 21.5715C27.2526 23.9832 27.2118 25.1158 27.0831 25.1167C26.9704 25.1174 26.914 23.8835 26.9283 21.1772L26.9346 17.248L25.9846 17.175C20.848 16.8012 19.4474 16.7425 15.3758 16.7693C11.3202 16.7961 9.05166 16.9129 4.83762 17.3144L3.95299 17.3995L4.00501 17.9653C4.07513 18.8367 3.78276 18.431 3.56637 17.3454C3.40036 16.5765 2.80621 14.3385 2.16408 12.146C2.01558 11.5921 2.16628 10.0398 2.43756 9.68702ZM5.19293 66.3192C5.40192 66.2838 6.46432 66.3108 7.57523 66.3714C10.2478 66.5123 20.5638 66.4443 23.5561 66.266C26.5806 66.0762 26.7256 66.0979 26.7298 66.732C26.736 67.6718 26.6234 67.6838 19.7048 67.9786C16.4064 68.1249 14.9097 68.1347 11.7385 68.0311C4.94536 67.8268 4.84865 67.8048 4.84253 66.8763C4.83984 66.4687 4.90362 66.3777 5.19293 66.3192ZM27.3517 26.8134C27.6867 26.3582 28.5385 26.1714 29.1838 26.405C29.458 26.4938 29.7488 26.6617 29.83 26.7744C30.041 27.0447 30.021 26.4447 30.156 46.9276L30.2552 64.4101L29.7907 64.7416C29.0858 65.2331 28.0067 65.127 27.5682 64.5184C27.4057 64.293 27.3316 60.3643 27.2348 45.69C27.1288 29.6117 27.1444 27.0978 27.3517 26.8134Z',
                        //     scale: .8,
                        //     fillColor: "#7f1318",
                        //     fillOpacity: 2,
                        //     strokeWeight: 1,
                        //     rotation: rotation,
                        // });

                        const mapHeading = google.maps.geometry.spherical.computeHeading(currentLocation, nextLocation);
                        map.setHeading(mapHeading); // Set the map's heading to match the rotation angle
                    }

                    operatorMarker.setPosition(currentLocation);
                    map.panTo(currentLocation);
                    map.setZoom(17);

                    currentIndex++;
                    setTimeout(moveMarker, 2000); // Move to the next location every 2 seconds
                }
            }
            moveMarker();
        }

        // setUpPusher();
    </script> --}}
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcjk7zETwVNURwNgnIidjFDQzVcjGaqVU&libraries=places&callback=initMap">
    </script>
@endpush
