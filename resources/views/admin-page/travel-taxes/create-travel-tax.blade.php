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
                <div class="col-xl-8" style="padding-right: 5px !important;">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger my-2 mb-3" style="border-left: 5px solid red;">
                                    Invalid Fields. Please check all fields before submitting the form.
                                </div>
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div id="passengers-list-repeater">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4><i class="bx bx-user"></i> Passengers</h4>
                                    <button class="btn btn-primary" id="add-passenger-btn" type="button">Add Passenger <i
                                            class="bx bx-plus"></i></button>
                                </div>
                                <div class="passenger-container row border p-2 px-1 my-2 rounded">
                                    <input type="hidden" name="passengers[0][amount]" value="1620" class="amount-field">
                                    <div class="col-lg-12 px-1 py-2 d-flex justify-content-between">
                                        <h5>Amount: <span class="text-primary amount-text">1620.00</span></h5>
                                        <button class="btn btn-sm btn-secondary" type="button">Remove <i
                                                class="bx bx-x"></i></button>
                                    </div>
                                    <div class="col-lg-3 px-1 py-2">
                                        <label for="firstname" class="form-label">Firstname</label>
                                        <input type="text" class="form-control" name="passengers[0][firstname]"
                                            placeholder="Firstname..." required>
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label for="lastname" class="form-label">Lastname</label>
                                        <input type="text" class="form-control" name="passengers[0][lastname]"
                                            placeholder="Lastname..." required>
                                    </div>
                                    <div class="col-lg-3 px-1 py-2">
                                        <label for="middlename" class="form-label">Middlename</label>
                                        <input type="text" class="form-control" name="passengers[0][middlename]"
                                            placeholder="Middlename...">
                                    </div>
                                    <div class="col-lg-2 px-1 py-2">
                                        <label for="suffix" class="form-label">Suffix</label>
                                        <input type="text" class="form-control" name="passengers[0][suffix]"
                                            placeholder="Suffix...">
                                    </div>
                                    <div class="col-lg-6 px-1 py-2">
                                        <label for="passport_number" class="form-label">Passport Number</label>
                                        <input type="text" class="form-control" name="passengers[0][passport_number]"
                                            placeholder="Passport Number..." required>
                                    </div>
                                    <div class="col-lg-6 px-1 py-2">
                                        <label for="ticket_number" class="form-label">Ticket Number</label>
                                        <input type="text" class="form-control" name="passengers[0][ticket_number]"
                                            placeholder="Ticket Number..." required>
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label class="form-label">Class</label>
                                        <div class="form-check ">
                                            <input name="passengers[0][class]" class="form-check-input class-field-radio"
                                                type="radio" value="first class" id="firstClassOption1" />
                                            <label class="form-check-label" for="firstClassOption1"> First Class </label>
                                        </div>
                                        <div class="form-check ">
                                            <input name="passengers[0][class]" class="form-check-input class-field-radio"
                                                type="radio" value="business class" id="businessClassOption1" checked />
                                            <label class="form-check-label" for="businessClassOption1"> Economy/Business Class
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label for="mobile_number" class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" name="passengers[0][mobile_number]"
                                            placeholder="Mobile Number..." required>
                                    </div>
                                    <div class="col-lg-4 px-1 py-2">
                                        <label for="email_address" class="form-label">Email Address</label>
                                        <input type="text" class="form-control" name="passengers[0][email_address]"
                                            placeholder="Email Address..." required>
                                    </div>
                                    <div class="col-xl-5 px-1 py-2">
                                        <label for="destination" class="form-label">Destination</label>
                                        <select name="passengers[0][destination]" required
                                            class="form-select destination-field">
                                            <option value="">--- SELECT DESTINATION ---</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 px-1 py-2">
                                        <label for="departure_date" class="form-label">Departure Date</label>
                                        <input type="date" class="form-control" name="passengers[0][departure_date]"
                                            required>
                                    </div>
                                    <div class="col-xl-3 px-1 py-2">
                                        <label for="" class="form-label">Passenger Type</label>
                                        <select name="passengers[0][passenger_type]" id="" class="form-select"
                                            required>
                                            <option value="">--- SELECT PASSENGER TYPE ---</option>
                                            <option value="primary">Primary</option>
                                            <option value="normal">Normal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="user-field" class="form-label">Payor <span
                                        class="text-danger">*</span></label>
                                <select name="user_id" id="user-field" required></select>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6>Sub Amount:</h6>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-end" id="sub-amount-text">₱ 0.00</div>
                                    <input type="hidden" name="amount" id="sub-amount-field">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6>Processing Fee:</h6>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-end">5 %</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6>Total Processing Fee:</h6>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-end" id="total-processing-fee-text">₱ 0.00</div>
                                    <input type="hidden" name="processing_fee" id="processing-fee-field"
                                        value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6>Discount:</h6>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-end">₱ 0.00</div>
                                    <input type="hidden" name="discount" id="discount-field" value="0">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6>Total Amount:</h6>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-end" id="total-amount-text">₱ 0.00</div>
                                    <input type="hidden" name="total_amount" value="" id="total-amount-field">
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block w-100">Pay <i class="bx bx-send"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#user-field').select2({
                width: '100%',
                placeholder: 'Select user',
                minimumInputLength: 3,
                ajax: {
                    url: `{{ route('admin.users.lookup') }}`,
                    dataType: 'json',
                    delay: 350,
                    processResults: data => ({
                        results: data
                    })
                }
            });

            // Add Passenger button click event
            $("#add-passenger-btn").on("click", function() {
                // Clone the passenger container
                var clone = $(".passenger-container").first().clone();

                // Increment name attributes of input fields within the cloned container
                var newIndex = $(".passenger-container").length + 1;
                clone.find("input, select").each(function() {
                    var currentName = $(this).attr("name");
                    var newName = currentName.replace(/\[\d+\]/, "[" + newIndex + "]");
                    $(this).attr("name", newName);
                });


                // Update the id and for attributes for the radio buttons and labels
                clone.find("input[type=radio]").each(function() {
                    var currentId = $(this).attr("id");
                    var newId = currentId.replace(/\d+$/,newIndex); // Increment the number at the end of the id
                    $(this).attr("id", newId);

                    // Update the corresponding label's for attribute
                    $(this).next("label").attr("for", newId);
                });

                // Clear input fields within the cloned container
                clone.find("input[type=text], input[type=date]").val('');
                // clone.find("input[type=radio]").prop('checked', true);
                clone.find("select").val('');

                // Append the cloned container after the last one
                $("#passengers-list-repeater").append(clone);

                clone.find('.class-field-radio').on('change', function(e) {
                    let value = e.target.value;
                    changeClass(value, e.target);
                });

                fetchCountries();
                computeSubAndTotalAmount();
            });

            // Remove Passenger button click event
            $(document).on("click", "button.btn-secondary", function() {
                const passengerContainers = document.querySelectorAll('.passenger-container');
                if (passengerContainers.length > 1) {
                    $(this).closest(".passenger-container").remove();
                }

                computeSubAndTotalAmount();
            });
        });

        $('.class-field-radio').change(function(e) {
            let value = e.target.value;
            changeClass(value, e.target);
        });

        function changeClass(classValue, element) {
            let amountField = $(element).closest('.passenger-container').find('.amount-field');
            let amountText = $(element).closest('.passenger-container').find('.amount-text');

            if (classValue === 'business class') {
                amountField.val(1620);
                amountText.text('1620.00');
            } else {
                amountField.val(2700);
                amountText.text('2700.00');
            }
            computeSubAndTotalAmount();
        }

        function computeSubAndTotalAmount() {
            let amountFields = document.querySelectorAll('.amount-field');
            let subAmount = 0;

            amountFields.forEach(element => {
                subAmount += parseInt(element.value);
            });

            let totalAmount = subAmount;


            $('#sub-amount-text').text(parseInt(subAmount).toFixed(2));
            $('#sub-amount-field').val(parseInt(subAmount));

            $('#total-amount-text').text(parseInt(totalAmount).toFixed(2));
            $('#total-amount-field').val(parseInt(subAmount));

            computeProcessingFee(totalAmount);
        }

        function computeProcessingFee(totalAmount) {
            let total_processing_fee = totalAmount * 0.05;
            totalAmount = totalAmount + total_processing_fee;

            $("#total-processing-fee-text").text('₱ ' + parseInt(total_processing_fee).toFixed(2));
            $("#processing-fee-field").val(parseInt(total_processing_fee));

            $('#total-amount-text').text('₱ ' + parseInt(totalAmount).toFixed(2));
            $('#total-amount-field').val(parseInt(totalAmount));

        }

        function fetchCountries() {
            let destinationField = document.querySelector('.destination-field');
            fetch("{{ URL::asset('assets/data/countries.json') }}")
                .then(response => response.json())
                .then(data => {
                    data.forEach(country => {
                        const option = document.createElement('option');
                        option.value = country;
                        option.text = country;
                        destinationField.add(option);
                    });
                })
        }

        fetchCountries();
        computeSubAndTotalAmount();
    </script>

    <script></script>
@endpush
