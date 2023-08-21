@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Create Tour Reservation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Create Tour Reservation</h4>
        <a href="{{ route('admin.tour_reservations.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="row">
        <div class="col-lg">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="steps-reservation-form">
                            <div class="steps-header">
                                <div class="step-one-icon step-icon active">Tour Type</div>
                                <div class="step-two-icon step-icon">Gen Form</div>
                                <div class="step-three-icon step-icon">Payment Method</div>
                                <div class="step-four-icon step-icon">Review</div>
                            </div>

                            {{-- Step 1 --}}
                            <div class="steps active-step">
                                <div class="step-one">
                                    <div class="tour-type" id="guided">
                                        <img src="{{ URL::asset('assets/img/icons/tour-guide.png') }}" alt="Guided Tour Icon" width="80">
                                        <h4>Guided Tour</h4>
                                    </div>
                                    <div class="tour-type selected" id="guided">
                                        <img src="{{ URL::asset('assets/img/icons/tour-diy.png') }}" alt="Guided Tour Icon" width="80">
                                        <h4>DIY Tour</h4>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="steps">
                                <div class="step-two">

                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="steps">
                                <div class="step-three">
                                    <div class="tour-type" id="guided">
                                        <img src="{{ URL::asset('assets/img/icons/tour-guide.png') }}" alt="Guided Tour Icon" width="80">
                                        <h4>Guided Tour</h4>
                                    </div>
                                    <div class="tour-type selected" id="guided">
                                        <img src="{{ URL::asset('assets/img/icons/tour-diy.png') }}" alt="Guided Tour Icon" width="80">
                                        <h4>DIY Tour</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="steps-bottom">
                                <button type="button" class="btn btn-dark"><i class="bx bx-arrow-from-right"></i> Previous</button>
                                <button type="button" class="btn btn-primary">Next <i class="bx bx-arrow-from-left"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
