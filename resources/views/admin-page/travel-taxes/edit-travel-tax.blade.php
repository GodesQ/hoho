@extends('layouts.admin.layout')

@section('title', 'Edit Travel Tax - Philippines Hop On Hop Off')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Travel Tax Payment</h4>
            <a href="{{ route('admin.travel_taxes.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        {{-- Off Canvas --}}
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasEndLabel" class="offcanvas-title">Edit Passenger</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body  mx-0 flex-grow-0">
                <form action="{{ route('admin.travel_taxes.passengers.update') }}" id="passenger-form" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="firstname" id="firstname">
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lastname" id="lastname">
                    </div>
                    <div class="mb-3">
                        <label for="middlename" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middlename" id="middlename">
                    </div>
                    <div class="mb-3">
                        <label for="suffix" class="form-label">Suffix</label>
                        <input type="text" class="form-control" name="suffix" id="suffix">
                    </div>
                    <div class="mb-3">
                        <label for="passport_number" class="form-label">Passport Number</label>
                        <input type="text" class="form-control" name="passport_number" id="passport_number">
                    </div>
                    <div class="mb-3">
                        <label for="ticket_number" class="form-label">Ticket Number</label>
                        <input type="text" class="form-control" name="ticket_number" id="ticket_number">
                    </div>
                    <div class="mb-3">
                        <label for="email_address" class="form-label">Email Address</label>
                        <input type="text" class="form-control" name="email_address" id="email_address">
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number">
                    </div>
                    <div class="mb-3">
                        <label for="destination" class="form-label">Destination</label>
                        <input type="text" class="form-control" name="destination" id="destination">
                    </div>
                    <div class="mb-3">
                        <label for="departure_date" class="form-label">Departure Date</label>
                        <input type="date" class="form-control" name="departure_date" id="departure_date">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 d-grid w-100">Update</button>
                    <button type="button" class="btn btn-outline-secondary d-grid w-100" data-bs-dismiss="offcanvas">
                        Cancel
                    </button>
                </form>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <h5>Passenger Info</h5>
                <div class="accordion mt-3" id="passengers-list">
                    @foreach ($travel_tax->passengers as $key => $passenger)
                        <div class="card accordion-item {{ $loop->first ? 'active' : null }}">
                            <h2 class="accordion-header d-flex " id="heading{{ $key }}">
                                <button type="button" class="accordion-button" data-bs-toggle="collapse"
                                    data-bs-target="#accordion{{ $key }}" aria-expanded="true"
                                    aria-controls="accordion{{ $key }}">
                                    Passenger {{ $key + 1 }}
                                </button>
                                {{-- <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasEnd" data-id="{{ $passenger->id }}" aria-controls="offcanvasEnd" onclick="handleClickEdit(this)">
                                    <i class="bx bx-edit"></i>
                                </button> --}}
                            </h2>

                            <div id="accordion{{ $key }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : null }}"
                                data-bs-parent="#passengers-list">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">First
                                                Name</label>
                                            <h6>{{ $passenger->firstname }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Last
                                                Name</label>
                                            <h6>{{ $passenger->lastname }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Middle
                                                Name</label>
                                            <h6>{{ $passenger->middlename ?? 'N/A' }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for=""
                                                class="form-label fw-semibold text-primary">Suffix</label>
                                            <h6>{{ $passenger->suffix ?? 'N/A' }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Passport
                                                Number</label>
                                            <h6>{{ $passenger->passport_number }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Ticket
                                                Number</label>
                                            <h6>{{ $passenger->ticket_number }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Departure
                                                Date</label>
                                            <h6>{{ date_format(new \DateTime($passenger->departure_date), 'F d, Y') }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Passenger
                                                Type</label><br>
                                            <div
                                                class="badge bg-{{ $passenger->passenger_type == 'primary' ? 'primary' : 'secondary' }}">
                                                {{ $passenger->passenger_type }}</div>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Email
                                                Address</label>
                                            <h6>{{ $passenger->email_address }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for="" class="form-label fw-semibold text-primary">Mobile
                                                Number</label>
                                            <h6>{{ $passenger->mobile_number }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for=""
                                                class="form-label fw-semibold text-primary">Destination</label>
                                            <h6>{{ $passenger->destination }}</h6>
                                        </div>
                                        <div class="col-lg-3 my-2">
                                            <label for=""
                                                class="form-label fw-semibold text-primary">Class</label>
                                            <h6>{{ strtoupper($passenger->class) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            <div class="col-lg-4">
                <h5>Payment Summary</h5>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Payment Method:</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-end">{{ $travel_tax->payment_method }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Sub Amount:</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-end" id="sub-amount-text">₱ {{ number_format($travel_tax->amount, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Processing Fee:</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-end">₱ 100.00</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Discount:</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-end">₱ 0.00</div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Status:</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-end">{{ $travel_tax->status }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Total Amount:</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-end" id="total-amount-text">₱
                                    {{ number_format($travel_tax->total_amount, 2) }}</div>
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
        function handleClickEdit(e) {
            let passenger_id = e.getAttribute('data-id');

            $.ajax({
                url: `{{ route('admin.travel_taxes.passengers', '') }}` + '/' + passenger_id,
                method: 'GET',
                success: function(data) {
                    $('#id').val(data.passenger.id);
                    $('#firstname').val(data.passenger.firstname);
                    $('#lastname').val(data.passenger.lastname);
                    $('#middlename').val(data.passenger.middlename);
                    $('#suffix').val(data.passenger.suffix);
                    $('#passport_number').val(data.passenger.passport_number);
                    $('#ticket_number').val(data.passenger.ticket_number);
                    $('#email_address').val(data.passenger.email_address);
                    $('#contact_number').val(data.passenger.contact_number);
                    $('#destination').val(data.passenger.destination);
                    $('#departure_date').val(data.passenger.departure_date);
                }
            })
        }

        // $(document).ready(function() {
        //     $('#passenger-form').on('submit', function(e) {
        //         e.preventDefault(); // Prevent default form submission
        //         const formData = new FormData(this);
        //         // Make Ajax request
        //         $.ajax({
        //             url: '{{ route('admin.travel_taxes.passengers.update') }}',
        //             method: 'PUT',
        //             data: formData,
        //             cache: false,
        //             processData: false,
        //             contentType: false,
        //             success: function(data) {
        //                 console.log(data);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error(xhr.responseText);
        //             }
        //         });
        //     });
        // });
    </script>
@endpush
