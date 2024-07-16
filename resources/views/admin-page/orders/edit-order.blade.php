@extends('layouts.admin.layout')

@section('title', 'Edit Order - Philippine Hop On Hop Off') 

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="fw-bold py-3 mb-4">Edit Order</h4>
        <div class="d-flex gap-1">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary">View Order Details <i class="bx bx-file"></i></a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark">Back to List <i class="bx bx-undo"></i></a> 
        </div>
    </div>

    <form action="{{ route('admin.orders.update', $order->id) }}" method="post">
        @csrf
        @method('PUT')
        <input type="hidden" name="product_price" id="product-price-field" value="{{ $order->product->price }}">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="product-field" class="form-label">Product</label>
                                    <input type="text" class="form-control" value="{{ $order->product->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="customer-field" class="form-label">Customer</label>
                                    <input type="text" class="form-control" value="{{ $order->customer->firstname . ' ' . $order->customer->lastname }}" readonly>
                                    <div class="text-danger">@error('customer_id'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="quantity-field" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" id="quantity-field" class="form-control" value="{{ $order->quantity }}" min="1">
                                    <div class="text-danger">@error('quantity'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="order-date-field" class="form-label">Order Date</label>
                                    <input type="date" name="order_date" id="order-date-field" value="{{ $order->order_date->format('Y-m-d') }}" class="form-control">
                                    <div class="text-danger">@error('order_date'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="status-field" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option {{ $order->status == 'pending' ? 'selected' : null }} value="pending">Pending</option>
                                        <option {{ $order->status == 'processing' ? 'selected' : null }} value="processing">Processing</option>
                                        <option {{ $order->status == 'received' ? 'selected' : null }} value="received">Received</option>
                                        <option {{ $order->status == 'cancelled' ? 'selected' : null }} value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4>Product</h4>
                        <div class="row">
                            <div class="col-lg-3">
                                <img src="{{ asset('assets/img/products/' . $order->product->id . '/' . $order->product->image) }}" class="w-100 rounded product-image" style="object-fit: cover;" alt="">                            </div>
                            <div class="col-lg-6">
                                <h5 style="margin-bottom: 10px;" class="product-name-text">{{ $order->product->name }}</h5>
                                <h6 class="text-primary product-price-text" style="margin-bottom: 10px;">₱ {{ number_format($order->product->price, 2)}}</h6>
                                <p class="product-description-text">{{ $order->product->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Order Summary</h5>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Sub Amount</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="sub-amount-text">₱ {{ number_format($order->sub_amount, 2) }}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Quantity</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="quantity-text">{{ $order->quantity }} x</h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Total Amount</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="total-amount-text">₱ {{ number_format($order->total_amount, 2) }}</h6>
                            </div>
                        </div>
                        <button class="btn btn-block w-100 btn-primary">Update Order</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        $('#product-field').on('change', function (e) {
            let product_id = e.target.value;
            $.ajax({
                url: "{{ route('admin.products.show', '') }}" + '/' + product_id,
                method: 'GET',
                success: function (data) {
                    displayProduct(data);
                    $('#product-price-field').val(data.product.price);
                    $('#sub-amount-text').text('₱ ' + data.product.price);  

                    calculateAmount();
                }
            });
        });

        $('#quantity-field').on('input', function (e) {
            $('#quantity-text').text(e.target.value + ' x');
            calculateAmount();
        });

        function calculateAmount() {
            let subAmountValue = $('#product-price-field').val() ;
            let quantity = $('#quantity-field').val();

            const totalAmount = (subAmountValue * quantity).toFixed(2);
            $('#total-amount-text').text('₱ ' + totalAmount);
        }

        function displayProduct(data) {
            $('.product-image').attr('src', '{{ asset("assets/img/products/") }}' + '/' + data.product.id + '/' + data.product.image);
            $('.product-name-text').text(data.product.name);
            $('.product-price-text').text('₱ ' + data.product.price);
            $('.product-description-text').text(data.product.description);
        }

    </script>
@endpush