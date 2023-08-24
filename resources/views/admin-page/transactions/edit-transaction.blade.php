@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">View Transaction</h4>
        <a href="{{ route('admin.transactions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.transactions.edit', $transaction->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="reference_no" class="form-label">Reference No</label>
                            <input type="text" name="reference_no" id="reference_no" value="{{ $transaction->reference_no }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Transaction By</label>
                            <input type="text" name="transaction_by" id="transaction_by" value="{{ $transaction->user->email }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="payment_amount" class="form-label">Payment Amount</label>
                            <input type="text" name="payment_amount" id="payment_amount" value="{{ $transaction->payment_amount }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="aqwire_paymentMethodCode" class="form-label">Payment Type</label>
                            <input type="text" name="aqwire_paymentMethodCode" id="aqwire_paymentMethodCode" value="{{ $transaction->aqwire_paymentMethodCode }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="additional_charges" class="form-label">Additional Charges</label>
                            <textarea name="additional_charges" id="additional_charges" cols="30" rows="6" class="form-control" readonly>{{ $transaction->additional_charges }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="payment_details" class="form-label">Payment Details</label>
                            <textarea name="payment_details" id="payment_details" cols="30" rows="6" class="form-control" readonly>{{ $transaction->payment_details }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Payment URL</label>
                            <input type="text" class="form-control" value="{{ $transaction->payment_url }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="aqwire_transactionId" class="form-label">Aqwire Trasaction ID</label>
                            <input type="text" class="form-control" value="{{ $transaction->aqwire_transactionId }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="aqwire_referenceId" class="form-label">Aqwire Reference No</label>
                            <input type="text" class="form-control" value="{{ $transaction->aqwire_referenceId }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Order Date</label>
                            <input type="date" class="form-control" value="{{ $transaction->order_date }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="transaction_date" class="form-label">Transaction Date</label>
                            <input type="date" class="form-control" value="{{ $transaction->transaction_date }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" value="{{ $transaction->payment_date }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="aqwire_totalAmount" class="form-label">Aqwire Total Amount</label>
                            <input type="text" class="form-control" value="{{ $transaction->aqwire_totalAmount }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" disabled>
                                <option value="success" {{ $transaction->payment_status == 'success' ? 'selected' : null }}>Success</option>
                                <option value="cancelled" {{ $transaction->payment_status == 'cancelled' ? 'selected' : null }}>Cancelled</option>
                                <option value="failed" {{ $transaction->payment_status == 'failed' ? 'selected' : null }}>Failed</option>
                                <option value="inc" {{ $transaction->payment_status == 'inc' ? 'selected' : null }}>Incomplete</option>
                                <option value="pending" {{ $transaction->payment_status == 'pending' ? 'selected' : null }}>Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
