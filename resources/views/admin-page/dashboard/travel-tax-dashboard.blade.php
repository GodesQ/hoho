@extends('layouts.admin.layout')

@section('title', 'Travel Tax Dashboard - Philippine Hop On Hop Off')

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
        @auth('admin')
            @can('update_maintenance_mode')
                <div class="d-flex justify-content-end">
                    <div class="form-check form-switch mb-2">
                        <label class="form-check-label" for="maintenance-mode-btn">Maintenance Mode</label>
                        <input class="form-check-input" type="checkbox" id="maintenance-mode-btn" name="is_approved"
                            {{ maintenanceMode() ? 'checked' : null }} />
                    </div>
                </div>
            @endcan
        @endauth
        <div class="row">
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-6">
                            <div class="card-body">
                                <?php
                                    $hour = date("H");

                                    if ($hour >= 5 && $hour < 12) {
                                        $greeting = "Good Morning";
                                    } elseif ($hour >= 12 && $hour < 17) {
                                        $greeting = "Good Afternoon";
                                    } elseif ($hour >= 17 || $hour < 5) {
                                        $greeting = "Good Evening";
                                    }
                                ?>
                                <h5 class="card-title text-primary">{{ $greeting }},
                                    {{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->username)) }}! 🎉</h5>
                                <p class="mb-4">
                                    Welcome to your Hop On Hop Off Travel Dashboard.
                                    Here's your overview regarding travel tax payments
                                    made thru Hop On Hop Off app.
                                    <br>
                                    <br>
                                    If you have any questions or see any mistakes,
                                    kindly contact our support.
                                </p>
                                <a href="https://m.me/philippineshoponhopoff" target="_blank" class="btn btn-sm btn-outline-primary">Contact Support</a>
                            </div>
                        </div>
                        <div class="col-sm-6 text-center text-sm-left">
                            <div class="card-body pb-0 px-0" style="padding: 4px !important;">
                                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/newbus-copy2-1.png"
                                    class="dashboard-img" alt="HOHO"
                                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="row row-bordered g-0">
                        <div class="col-md-5">
                            <div class="card-body">
                                <h5>Recent Passengers</h5>
                                @forelse ($recent_passengers as $passenger)
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            @if ($passenger->payment->status == 'paid')
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
                                                    class="text-muted d-block mb-1">{{ $passenger->ticket_number }}</small>
                                                <h6 class="mb-0">
                                                    <a href="{{ route('admin.travel_taxes.edit', $passenger->id) }}">
                                                        {{ $passenger->firstname }} {{ $passenger->lastname }}
                                                    </a>
                                                </h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0" style="font-size: 12px;">₱
                                                    {{ number_format($passenger->payment->total_amount, 2) }}</h6>
                                                {{-- <span class="text-muted">USD</span> --}}
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5 class="card-header m-0 me-2 pb-3">Total Payments Chart</h5>
                            <div id="total_payment_per_class_chart" class="px-2"></div>
                        </div>
                        <div class="col-md-3">
                            <h5 class="card-header m-0 me-2 pb-3">Total Payments Per Class</h5>
                            <div class="card-body mt-3">
                                <ul class="p-0 m-0" id="topSellingToursList">
                                    @forelse ($total_payments_per_class as $payment_class)
                                        <li class="d-flex mb-4 pb-1">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-primary"><i
                                                        class="bx bx-wallet"></i></span>
                                            </div>
                                            <div
                                                class="gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-uppercase" style="font-size: 13px;">
                                                        {{ $payment_class->class }}
                                                    </h6>
                                                    <small
                                                        class="text-muted"></small>
                                                </div>
                                                <div class="user-progress">
                                                    <small class="fw-semibold">₱
                                                        {{ number_format($payment_class->total_amount, 2) }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <h6 class="text-center">No Payments Found</h6>
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
                                <div class="d-flex justify-content-between align-items-center my-2 h-100">
                                    <div style="width: 20%">
                                        <img src="{{ URL::asset('assets/img/icons/unicons/transaction-success.png') }}"
                                            alt="User" class="rounded" />
                                    </div>
                                    <div style="width: 80%">
                                        <h2 style="font-weight: bold;">₱ <span
                                                id="profit-amount">{{ number_format($totalProfit, 2) }}</span></h2>
                                        <h6 style="line-height: 5px;">Earned this month</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Recent Payments</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                <a class="dropdown-item" href="{{ route('admin.travel_taxes.list') }}">All Payments</a>
                                <a class="dropdown-item" href="{{ route('admin.reports.travel_taxes_report') }}">Travel Tax Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @foreach ($recent_payments as $recent_payment)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        @if ($recent_payment->status == 'paid')
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
                                                class="text-muted d-block mb-1">{{ Carbon::parse($recent_payment->payment_time)->format('M d, Y') }}</small>
                                            <h6 class="mb-0"><a
                                                    href="{{ route('admin.travel_taxes.edit', $recent_payment->id) }}">{{ $recent_payment->reference_number }}</a>
                                            </h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">₱
                                                {{ number_format($recent_payment->total_amount, 2) }}</h6>
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
            getTotalPaymentPerClass();
        });

        function getTotalPaymentPerClass() {
            fetch("{{ route('admin.reports.travel_taxes_report.total_payment_per_class') }}")
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    setTotalPaymentPerClassChart(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function setTotalPaymentPerClassChart(data) {
            const totalPaymentPerClassEl = document.querySelector('#total_payment_per_class_chart'),
                totalPaymentPerClassChartOptions = {
                    series: [{
                        data: [data.total_economy_class, data.total_first_class]
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
                        categories: ['Economy Class', 'First Class'],
                    }
                };
            if (typeof totalPaymentPerClassEl !== undefined && totalPaymentPerClassEl !== null) {
                const totalPaymentPerClassChart = new ApexCharts(totalPaymentPerClassEl,
                    totalPaymentPerClassChartOptions);
                totalPaymentPerClassChart.render();
            }
        }

        const maintenanceModeBtn = document.querySelector('#maintenance-mode-btn');

        maintenanceModeBtn.addEventListener('change', (e) => {
            if (e.target.checked) {
                Swal.fire({
                    title: 'Are you sure you want to turn on maintenance mode?',
                    text: "It can affect to mobile application",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateMaintenanceMode(1);
                    } else {
                        e.target.checked = false;
                    }
                })
            } else {
                updateMaintenanceMode(0);
            }
        })

        const updateMaintenanceMode = (mode) => {
            $.ajax({
                url: "{{ route('admin.maintenance-mode.update') }}",
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    maintenance_mode: mode,
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Updated!', response.message, 'success');
                    }
                }
            })
        }
    </script>
@endpush
