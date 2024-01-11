<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta property="fb:app_id" content="514817087155790" />
    <meta property="og:title" content="Guest Partner Dashboard" />
    <meta property="og:description" content="The official LGU / Merchant dashboard for Guest PH." />
    <meta property="og:image"
        content="http://guest-app-main.s3.ap-southeast-1.amazonaws.com/Web/guest-brand-logo.jpg" />
    <meta property="og:image:secure_url"
        content="https://guest-app-main.s3.ap-southeast-1.amazonaws.com/Web/guest-brand-logo.jpg" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://guestapp.ph" />
    <meta property="og:site_name" content="guestapp.ph" />

    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <link rel="apple-touch-icon" href="{{ URL::asset('assets/img/logo/favicon-hoho.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('assets/img/logo/favicon-hoho.ico') }}">
    <title>@yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
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

    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <link rel="stylesheet" href="{{ URL::asset('assets/css/selects/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/wizard.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/tour-reservation-step-form.css') }}">

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ URL::asset('assets/vendor/js/helpers.js') }}"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ URL::asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Menu --}}

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                style="width:35px; border-radius: 5px;" alt="">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">Menu</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                @include('layouts.menu')
                
            </aside>
            {{-- Menu --}}

            {{-- Layout Container --}}
            <div class="layout-page">
                {{-- Navbar --}}
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                {{-- <i class="bx bx-search fs-4 lh-0"></i> --}}
                                <input type="text" class="form-control border-0 shadow-none"
                                    placeholder="" aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
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
                                                    <small
                                                        class="text-muted">{{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->role)) }}</small>
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
                                    <li
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                {{-- Navbar --}}

                {{-- Content Wrapper --}}
                <div class="content-wrapper">
                    @yield('content')
                </div>
                {{-- Content Wrapper --}}
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ URL::asset('assets/js/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.steps.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ URL::asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ URL::asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ URL::asset('assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('assets/js/select/form-select2.js') }}"></script>
    <script src="{{ asset('assets/js/wizard-steps.js') }}"></script>
    <script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>

    <!-- CDN JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    {{-- <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkEg-gEi37ABEp2EHxHMMEiYr_ZwdzQyg&libraries=places&callback=initMap">
    </script> --}}
    {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEmTK1XpJ2VJuylKczq2-49A6_WuUlfe4&libraries=places&callback=initMap"></script> --}}

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>


    @if (Session::get('success'))
        <script>
            toastr.options = {
                closeButton: true, // Add close button
                timeOut: 2000
            };
            toastr.success("{{ Session::get('success') }}", "Success");
        </script>
    @endif

    @if (Session::get('fail'))
        <script>
            toastr.options = {
                closeButton: true, // Add close button
                timeOut: 2000
            };
            toastr.error("{{ Session::get('fail') }}", 'Fail');
        </script>
    @endif

    @stack('scripts')

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

</body>

</html>
