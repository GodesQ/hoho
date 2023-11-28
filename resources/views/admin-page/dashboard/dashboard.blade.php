@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Dashboard')

@section('content')
    <style>
        .dashboard-img {
            height: 240px !important;
        }

        @media screen and (max-width: 1280px) {
            .dashboard-img {
                height: 200px !important;
            }
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-6">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Good Morning,
                                    {{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->username)) }}! 🎉</h5>
                                <p class="mb-4">
                                    Welcome to your Hop On Hop Off Travel Dashboard.
                                    Here's your overview regarding tour reservations
                                    made thru Hop On Hop Off app.
                                    <br>
                                    <br>
                                    If you have any questions or see any mistakes,
                                    kindly contact our support.
                                </p>
                                {{-- <a href="javascript:;" class="btn btn-sm btn-outline-primary">Contact Support</a> --}}
                            </div>
                        </div>
                        <div class="col-sm-6 text-center text-sm-left">
                            <div class="card-body pb-0 px-0" style="padding: 4px !important;">
                                <img src="https://metrohoho.s3.ap-southeast-1.amazonaws.com/hoho_bus.png"
                                    class="dashboard-img" alt="View Badge User"
                                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="row row-bordered g-0">
                        <div class="col-md-6">
                            <h5 class="card-header m-0 me-2 pb-3">Total Bookings</h5>
                            <div id="totalBookingsPerTypeChart" class="px-2"></div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-header m-0 me-2 pb-3">Top Selling Tours </h5>
                            <div class="card-body mt-3">
                                <ul class="p-0 m-0" id="topSellingToursList">
                                    @forelse ($topSellingTours as $topSellingTour)
                                        <li class="d-flex mb-4 pb-1">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-primary"
                                                ><i class="bx bx-wallet"></i
                                                ></span>
                                            </div>
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                <h6 class="mb-0">{{ strlen(optional($topSellingTour->tour)->name) > 25 ? substr(optional($topSellingTour->tour)->name, 0, 25) . '...' : optional($topSellingTour->tour)->name }}</h6>
                                                <small class="text-muted">{{ optional($topSellingTour->tour)->type }}</small>
                                                </div>
                                                <div class="user-progress">
                                                <small class="fw-semibold">₱ {{ number_format($topSellingTour->total_amount, 2) }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <h6 class="text-center">No Tour Reservations Found</h6>
                                    @endforelse
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 order-1">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Profit</h5>
                                <div class="d-flex justify-content-between align-items-center my-2 h-100">
                                    <div style="width: 20%">
                                        <img src="{{ URL::asset('assets/img/icons/unicons/transaction-success.png') }}"
                                            alt="User" class="rounded" />
                                    </div>
                                    <div style="width: 80%">
                                        <h2 style="font-weight: bold;">₱ <span id="profit-amount">{{ number_format($totalProfit, 2) }}</span></h2>
                                        <h6 style="line-height: 5px;">Earned this month</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Recent Transactions</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                <a class="dropdown-item" href="javascript:void(0);">All Transactions</a>
                                <a class="dropdown-item" href="javascript:void(0);">Transactions Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @foreach ($recentTransactions as $recent_transaction)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        @if ($recent_transaction->payment_status == 'success')
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
                                                class="text-muted d-block mb-1">{{ $recent_transaction->aqwire_paymentMethodCode ? $recent_transaction->aqwire_paymentMethodCode : 'Payment Method Not Set' }}</small>
                                            <h6 class="mb-0"><a
                                                    href="{{ route('admin.transactions.edit', $recent_transaction->id) }}">{{ $recent_transaction->reference_no }}</a>
                                            </h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">₱ {{ number_format($recent_transaction->payment_amount, 2) }}</h6>
                                            {{-- <span class="text-muted">USD</span> --}}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">

            </div>
            <div class="col-md-6 col-lg-4 order-2 mb-4">

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            getTotalBookingsPerType();
        });

        function getTotalBookingsPerType() {
            fetch("{{ route('admin.reports.get_total_bookings_per_type') }}")
                .then(response => response.json())
                .then(data => {
                    setTotalBookingTypeChart(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function setTotalBookingTypeChart(data) {
            const totalBookingsPerTypeChartEl = document.querySelector('#totalBookingsPerTypeChart'),
                totalBookingsPerTypeChartOptions = {
                    series: [{
                        data: [data.total_guided_tours, data.total_diy_tours]
                    }],
                    chart: {
                        stacked: true,
                        type: 'bar',
                        toolbar: {
                            show: false
                        },
                        height: 300,
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 10,
                            horizontal: false,
                            startingShape: 'rounded',
                            endingShape: 'rounded'
                        }
                    },
                    colors: [config.colors.primary, config.colors.info],
                    xaxis: {
                        categories: ['Guided Tour', 'DIY Tour'],
                    }
                };
            if (typeof totalBookingsPerTypeChartEl !== undefined && totalBookingsPerTypeChartEl !== null) {
                const totalBookingsPerTypeChart = new ApexCharts(totalBookingsPerTypeChartEl,
                    totalBookingsPerTypeChartOptions);
                totalBookingsPerTypeChart.render();
            }
        }
    </script>
@endpush
