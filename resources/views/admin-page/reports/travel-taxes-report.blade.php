@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop - Sales Report')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section-header d-flex justify-content-between align-items-center">
            <div class="title-section">
                <h4 class="fw-medium mb-2">Travel Tax Reports</h4>
                <h6 class="fw-medium text-primary"><a href="{{ route('admin.dashboard') }}"
                        class="text-muted fw-light">Dashboard /</a> Travel Tax Reports</h6>
            </div>
        </section>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-bottom pb-2">
                        <div class="card-title mb-2">
                            <h4 class="m-0 me-2">Total Amount ({{ Carbon::now()->format('M') }})</h4>
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
                                <h2 style="font-weight: bold;">₱ <span id="total-payment-amount">0.00</span></h2>
                                <h6 style="line-height: 5px;">Total Amount This Month </h6>
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
                            <h5 class="m-0 me-2">Top 5 Departure Countries</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center my-3">
                            <div id="pie-simple-chart"></div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-header border-bottom pb-2">
                        <div class="card-title mb-2">
                            <h5 class="m-0 me-2">Total Payment Per Class</h5>
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
                            <h5 class="m-0 me-2">Total Payments Per Month</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="overallSaleChart" class="my-3"></div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Monthly Total Transactions</h5>
                                    <h6 class="text-primary">{{ date('Y') }}</h6>
                                </div>
                                <table class="table table-striped table-bordered rounded" id="monthly-total-transactions">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th style="text-align: center;">Transaction Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">

                            </div>
                        </div>
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
            getTotalTravelTaxPayment();
            getTopSellingTours();
            getTotalBookingsPerType();
            getTravelTaxTxnCountPerMonth();
        });

        function getTotalTravelTaxPayment() {
            fetch("{{ route('admin.reports.travel_taxes_report.total_payment_amount') }}")
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#total-payment-amount').innerText = parseInt(data.total_amount).toFixed(2);
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

        function getTravelTaxTxnCountPerMonth() {
            fetch("{{ route('admin.reports.travel_taxes_report.trasanctions_count_per_month') }}")
                .then(response => response.json())
                .then(data => {
                    let monthly_total_transactions_table_body = document.querySelector('#monthly-total-transactions tbody');
                    let output = '';
                    if(data.results.length > 0) {
                        data.results.forEach(result => {
                            output += `<tr>
                                    <td>${result.month_name}</td>
                                    <td align="center">${result.total_count}</td>
                                </tr>`;
                        })
                        monthly_total_transactions_table_body.innerHTML = output;
                    }
                })
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

        var pieSimpleChart = {
            chart: {
                height: 350,
                type: 'pie',
            },
            labels: ['USA', 'Brazil', 'Australia', 'Japan', 'South Korea'],
            series: [44, 55, 13, 43, 22],
            responsive: [{
                breakpoint: 1200,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }, {
                breakpoint: 768,
                options: {
                    chart: {
                        width: 520
                    },
                    legend: {
                        position: 'right'
                    }
                }
            }, {
                breakpoint: 620,
                options: {
                    chart: {
                        width: 450
                    },
                    legend: {
                        position: 'right'
                    }
                }
            }, {
                breakpoint: 480,
                options: {
                    chart: {
                        width: 250
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],

        }

        var pie_simple_chart = new ApexCharts(
            document.querySelector("#pie-simple-chart"),
            pieSimpleChart
        );
        pie_simple_chart.render();

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
                        categories: ['Business Class', 'First Class'],
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
                    const series = Object.keys(data.data[categories[0]]).map(status => ({
                        name: status,
                        data: []
                    }));

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
                                    'transparent'
                                ], // takes an array which will be repeated on columns
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
