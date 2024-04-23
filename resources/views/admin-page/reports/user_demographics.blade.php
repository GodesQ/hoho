@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off - User Demographics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">User Demographics</h4>
    </div>

    {{-- <div class="row mb-3">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
    </div> --}}

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
    </script>
@endpush
