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

    <style>
        .contact-messenger-con {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .contact-messenger-con img {
            width: 40px;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="#" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-1024x1024.jpg"
                                        style="width: 50px; border-radius: 5px;" alt="">
                                </span>
                                <span class="app-brand-text demo text-body fw-bold"
                                    style="text-transform: capitalize;">Hop On Hop Off</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Register here! 🚀</h4>
                        <p class="mb-4"></p>

                        <form id="formAuthentication" class="mb-3" action="{{ route('admin.saveRegister') }}"
                            method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter your username" autofocus value="{{ old('username') }}" />
                                <span class="danger text-danger">
                                    @error('username')
                                        {{ $message }}
                                    @enderror
                                </span>

                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" value="{{ old('username') }}" />
                                <span class="danger text-danger">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="*************" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                <span class="danger text-danger">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="confirm-password">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="confirm-password" class="form-control"
                                        name="confirm_password" placeholder="************"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                <span class="danger text-danger">
                                    @error('confirm_password')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="role">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="">--- SELECT ROLE ---</option>
                                    <option value="merchant_store_admin">Store Admin</option>
                                    <option value="merchant_restaurant_admin">Restaurant Admin</option>
                                    <option value="merchant_hotel_admin">Hotel Admin</option>
                                    <option value="tour_operator_admin">Tour Provider Admin</option>
                                </select>
                                <span class="danger text-danger">
                                    @error('role')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms-conditions"
                                        name="terms" />
                                    <label class="form-check-label" for="terms-conditions">
                                        I agree to
                                        <a href="https://philippines-hoho.ph/privacy-policy/">Privacy Policy</a> & <a
                                            href="https://philippines-hoho.ph/terms-and-conditions/">Terms
                                            Condition</a>
                                    </label>
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100">Sign up</button>
                        </form>

                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{ route('admin.login') }}">
                                <span>Sign in instead</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>

    <!-- / Content -->
    <div class="contact-messenger-con">
        <a href="https://m.me/philippineshoponhopoff" target="_blank">
            <img src="{{ URL::asset('assets/img/logo/messenger.png') }}" alt="">
        </a>
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
</body>

</html>
