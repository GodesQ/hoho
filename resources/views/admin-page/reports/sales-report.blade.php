@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop - Sales Report')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-bottom pb-2">
                        <div class="card-title mb-2">
                            <h4 class="m-0 me-2">Profit</h4>
                            {{-- <small class="text-muted">42.82k Total Sales</small> --}}
                        </div>
                    </div>
                    <div class="card-body py-1">
                        <div class="d-flex justify-content-between align-items-center my-2 h-100">
                            <div style="width: 20%">
                                <img src="{{ URL::asset('assets/img/icons/unicons/transaction-success.png') }}"
                                    alt="User" class="rounded" />
                            </div>
                            <div style="width: 80%">
                                <h2 style="font-weight: bold;">₱ <span id="profit-amount">0.00</span></h2>
                                <h6 style="line-height: 5px;">Earned this month</h6>
                            </div>
                            {{-- <div>
                            <div style="font-size: 12px;" class="text-success fw-semibold"> +72.80% <i class="bx bx-up-arrow-alt"></i></div>
                        </div> --}}
                        </div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Top Selling Tours</h5>
                            {{-- <small class="text-muted">42.82k Total Sales</small> --}}
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <h2 class="mb-2" id="total-orders">0</h2>
                                <span>Total Orders</span>
                            </div>
                            <div id="topSellingToursChart"></div>
                        </div>
                        <ul class="p-0 m-0" id="topSellingToursList"></ul>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-header border-bottom pb-2">
                        <div class="card-title mb-2">
                            <h5 class="m-0 me-2">Total Bookings Per Type</h5>
                            {{-- <small class="text-muted">42.82k Total Sales</small> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="totalBookingsPerTypeChart" class="px-2"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom pb-2">
                        <div class="card-title mb-2">
                            <h5 class="m-0 me-2">Overall Sales</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="overallSaleChart" class="my-3"></div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-header border-bottom pb-2">
                        <div class="card-title mb-2">
                            <h5 class="m-0 me-2">Transaction By Status</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="transactionStatusChart" class="my-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make the request
            getTotalProfit();
            getTopSellingTours();
            getTotalBookingsPerType();
        });

        function getTotalProfit() {
            fetch("{{ route('admin.reports.get_profit') }}")
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#profit-amount').innerText = data.total_profit;
                })
                .catch(error => console.error('Error:', error));
        }

        function getTopSellingTours() {
            fetch("{{ route('admin.reports.get_top_selling_tours') }}")
                .then(response => response.json())
                .then(data => {
                    $('#total-orders').text(data.total_orders);
                    displayTopSellingTours(data);
                    setTopSellingToursChart(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function getTotalBookingsPerType() {
            fetch("{{ route('admin.reports.get_total_bookings_per_type') }}")
                .then(response => response.json())
                .then(data => {
                    setTotalBookingTypeChart(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function displayTopSellingTours(data) {
            let toursList = document.querySelector('#topSellingToursList');
            let output = '';

            data.top_selling_tours.forEach(tour => {
                var totalAmount = parseFloat(tour
                    .total_amount); // Use parseFloat if the amount can have decimal places
                var formattedAmount = totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                output += `
                            <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"
                                ><i class="bx bx-wallet"></i
                                ></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                <h6 class="mb-0">${tour.tour.name.length > 25 ? tour.tour.name.substr(0, 25) + '...' : tour.tour.name }</h6>
                                <small class="text-muted">${tour.tour.type}</small>
                                </div>
                                <div class="user-progress">
                                <small class="fw-semibold">${formattedAmount}</small>
                                </div>
                            </div>
                            </li>
                        `;
            })
            toursList.innerHTML = output;
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

        function setTopSellingToursChart(data) {
            console.log(data);
            const chartOrderStatistics = document.querySelector('#topSellingToursChart'),
                orderChartConfig = {
                    chart: {
                        height: 165,
                        width: 130,
                        type: 'donut'
                    },
                    labels: data.top_selling_tours.map(tour => tour.tour.name),
                    series: data.top_selling_tours.map(tour => tour.total_reservations),
                    colors: [config.colors.primary, config.colors.secondary, config.colors.info, config.colors
                        .success
                    ],
                    stroke: {
                        width: 5,
                    },
                    dataLabels: {
                        enabled: false,
                        formatter: function(val, opt) {
                            return parseInt(val) + '%';
                        }
                    },
                    legend: {
                        show: false
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                            right: 15
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: {
                                    show: true,
                                    value: {
                                        fontSize: '1.5rem',
                                        fontFamily: 'Inter',
                                        offsetY: -15,
                                        formatter: function(val) {
                                            return parseInt(val)
                                        }
                                    },
                                    name: {
                                        offsetY: 20,
                                        fontFamily: 'Inter'
                                    },
                                    total: {
                                        show: true,
                                        fontSize: '0.8125rem',
                                        label: 'Monthly',
                                        formatter: function(w) {
                                            // console.log(data.top_selling_tours);
                                            return data.top_selling_tours.reduce((acc, tour) => {
                                                // console.log(acc, tour);
                                                console.log(tour);
                                                return parseint(acc) + parseInt(tour.total_reservations);
                                            }, 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
            if (typeof chartOrderStatistics !== undefined && chartOrderStatistics !== null) {
                const statisticsChart = new ApexCharts(chartOrderStatistics, orderChartConfig);
                statisticsChart.render();
            }
        }

        const overallSaleChartEl = document.querySelector('#overallSaleChart');

        if (typeof overallSaleChartEl !== 'undefined' && overallSaleChartEl !== null) {
            fetch("{{ route('admin.reports.get_overall_sales') }}")
                .then(response => response.json())
                .then(data => {
                    setOverAllSaleChart(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function setOverAllSaleChart(data) {
            const overallSaleChart = new ApexCharts(overallSaleChartEl, {
                series: [{
                    data: data.salesData
                }],
                chart: {
                    height: 215,
                    parentHeightOffset: 0,
                    parentWidthOffset: 0,
                    toolbar: {
                        show: false
                    },
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                legend: {
                    show: false
                },
                markers: {
                    size: 6,
                    colors: 'transparent',
                    strokeColors: 'transparent',
                    strokeWidth: 4,
                    discrete: [{
                        fillColor: config.colors.white,
                        seriesIndex: 0,
                        dataPointIndex: 7,
                        strokeColor: config.colors.primary,
                        strokeWidth: 2,
                        size: 6,
                        radius: 8
                    }],
                    hover: {
                        size: 7
                    }
                },
                colors: [config.colors.primary],
                xaxis: {
                    categories: data.months,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        show: true,
                        style: {
                            fontSize: '13px',
                        }
                    }
                }
            });
            overallSaleChart.render();
        }

        const transactionStatusEl = document.querySelector('#transactionStatusChart');

        if (typeof transactionStatusEl !== undefined && transactionStatusEl !== null) {
            fetch("{{ route('admin.reports.get_transaction_status_data') }}")
                .then(response => response.json())
                .then(data => {
                    const categories = Object.keys(data.data);
                    const series = Object.keys(data.data[categories[0]]).map(status => ({ name: status, data: [] }));

                    categories.forEach(month => {
                        series.forEach(status => {
                            status.data.push(data.data[month][status.name]);
                        });
                    });

                    const transactionStatusConfig = {
                        series,
                        chart: {
                            height: 350,
                            type: 'line',
                            zoom: {
                                enabled: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'straight'
                        },
                        title: {
                            text: 'Transaction Status By Month',
                            align: 'left'
                        },
                        colors: [
                            '#6f0d00',
                            '#71dd37',
                            '#ffc858'
                        ],
                        grid: {
                            row: {
                                colors: ['#f3f3f3',
                                'transparent'], // takes an array which will be repeated on columns
                                opacity: 0.2
                            },
                        },
                        xaxis: {
                            categories
                            // ...
                        }
                    };

                    const transactionStatusChart = new ApexCharts(transactionStatusEl, transactionStatusConfig);
                    transactionStatusChart.render();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endpush
