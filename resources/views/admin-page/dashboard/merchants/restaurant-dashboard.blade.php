@extends('layouts.admin.layout')

@section('title', 'Restaurant Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Welcome
                                    {{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->username)) }}! 🎉</h5>
                                <p class="mb-4">
                                    Track
                                    occupancy, manage bookings, and optimize your operations effortlessly using Philippine
                                    Hop On Hop Off.
                                </p>

                                <a href="{{ route('merchant_form', $type) }}" class="btn btn-sm btn-outline-primary">View
                                    Merchant Profile</a>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="../assets/img/illustrations/man-with-laptop-light.png" height="140"
                                    alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 order-1">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4 px-1">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success"
                                            class="rounded" />
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                            <a class="dropdown-item" href="javascript:void(0);">View List</a>
                                        </div>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Foods</span>
                                <h3 class="card-title mb-2">0{{ $foods_count }}</h3>
                                {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4 px-1">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="../assets/img/icons/unicons/wallet-info.png" alt="Credit Card"
                                            class="rounded" />
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Reservations</span>
                                <h3 class="card-title mb-2">0{{ $restaurant_reservations_count }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                </div>
            </div>
            <!-- Total Revenue -->
            <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Restaurant Reservations Per Month</h5>
                        <div id="incomeChart"></div>
                    </div>
                </div>
            </div>
            <!--/ Total Revenue -->
            <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Restaurant Reservations</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @forelse ($recent_restaurant_reservations as $recent_restaurant_reservation)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        @if ($recent_restaurant_reservation->status == 'approved')
                                            <img src="{{ URL::asset('assets/img/icons/unicons/transaction-success.png') }}"
                                                alt="User" class="rounded" />
                                        @else
                                            <img src="{{ URL::asset('assets/img/icons/unicons/transaction-warning.png') }}"
                                                alt="User" class="rounded" />
                                        @endif
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small
                                                class="text-muted d-block mb-1">{{ $recent_restaurant_reservation->reserved_user ? $recent_restaurant_reservation->reserved_user->email : 'Deleted User' }}</small>
                                            <h6 class="mb-0">
                                                <a href="{{ route('admin.restaurant_reservations.edit', $recent_restaurant_reservation->id) }}">{{ $recent_restaurant_reservation->merchant->name ?? null }}</a>
                                            </h6>
                                        </div>
                                        <div class="user-progress text-end">
                                            <small class="text-mute d-block mb-1">
                                                {{ $recent_restaurant_reservation->reservation_date->format('M d, Y') }} 
                                                {{ date_format(new \DateTime($recent_restaurant_reservation->reservation_time), 'h:i A') }}
                                            </small> 
                                            <h6 class="mb-0 text-end">{{ $recent_restaurant_reservation->seats }} Seats</h6>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="d-flex mb-4 pb-1">No Hotel Reservations Found</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection