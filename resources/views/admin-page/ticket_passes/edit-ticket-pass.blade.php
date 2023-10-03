@extends('layouts.admin.layout')

@section('title', 'Hop On Hop Off - Edit Ticket Pass')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Edit Ticket Pass</h4>
            <a href="{{ route('admin.ticket_passes.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.ticket_passes.update', $ticket_pass->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $ticket_pass->name }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="ticket_image" class="form-label">Ticket Image</label>
                                        <input type="file" class="form-control" name="ticket_image" id="ticket_image" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="text" class="form-control" placeholder="Amount"
                                                aria-label="Amount (to the nearest peso)" name="price" id="price" value="{{ $ticket_pass->price }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Ticket Pass</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Preview Featured Image</h6>
                        @if ($ticket_pass->ticket_image)
                            <img src="{{ URL::asset('assets/img/ticket_passes/'. $ticket_pass->ticket_image) }}" alt="Default Image" id="previewImage"
                            style="border-radius: 10px" width="100%">
                        @else
                            <img src="{{ URL::asset('assets/img/default-image.jpg') }}" alt="Default Image" id="previewImage"
                            style="border-radius: 10px" width="100%">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to handle file selection and display preview image
        function handleFileSelect(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        function handlePreviewImage(event, previewImageId) {
            const file = event.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const previewImage = document.getElementById(previewImageId);
                    console.log(previewImage);
                    previewImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        // Attach the 'handleFileSelect' function to the file input's change event
        document.getElementById('ticket_image').addEventListener('change', handleFileSelect);
    </script>
@endpush
