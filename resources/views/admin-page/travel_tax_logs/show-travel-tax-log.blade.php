@extends('layouts.admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="fw-bold">Log Details</h4>
            <div class="d-flex gap-1">
                <a href="{{ route('admin.travel_tax_logs.list') }}" class="btn btn-outline-dark">Back to List <i
                        class="bx bx-undo"></i></a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label for="ar-number-field" class="form-label">AR Number</label>
                                <input type="text" class="form-control"
                                    value="{{ $travel_tax_log->travel_tax->ar_number ?? 'N/A' }}"
                                    style="pointer-events: none;">
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="ar-number-field" class="form-label">Reference Number</label>
                                <input type="text" class="form-control"
                                    value="{{ $travel_tax_log->travel_tax->reference_number ?? 'N/A' }}"
                                    style="pointer-events: none;">
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="ar-number-field" class="form-label">Status Code</label> <br>
                                @if ((int) $travel_tax_log->status_code >= 200 && (int) $travel_tax_log->status_code < 300)
                                    <div class="badge bg-success">{{ $travel_tax_log->status_code }}</div>
                                @else
                                    <div class="badge bg-danger">{{ $travel_tax_log->status_code }}</div>
                                @endif
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="" class="form-label">Response</label>
                                <textarea name="" id="response-field" cols="30" rows="10" class="form-control" readonly>{{ $travel_tax_log->response }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3"></div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let response_field = document.querySelector('#response-field');
            let obj = JSON.parse(response_field.value);
            response_field.value = JSON.stringify(obj, undefined, 4);
        })
    </script>
@endpush
