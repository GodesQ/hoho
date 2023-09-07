@extends('layouts.admin.layout')

@section('title', 'Philippine Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Transaction Details</h4>
        <a href="{{ route('admin.transactions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Reference No: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>{{ $transaction->reference_no }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Transaction By: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>{{ $transaction->user->email }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Status: </h6>
                        </div>
                        <div class="col-lg-9">
                            @if($transaction->payment_status == 'success')
                                <div class="badge bg-success">Success</div>
                            @elseif($transaction->payment_status == 'cancelled')
                                <div class="badge bg-danger">Cancelled</div>
                            @elseif($transaction->payment_status == 'pending')
                                <div class="badge bg-warning">Pending</div>
                            @elseif($transaction->payment_status == 'failed')
                                <div class="badge bg-danger">Pending</div>
                            @elseif($transaction->payment_status == 'inc')
                                <div class="badge bg-warning">Pending</div>
                            @endif
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Total Amount: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>₱ {{ number_format($transaction->payment_amount, 2) }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Aqwire Total Amount: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6> {{ $transaction->aqwire_totalAmount, 2 }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Payment Type: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>{{ $transaction->aqwire_paymentMethodCode }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Additional Charges: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <?php $additional_charges = json_decode($transaction->additional_charges) ?>
                            <h6>
                                @foreach ($additional_charges as $propertyName => $propertyValue)
                                    <div class="my-1">{{  $propertyName . ': ' . number_format($propertyValue, 2) }}</div>
                                @endforeach
                            </h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Payment Details: </h6>
                        </div>
                        <div class="col-lg-9 py-3">
                           <textarea name="" id="" cols="30" rows="10" class="form-control" disabled>{{ $transaction->payment_details }}</textarea>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Payment Url: </h6>
                        </div>
                        <div class="col-lg-9 py-3">
                            <a href="{{ $transaction->payment_url }}" target="_blank">{{ $transaction->payment_url }}</a>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Aqwire Trasaction ID: </h6>
                        </div>
                        <div class="col-lg-9 py-3">
                            <span class="bg-dark p-2 text-white rounded" style="font-size: 12px;">{{ $transaction->aqwire_transactionId }}</span>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Aqwire Reference No: </h6>
                        </div>
                        <div class="col-lg-9 py-3">
                            <span class="bg-dark p-2 text-white rounded" style="font-size: 12px;">{{ $transaction->aqwire_referenceId }}</span>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Order Date: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>{{ date_format(new DateTime($transaction->order_date), 'F d, Y') }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Transaction Date: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>{{ date_format(new DateTime($transaction->transaction_date), 'F d, Y') }}</h6>
                        </div>
                    </div>
                    <div class="row align-items-center" style="border-bottom: 1px dashed lightgray;">
                        <div class="col-lg-3 pt-3">
                            <h6>Payment Date: </h6>
                        </div>
                        <div class="col-lg-9 pt-3">
                            <h6>{{ date_format(new DateTime($transaction->payment_date), 'F d, Y') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
