<!DOCTYPE html>
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
    <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
        id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            {{-- <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a> --}}
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav justify-content-center align-items-center">
                <div class="nav-item d-flex justify-content-center align-items-center">
                    <div>Merchant Form</div>
                    {{-- <i class="bx bx-search fs-4 lh-0"></i> --}}
                    {{-- <input type="text" class="form-control border-0 shadow-none" placeholder="Search..."
                        aria-label="Search..." /> --}}
                </div>
            </div>
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            @auth('admin')
                                @if (auth('admin')->user()->admin_profile)
                                    <img src="{{ URL::asset('assets/img/admin_profiles/' . auth('admin')->user()->admin_profile) }}"
                                        alt class="w-px-40 h-auto rounded-circle" />
                                @else
                                    <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                        alt class="w-px-40 h-auto rounded-circle" />
                                @endif
                            @endauth
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            @auth('admin')
                                                @if (auth('admin')->user()->admin_profile)
                                                    <img src="{{ URL::asset('assets/img/admin_profiles/' . auth('admin')->user()->admin_profile) }}"
                                                        alt class="w-px-40 h-auto rounded-circle" />
                                                @else
                                                    <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                                        alt class="w-px-40 h-auto rounded-circle" />
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span
                                            class="fw-semibold d-block">{{ Auth::guard('admin')->user()->username }}</span>
                                        <small class="text-muted">Admin</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="bx bx-user me-2"></i>
                                <span class="align-middle">My Profile</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <a class="dropdown-item" href="#">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </a>
                            <form method="POST" action="{{ route('admin.logout') }}" id="logout-form"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <hr>
                                        <h4>Merchant Information</h4>
                                        <hr>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Merchant Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name"
                                                        id="name" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="code" class="form-label">Merchant Code</label>
                                                    <input type="text" class="form-control" name="code"
                                                        id="code" value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="type" class="form-label">Merchant Type <span
                                                            class="text-danger">*</span></label>
                                                    <select name="type" id="type" class="form-select"
                                                        required>
                                                        <option value="Restaurant">Restaurant</option>
                                                        {{-- <option value="Hotel">Hotel</option>
                                                        <option value="Store">Store</option>
                                                        <option value="Tour Provider">Tour Provider</option> --}}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="featured_image" class="form-label">Featured
                                                        Image</label>
                                                    <input type="file" class="form-control" name="featured_image"
                                                        id="featured_image" value="" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="nature_of_business" class="form-label">Nature of
                                                        Business</label>
                                                    <input type="text" class="form-control"
                                                        name="nature_of_business" id="nature_of_business"
                                                        value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="organizations"
                                                        class="form-label">Organizations</label>
                                                    <select name="organization_id" id="organization_id"
                                                        class="select2 form-select">
                                                        @foreach ($organizations as $organization)
                                                            <option value="{{ $organization->id }}">
                                                                {{ $organization->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input type="text" class="form-control" name="address"
                                                        id="address">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label action="latitude"
                                                                class="form-label">Latitude</label>
                                                            <input type="text" class="form-control"
                                                                name="latitude" id="latitude">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label action="longitude"
                                                                class="form-label">longitude</label>
                                                            <input type="text" class="form-control"
                                                                name="longitude" id="longitude">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <hr>
                                                <h4> Store Information</h4>
                                                <hr>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="interests" class="form-label">Interests</label>
                                                    <select name="interests[]" id="interests"
                                                        class="form-select select2" multiple>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="payment_options" class="form-label">Payment
                                                        Options</label>
                                                    <input type="text" class="form-control" name="payment_options"
                                                        id="payment_options" value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="contact_number" class="form-label">Contact
                                                        Number</label>
                                                    <input type="text" name="contact_number" id="contact_number"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="contact_email" class="form-label">Contact
                                                        Email</label>
                                                    <input type="text" name="contact_email" id="contact_email"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="business_hours" class="form-label">Business
                                                        Hours</label>
                                                    <textarea name="business_hours" id="business_hours" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="tags" class="form-label">Tags</label>
                                                    <textarea name="tags" id="tags" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            {{-- <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="images" class="form-label">Images</label>
                                                    <input type="file" name="images[]" id="images_1" class="form-control">
                                                    <input type="file" name="images[]" id="images_2" class="form-control">
                                                    <input type="file" name="images[]" id="images_3" class="form-control">
                                                </div>
                                            </div> --}}
                                        </div>
                                        <hr>
                                        <h4>Images</h4>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <input type="file"
                                                                class="form-control mb-2 image-input" accept="image/*"
                                                                name="images[]" id="image_1"
                                                                onchange="handlePreviewImage(this, 'previewImage1')">
                                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                                id="previewImage1" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <input type="file"
                                                                class="form-control mb-2 image-input" accept="image/*"
                                                                name="images[]" id="image_2"
                                                                onchange="handlePreviewImage(this, 'previewImage2')">
                                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                                id="previewImage2" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <input type="file"
                                                                class="form-control mb-2 image-input" accept="image/*"
                                                                name="images[]" id="image_3"
                                                                onchange="handlePreviewImage(this, 'previewImage3')">
                                                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}"
                                                                id="previewImage3" alt="Default Image" width="100%"
                                                                height="200px"
                                                                style="border-radius: 10px; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Preview of Featured Image</h6>
                                        <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt=""
                                            id="previewImage" style="border-radius: 10px;" width="100%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ URL::asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ URL::asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

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
        document.getElementById('featured_image').addEventListener('change', handleFileSelect);
    </script>
</body>

</html>
