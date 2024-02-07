{{-- <!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="apple-touch-icon" href="{{ URL::asset('assets/img/logo/hoho.jpg') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('assets/img/logo/hoho.jpg') }}">
    <title>Hop On Hop Off - Admin Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ URL::asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/css/pages/page-auth.css') }}" />
    <!-- Helpers -->
    <script src="{{ URL::asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ URL::asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl">
        <button class="btn btn-primary" id="test-btn">Test Location for MANILA (2)</button>
    </div>

    <script src="{{ URL::asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script>
        let test_btn = document.querySelector('#test-btn');
        let INITIAL_LATITUDE = 14.5895189;
        let INITIAL_LONGITUDE = 120.9790363;

        let CURRENT_LATITUDE = 14.5896189;
        let CURRENT_LONGITUDE = 120.9792363;

        test_btn.addEventListener('click', function() {
            sendLocationToServer();
        });

        function degToRad(deg) {
            return deg * (Math.PI / 180);
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const earthRadius = 6371; // Earth's radius in kilometers

            const dLat = degToRad(lat2 - lat1);
            const dLon = degToRad(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(degToRad(lat1)) * Math.cos(degToRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const distance = earthRadius * c;
            return distance;
        }

        function sendLocationToServer() {
            let latitude, longitude;


            const successCallback = (position) => {
                // latitude = position.coords.latitude;
                // longitude = position.coords.longitude;

                // // Send location to Laravel backend
                // fetch('{{ route('admin.transports.updateLocation') }}', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': "{{ csrf_token() }}"
                //     },
                //     body: JSON.stringify({
                //         id: 2,
                //         latitude: latitude,
                //         longitude: longitude
                //     })
                // });

                // // Send location every few seconds
                // setTimeout(sendLocationToServer, 5000); // Adjust the interval as needed
            };

            const errorCallback = (error) => {
                console.log(error);
            };

            navigator.geolocation.watchPosition(successCallback, errorCallback);

            INITIAL_LATITUDE = CURRENT_LATITUDE;
            INITIAL_LONGITUDE = CURRENT_LONGITUDE;

            CURRENT_LATITUDE = INITIAL_LATITUDE + 0.0004;
            CURRENT_LONGITUDE = INITIAL_LONGITUDE + 0.0004;

            console.log(CURRENT_LATITUDE, CURRENT_LONGITUDE);

            let distance = calculateDistance(INITIAL_LATITUDE, INITIAL_LONGITUDE, CURRENT_LATITUDE, CURRENT_LONGITUDE);

             // Send location to Laravel backend
             fetch('{{ route('admin.transports.updateLocation') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
 
                        latitude: CURRENT_LATITUDE,
                        longitude: CURRENT_LONGITUDE
                    })
                });

                // Send location every few seconds
                setTimeout(sendLocationToServer, 5000); // Adjust the interval as needed
        }
        // sendLocationToServer();
    </script>
</body>

</html> --}}

<!DOCTYPE html>
<html>
<head>
    <title>Bus Tracker</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script>
        var map;
        var busMarker;
        var routeCoordinates = [
            { lat: 14.5374, lng: 120.9991 }, // Okada Manila
            { lat: 14.5320, lng: 120.9817 }  // SM Mall of Asia
            // Add more waypoints if needed
        ];
        var routeIndex = 0; // Index to track the current waypoint
    
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: routeCoordinates[0], // Start at Okada Manila
                zoom: 12
            });
    
            // Start updating bus location
            setInterval(moveBus, 2000); // Update every 2 seconds
        }
    
        function moveBus() {
            // Check if we reached the end of the route
            if (routeIndex >= routeCoordinates.length - 1) {
                console.log('Bus reached SM Mall of Asia');
                return; // Stop moving the bus
            }
    
            // Create bus marker if it doesn't exist
            if (!busMarker) {
                busMarker = new google.maps.Marker({
                    position: routeCoordinates[0], // Start at Okada Manila
                    map: map,
                    title: 'Bus'
                });
            }
    
            // Move bus marker to the next waypoint
            var nextPosition = routeCoordinates[routeIndex + 1];
            busMarker.setPosition(nextPosition);
            routeIndex++;
    
            // Send bus coordinates to server
            sendBusCoordinates(nextPosition);
        }
    
        function sendBusCoordinates(coordinates) {
            $.ajax({
                url: '{{ route('admin.transports.updateLocation') }}', // Update with your backend endpoint
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: JSON.stringify({
                    id: 4,
                    latitude: coordinates.lat, // Access latitude using dot notation
                    longitude: coordinates.lng // Access longitude using dot notation
                }),
                success: function(response) {
                    console.log('Bus location sent to server:', coordinates);
                },
                error: function(error) {
                    console.error('Error sending bus location:', error);
                }
            });
        }
    </script>
    
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1UJdBuEc_a3P3i-efUeZIJmMQ5VXZGgU&libraries=places&callback=initMap"></script>
</body>
</html>

