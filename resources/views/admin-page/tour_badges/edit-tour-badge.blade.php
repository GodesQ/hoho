@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Tour Badge')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Tour Badge</h4>
            <a href="{{ route('admin.tour_badges.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.tour_badges.update', $tour_badge->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="badge_name" class="form-label">Badge Name</label>
                                        <input type="text" class="form-control" name="badge_name" id="badge_name" value="{{ $tour_badge->badge_name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="badge_code" class="form-label">Badge Code</label>
                                        <input type="text" class="form-control" name="badge_code" id="badge_code" value="{{ $tour_badge->badge_code }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="badge_img" class="form-label">Badge Image</label>
                                        <input type="file" class="form-control" name="badge_img" id="badge_img">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" name="location" id="location" value="{{ $tour_badge->location }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input type="text" class="form-control" name="latitude" id="latitude" readonly value="{{ $tour_badge->latitude }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input type="text" class="form-control" name="longitude" id="longitude" readonly value="{{ $tour_badge->longitude }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="tour" class="form-label">Tour</label>
                                        <select name="tour_id" id="tour" class="select2 form-select">
                                            <option value="">--- SELECT TOUR ---</option>
                                            @foreach ($tours as $tour)
                                                <option value="{{ $tour->id }}" {{ $tour_badge->tour_id == $tour->id ? 'selected' : null }}>{{ $tour->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Badge</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview Badge Image</h6>
                        @if ($tour_badge->badge_img)
                            <img src="{{ URL::asset('assets/img/badges/'. $tour_badge->badge_img) }}" alt="Default Image" id="previewImage"
                            style="border-radius: 10px" width="100%">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image" id="previewImage"
                            style="border-radius: 10px" width="100%">
                        @endif
                        <hr>
                        <h6 class="my-2">QR Code</h6>
                        <div class="qr-code"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        // Function to handle file selection and display preview image
        function handleFileSelect(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        function handlePreviewImage(event, previewImageId) {
            const file = event.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById(previewImageId);
                    console.log(previewImage);
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('badge_img').addEventListener('change', handleFileSelect);
    </script>

    <script>
        let address = document.querySelector('#location');
        let latitude = document.querySelector('#latitude');
        let longitude = document.querySelector('#longitude');
        // let add_badge_btn = document.querySelector('#add-badge-btn');

        // add_badge_btn.addEventListener('click', function() {
        //     console.log(true);
        // })

        function initMap() {
            console.log(false);
            // for search
            let searchBox = new google.maps.places.SearchBox(address);

            google.maps.event.addListener(searchBox, 'places_changed', function() {
                var places = searchBox.getPlaces(),
                    bounds = new google.maps.LatLngBounds(),
                    i, place, lat, long, resultArray, address = places[0].formatted_address;
                lat = places[0].geometry.location.lat()
                long = places[0].geometry.location.lng();
                latitude.value = lat;
                longitude.value = long;
                resultArray = places[0].address_components;
            });
        }

        $(document).ready(function() {
            $('#location').keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Loop through each table row
        document.querySelectorAll('.qr-code').forEach(function(qrCodeContainer) {
            const badgeCode = document.querySelector('#badge_code').value;
            const qrCodeImage = generateQRCode(qrCodeContainer,badgeCode);

            // Create a "Download" button element
            const downloadButton = document.createElement('button');
            downloadButton.className = 'btn btn-primary btn-sm download-qr my-1';
            downloadButton.innerText = 'Download QR';

            // Add an event listener to handle the download
            downloadButton.addEventListener('click', function() {
                // Create a temporary anchor element to trigger the download
                const downloadLink = document.createElement('a');
                downloadLink.href = qrCodeImage;
                downloadLink.download = 'qr_code.png';
                downloadLink.style.display = 'none';

                // Trigger the download
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            });

            // Append the "Download" button to the QR code container
            qrCodeContainer.appendChild(downloadButton);
        });

        function generateQRCode(qrCodeContainer, badgeCode) {
            // Generate the QR code
            const qrCode = new QRCode(qrCodeContainer, {
                text: badgeCode,
                width: 200,
                height: 200
            });

            // const qrCodeDownload = new QRCode(document.createElement('div'), {
            //     text: badgeCode,
            //     width: 300,
            //     height: 300
            // });

            return qrCode._el.firstChild.toDataURL('image/png');
        }
    });
</script>
    
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1UJdBuEc_a3P3i-efUeZIJmMQ5VXZGgU&libraries=places&callback=initMap">
    </script>
@endpush
