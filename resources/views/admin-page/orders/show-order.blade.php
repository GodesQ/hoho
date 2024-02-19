@extends('layouts.admin.layout')

@section('title', 'Order Details - Philippines Hop On Hop Off')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="fw-bold">Order Details</h4>
        <div class="d-flex gap-1">
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">Edit Order <i class="bx bx-edit-alt"></i></a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">Back to List <i class="bx bx-undo"></i></a> 
        </div>
    </div>

    <form action="#" class="my-3">
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="main-container">
                            <h4>Customer</h4>
                            <div class="row">
                                <div class="col-xl-4">
                                    <label for="" class="form-label">Name</label>
                                    <h6 style="font-weight: 600;">{{ $order->customer->firstname }} {{ $order->customer->lastname }}</h6>
                                </div>
                                <div class="col-xl-4">
                                    <label for="" class="form-label">Email</label>
                                    <h6 style="font-weight: 600;">{{ $order->customer->email }}</h6>
                                </div>
                                <div class="col-xl-4">
                                    <label for="" class="form-label">Contact Number</label>
                                    <h6 style="font-weight: 600;">+{{ $order->customer->countryCode }} {{ $order->customer->contact_no }} ({{ $order->customer->isoCode }})</h6>
                                </div>
                            </div>
                            <hr>
                            <h4>Product</h4>
                            <div class="row">
                                <div class="col-lg-3">
                                    <img src="{{ asset('assets/img/products/1/' . $order->product->image) }}" class="w-100 rounded product-image" style="object-fit: cover;" alt="">
                                </div>
                                <div class="col-lg-6">
                                    <h5 style="margin-bottom: 10px;" class="product-name-text">{{ $order->product->name }}</h5>
                                    <h6 class="text-primary product-price-text" style="margin-bottom: 10px;">₱ {{ number_format($order->product->price, 2)}}</h6>
                                    <p class="product-description-text">{{ $order->product->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Order Summary</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Status</h6>
                            </div>
                            <div class="col-xl-6">
                                @if ($order->status == 'received')
                                    <div class="badge bg-label-success">Received</div>
                                @elseif($order->status == 'cancelled')
                                    <div class="badge bg-label-danger">Cancelled</div>
                                @elseif($order->status == 'pending')
                                    <div class="badge bg-label-warning">Pending</div>
                                @elseif($order->status == 'processing')
                                    <div class="badge bg-label-warning">Processing</div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Reference Code</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="pax_text">{{ $order->reference_code }}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Payment Method</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="pax_text">{{ Str::upper($order->payment_method) }}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Order Date</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="pax_text">{{ $order->order_date->format('F d, Y') }}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Quantity</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="pax_text">{{ $order->quantity }} x</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Sub Amount</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="pax_text">₱ {{ number_format($order->sub_amount, 2) }}</h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Total Amount</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="pax_text">₱ {{ number_format($order->total_amount, 2) }}</h6>
                            </div>
                        </div>
                        <div class="my-3 justify-content-between d-flex flex-column" style="gap: 10px;">
                            <div class="w-100">
                                <a href="{{ route('admin.transactions.edit', $order->transaction_id) }}"
                                    class="w-100 btn-outline-primary btn">View Transaction Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection