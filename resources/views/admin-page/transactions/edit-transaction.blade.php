@extends('layouts.admin.layout')

@section('title', 'Test Edit')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold py-3 mb-4">Transaction Details</h4>
            <div>
                <a href="{{ route('admin.transactions.list') }}" class="btn btn-dark"><i class="bx bx-undo"></i> Back to
                    List</a>
                @if ($transaction->payment_status == 'success')
                    <a href="{{ route('admin.transactions.print', $transaction->id) }}" target="_blank" class="btn btn-dark"><i
                            class="bx bx-print"></i> Print</a>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="post">
            @csrf
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end align-items-center mb-4">
                                @if ($transaction->payment_status == 'success')
                                    <div class="badge bg-success">Success</div>
                                @elseif($transaction->payment_status == 'cancelled')
                                    <div class="badge bg-danger">Cancelled</div>
                                @elseif($transaction->payment_status == 'pending')
                                    <div class="badge bg-warning">Pending</div>
                                @elseif($transaction->payment_status == 'failed')
                                    <div class="badge bg-danger">Failed</div>
                                @elseif($transaction->payment_status == 'inc')
                                    <div class="badge bg-warning">Incompleted</div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="reference_no" class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" value="{{ $transaction->reference_no }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="aqwire_transactionId" class="form-label">Aqwire Transaction Id</label>
                                        <input type="text" class="form-control"
                                            value="{{ $transaction->aqwire_transactionId }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="aqwire_referenceId" class="form-label">Aqwire Reference Id</label>
                                        <input type="text" class="form-control"
                                            value="{{ $transaction->aqwire_referenceId }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="aqwire_paymentMethodCode" class="form-label">Aqwire Payment
                                            Method</label>
                                        <input type="text" class="form-control"
                                            value="{{ $transaction->aqwire_paymentMethodCode }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="aqwire_totalAmount" class="form-label">Aqwire Total Amount</label>
                                        <input type="text" class="form-control"
                                            value="{{ $transaction->aqwire_totalAmount }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="transaction_type" class="form-label">Transaction Type</label>
                                        <input type="text" class="form-control"
                                            value="{{ $transaction->transaction_type }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="payment_url" class="form-label">Payment URL</label> <br>
                                        <a href="{{ $transaction->payment_url }}"
                                            target="_blank">{{ $transaction->payment_url ?? 'No Payment URL Found' }}</a>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="payment_details" class="form-label">Payment Details</label>
                                        <textarea readonly id="payment_details" cols="30" rows="5" class="form-control">{{ $transaction->payment_details }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Order Date</label>
                                        <input type="date" readonly class="form-control" value="{{ $transaction->order_date }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Transaction Date</label>
                                        <input type="date" readonly class="form-control" value="{{ $transaction->transaction_date }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Payment Date</label>
                                        <input type="date" readonly class="form-control" value="{{ $transaction->payment_date }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center flex-column" style="gap: 10px;">
                                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                    style="width: 100px; height: 100px; border-radius: 50%;" alt="">
                                <div class="text-center">
                                    <h5>{{ $transaction->user->firstname ?? null }} {{ $transaction->user->lastname ?? null }}</h5>
                                    <h6>
                                        <div class="badge bg-label-primary">{{ $transaction->user->account_uid ?? null }}</div>
                                    </h6>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <h5 for="payment_status">Payment Status</h5>
                                <select name="payment_status" id="payment_status" class="form-select">
                                    <option {{ $transaction->payment_status == 'success' ? 'selected' : null }} value="success">Success</option>
                                    <option {{ $transaction->payment_status == 'cancelled' ? 'selected' : null }} value="cancelled">Cancelled</option>
                                    <option {{ $transaction->payment_status == 'pending' ? 'selected' : null }} value="pending">Pending</option>
                                    <option {{ $transaction->payment_status == 'failed' ? 'selected' : null }} value="failed">Failed</option>
                                    <option {{ $transaction->payment_status == 'inc' ? 'selected' : null }} value="inc">Incomplete</option>
                                </select>
                            </div>
                            <div class="my-4">
                                <h5 for="payment_status mb-2">Transaction Summary</h5>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h6>Total Of Additional Charges</h6>
                                    </div>
                                    <div class="col-xl-6 ">
                                        <h6 style="text-align: right;">₱ {{ number_format($transaction->total_additional_charges, 2) }}</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h6>Total Of Discount</h6>
                                    </div>
                                    <div class="col-xl-6 ">
                                        <h6 style="text-align: right;">₱ {{ number_format($transaction->total_discount, 2) }}</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h6>Sub Amount</h6>
                                    </div>
                                    <div class="col-xl-6 ">
                                        <h6 style="text-align: right;">₱ {{ number_format($transaction->sub_amount, 2) }}</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h6>Total Amount</h6>
                                    </div>
                                    <div class="col-xl-6 ">
                                        <h6 style="text-align: right;">₱ {{ number_format($transaction->payment_amount, 2) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-lg btn-primary" style="width: 100%;">Update Transaction</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
