@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Tour Reservations Report')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Tour Reservations Report</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reports.tour_reservations_report.get_data') }}" method="get" target="_blank">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" name="from_date" id="from_date">
                            <span class="danger text-danger">@error('from_date'){{ $message }}@enderror</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" name="to_date" id="to_date">
                            <span class="danger text-danger">@error('to_date'){{ $message }}@enderror</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="tour_type" class="form-label">Tour Type</label>
                            <select name="tour_type" id="tour_type" class="form-select">
                                <option value="Guided">Guided Tour</option>
                                <option value="DIY">DIY Tour</option>
                            </select>
                            <span class="danger text-danger">@error('tour_type'){{ $message }}@enderror</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All</option>
                                <option value="approved">Approved</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <span class="danger text-danger">@error('status'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>
                <hr>
                <input type="submit" name="action" value="Print" class="btn btn-outline-primary">
                <input type="submit" name="action" value="Download CSV" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>


@endsection
