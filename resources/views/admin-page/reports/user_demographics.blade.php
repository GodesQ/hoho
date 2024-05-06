@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off - User Demographics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">User Demographics</h4>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5>Users Per Month (2024)</h5>
        </div>
        <div class="card-body">
            <div id="user-per-month-graph"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Users By Location</h5>
                </div>
                <div class="card-body">
                    <div id="user-by-location-graph">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Users By Age</h5>
                </div>
                <div class="card-body">
                    <div id="user-by-age-graph"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            getUsersByAge();
            getUsersByLocation();
            getUsersPerMonth();
        });

        function getUsersByAge() {
            fetch("{{ route('admin.reports.user_demographics.user_ages') }}")
                .then(response => response.json())
                .then(data => {
                    var result = Object.entries(data.result);
                    setUsersByAge(result);
                })
                .catch(error => console.error('Error:', error));
        }

        function getUsersByLocation() {
            fetch("{{ route('admin.reports.user_demographics.user_locations') }}")
                .then(response => response.json())
                .then(data => {
                    console.log(data.result);
                    setUsersByLocation(data.result);
                })
                .catch(error => console.error('Error:', error));
        }

        function getUsersPerMonth() {
            fetch("{{ route('admin.reports.user_demographics.user_months') }}")
                .then(response => response.json())
                .then(data => {
                    setUsersPerMonthChart(data.result);
                })
                .catch(error => console.error('Error:', error));
        }

        function setUsersByAge(result) {
            const usersByAgeEl = document.querySelector('#user-by-age-graph'),
                usersByAgeChartOptions = {
                    series: [{
                        data: result.map(res => res[1])
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
                        categories: result.map(res => res[0]),
                    }
                };
            if (typeof usersByAgeEl !== undefined && usersByAgeEl !== null) {
                const usersByAgeChart = new ApexCharts(usersByAgeEl,
                    usersByAgeChartOptions);
                usersByAgeChart.render();
            }
        }

        function setUsersByLocation(result) {
            const countries = result.map(res => res.country_of_residence);
            const totals = result.map(res => res.total_user);

            const usersByLocationEl = document.querySelector('#user-by-location-graph'),
                usersByLocationChartOptions = {
                    series: [{
                        data: totals
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
                        categories: countries,
                    }
                };
            if (typeof usersByLocationEl !== undefined && usersByLocationEl !== null) {
                const usersByLocationChart = new ApexCharts(usersByLocationEl,
                    usersByLocationChartOptions);
                usersByLocationChart.render();
            }
        }

        function setUsersPerMonthChart(result) {
            let usersPerMonthChartEl = document.querySelector('#user-per-month-graph');
            const months = result.map(res => res.month_name);
            const totals = result.map(res => res.total_user);

            const usersPerMonthChart = new ApexCharts(usersPerMonthChartEl, {
                series: [{
                    data: totals,
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
                    categories: months,
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
            usersPerMonthChart.render();
        }
    </script>
@endpush
