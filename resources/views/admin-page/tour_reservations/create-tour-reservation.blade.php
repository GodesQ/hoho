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
                        @csrf
                        <input type="hidden" name="type" value="guided" id="type">
                        <div class="steps-reservation-form">
                            <div class="steps-header">
                                <div class="step-one-icon step-icon active">Tour Type</div>
                                <div class="step-two-icon step-icon">Gen Form</div>
                                <div class="step-three-icon step-icon">Payment Method</div>
                                <div class="step-four-icon step-icon">Review</div>
                            </div>

                            {{-- Step 1 --}}
                            <div class="steps py-2 active-step">
                                <div class="step-one">
                                    <div class="tour-type selected" id="guided" data-value="guided">
                                        <img src="{{ URL::asset('assets/img/icons/tour-guide.png') }}" alt="Guided Tour Icon" width="80">
                                        <h4>Guided Tour</h4>
                                    </div>
                                    <div class="tour-type" id="diy" data-value="diy">
                                        <img src="{{ URL::asset('assets/img/icons/tour-diy.png') }}" alt="DIY Tour Icon" width="80">
                                        <h4>DIY Tour</h4>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="steps py-2">
                                <div class="step-two my-2">
                                    @include('admin-page.tour_reservations.guided-tour-form.create-guided-tour-form')
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="steps">
                                <div class="step-three">
                                    Step Three
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="steps">
                                <div class="step-three">
                                    Step Four
                                </div>
                            </div>

                            <div class="steps-bottom">
                                <button type="button" class="btn btn-dark" id="prevButton" onclick="prevStep()"><i class="bx bx-arrow-from-right"></i> Previous</button>
                                <button type="button" class="btn btn-primary" id="nextButton" onclick="nextStep()">Next <i class="bx bx-arrow-from-left"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        let currentStep = 0;
        const stepIcons = document.querySelectorAll('.step-icon');
        const steps = document.querySelectorAll('.steps');
        const prevButton = document.getElementById("prevButton");
        const nextButton = document.getElementById("nextButton");

        function showStep(stepIndex) {
            stepIcons.forEach((icon, index)=> {
                if (index === stepIndex) {
                    icon.classList.add("active");
                } else {
                    icon.classList.remove("active");
                }
            });

            steps.forEach((step, index) => {
                if (index === stepIndex) {
                    step.classList.add("active-step");
                } else {
                    step.classList.remove("active-step");
                }
            })
        }

        function prevStep() {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);

                if(currentStep != steps.length - 1) {
                    nextButton.innerHTML = 'Next <i class="bx bx-arrow-from-left"></i>';
                }
            }
        }

        function nextStep() {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
                if(currentStep == steps.length - 1) {
                    nextButton.innerHTML = 'Submit <i class="bx bx-check"></i>';
                }
            } else {
                console.log(currentStep);
                // Handle form submission or completion here
                // alert("Form submitted!");
            }
        }

        let tourTypeButtons = document.querySelectorAll('.tour-type');

        tourTypeButtons.forEach((tourTypeButton, index) => {
            tourTypeButton.addEventListener('click', function(e) {

                for (let index = 0; index < tourTypeButtons.length; index++) {
                    const element = tourTypeButtons[index];
                    element.classList.remove('selected');
                }
                let type_value = e.target.getAttribute('data-value');
                e.target.classList.add('selected');
                $('#type').val(type_value);
            })
        })

        $('.reserved_users').select2({
            placeholder: 'Select users',
            minimumInputLength: 3, // Minimum characters before making a request
            ajax: {
                url: `{{ route('admin.users.lookup') }}`, // Replace with your Laravel route
                dataType: 'json',
                delay: 350, // Delay in milliseconds before sending the request
                processResults: function(data) {
                    console.log(data);
                    return {
                        results: data
                    };
                }
            }
        });

        $('.registered_passengers').select2({
            placeholder: 'Select users',
            minimumInputLength: 3, // Minimum characters before making a request
            ajax: {
                url: `{{ route('admin.users.lookup') }}`, // Replace with your Laravel route
                dataType: 'json',
                delay: 350, // Delay in milliseconds before sending the request
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });


    </script>
@endpush
