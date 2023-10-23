@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Transport')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Transport</h4>
        <a href="{{ route('admin.transports.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.transports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <input type="text" class="form-control" name="type" id="type" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity</label>
                                    <input type="text" class="form-control" name="capacity" id="capacity" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration</label>
                                            <input type="text" class="form-control" name="duration" id="duration" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="operator" class="form-label">Operator</label>
                                            <select name="operator_id" id="operator" class="form-select">
                                                <option value="">--- SELECT OPERATOR ---</option>
                                                @foreach ($operators as $operator)
                                                    <option value="{{ $operator->id }}">{{ $operator->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="tour_assignment_ids" class="form-label">Tour Assignments</label>
                                    <select name="tour_assignment_ids[]" id="tour_assignment_ids" class="select2 form-select" multiple>
                                        @foreach ($tours as $tour)
                                            <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="transport_provider_id" class="form-label">Transport Provider</label>
                                            <select name="transport_provider_id" id="transport_provider_id" class="form-select">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="route" class="form-label">Route</label>
                                            <input type="text" class="form-control" name="route" id="route" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Arrival Date</label>
                                            <input type="datetime-local" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Departure Date</label>
                                            <input type="datetime-local" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="text" class="form-control" name="contact_email" id="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="operating_hours" class="form-label">Operating Hours</label>
                                    <textarea name="operating_hours" id="operating_hours" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label for="previous_location" class="form-label">Previous Location</label>
                                    <input type="text" class="form-control" name="previous_location" id="previous_location" readonly>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label for="current_location" class="form-label">Current Location</label>
                                    <input type="text" class="form-control" name="current_location" id="current_location" readonly>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label for="next_location" class="form-label">Next Location</label>
                                    <input type="text" class="form-control" name="next_location" id="next_location" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-primary">Save Transport</button>
                    </form>
                </div>
            </div>
        </div>
        {{-- <div class="col-xl-4">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection
