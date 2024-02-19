@extends('layouts.admin.layout')

@section('title', 'Add Order - Philippine Hop On Hop Off') 

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">Add Order</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Back to List <i class="bx bx-undo"></i></a>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="post">
        @csrf
        <input type="hidden" name="product_price" id="product-price-field" value="0">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="product-field" class="form-label">Product</label>
                                    <select name="product_id" id="product-field" class="products form-select"></select>
                                    <div class="text-danger">@error('product_id'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="customer-field" class="form-label">Customer</label>
                                    <select name="customer_id" id="customer-field" class="customers form-select"
                                        style="width: 100%;">
                                    </select>
                                    <div class="text-danger">@error('customer_id'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="quantity-field" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" id="quantity-field" class="form-control" value="1">
                                    <div class="text-danger">@error('quantity'){{ $message }}@enderror</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="order-date-field" class="form-label">Order Date</label>
                                    <input type="date" name="order_date" id="order-date-field" value="{{ date('Y-m-d') }}" class="form-control">
                                    <div class="text-danger">@error('order_date'){{ $message }}@enderror</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4>Product Review</h4>
                        <div class="row">
                            <div class="col-lg-3">
                                <img src="{{ asset('assets/img/default-image.jpg') }}" class="w-100 rounded product-image" style="object-fit: cover;" alt="">
                            </div>
                            <div class="col-lg-6">
                                <h5 style="margin-bottom: 10px;" class="product-name-text">Product Name</h5>
                                <h6 class="text-primary product-price-text" style="margin-bottom: 10px;">₱ 0.00</h6>
                                <p class="product-description-text">Description..</p>
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
                                <h6 id="sub-amount-text">₱ 0.00</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Quantity</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="quantity-text">1</h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <h6 class="text-primary">Total Amount</h6>
                            </div>
                            <div class="col-xl-6">
                                <h6 id="total-amount-text">₱ 0.00</h6>
                            </div>
                        </div>
                        <button class="btn btn-block w-100 btn-primary">Submit Order</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        $('.customers').select2({
            placeholder: 'Select users',
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

        $('#product-field').select2({
            placeholder: 'Select products',
            minimumInputLength: 3,
            ajax: {
                url: `{{ route('admin.products.lookup') }}`,
                dataType: 'json',
                delay: 350,
                processResults: data => ({
                    results: data
                })
            }
        });

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
            $('#quantity-text').text(e.target.value);
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