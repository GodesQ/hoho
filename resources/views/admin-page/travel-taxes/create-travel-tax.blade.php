@extends('layouts.admin.layout')

@section('title', 'Add Travel Tax Payment - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Add Travel Tax Payment</h4>
            <a href="{{ route('admin.travel_taxes.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <form action="{{ route('admin.travel_taxes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body">

                            <div id="passengers-list-repeater">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4><i class="bx bx-user"></i> Passengers</h4>
                                    <button class="btn btn-primary" type="button">Add Passenger <i class="bx bx-plus"></i></button>
                                </div>
                                
                                <div class="passenger-container row border p-2 px-1 my-2 rounded">
                                    <div class="col-lg-12 px-1 py-2 d-flex justify-content-between">
                                        <h5>Amount: <span class="text-primary">0.00</span></h5>
                                        <button class="btn btn-sm btn-secondary" type="button">Remove <i class="bx bx-x"></i></button>
                                    </div>
                                    <div class="col-lg-3 px-1 py-2">
                                        <label for="firstname" class="form-label">Firstname</label>
                                        <input type="text" class="form-control" name="firstname[]" placeholder="Firstname...">
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label for="lastname" class="form-label">Lastname</label>
                                        <input type="text" class="form-control" name="lastname[]" placeholder="Lastname...">
                                    </div>
                                    <div class="col-lg-3 px-1 py-2">
                                        <label for="middlename" class="form-label">Middlename</label>
                                        <input type="text" class="form-control" name="middlename[]" placeholder="Middlename...">
                                    </div>
                                    <div class="col-lg-2 px-1 py-2">
                                        <label for="suffix" class="form-label">Suffix</label>
                                        <input type="text" class="form-control" name="suffix[]" placeholder="Suffix...">
                                    </div>
                                    <div class="col-lg-6 px-1 py-2">
                                        <label for="passport_number" class="form-label">Passport Number</label>
                                        <input type="text" class="form-control" name="passport_number[]" placeholder="Passport Number...">
                                    </div>
                                    <div class="col-lg-6 px-1 py-2">
                                        <label for="ticket_number" class="form-label">Ticket Number</label>
                                        <input type="text" class="form-control" name="ticket_number[]" placeholder="Ticket Number...">
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label class="form-label">Class</label>
                                        <div class="form-check ">
                                            <input name="class[]" class="form-check-input" type="radio"
                                                value="first class" id="firstClassOption" />
                                            <label class="form-check-label" for="firstClassOption"> First Class </label>
                                        </div>
                                        <div class="form-check ">
                                            <input name="class[]" class="form-check-input" type="radio"
                                                value="business class" id="businessClassOption" />
                                            <label class="form-check-label" for="businessClassOption"> Business Class </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label for="mobile_number" class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" name="mobile_number[]" placeholder="Mobile Number...">
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label for="email_address" class="form-label">Email Address</label>
                                        <input type="text" class="form-control" name="email_address[]" placeholder="Email Address...">
                                    </div>
                                    <div class="col-xl-6 px-1 py-2">
                                        <label for="destination" class="form-label">Destination</label>
                                        <select name="destination[]" class="select2">
                                            <option value="">--- SELECT DESTINATION ---</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-6 px-1 py-2">
                                        <label for="departure_date" class="form-label">Departure Date</label>
                                        <input type="date" class="form-control" name="departure_date[]" >
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
