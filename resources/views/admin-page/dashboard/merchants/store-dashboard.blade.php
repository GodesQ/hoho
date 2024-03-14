@extends('layouts.admin.layout')

@section('title', 'Store Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3 class="card-title text-primary">Good Day,
            {{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->username)) }}! 🎉</h3>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <img class="card-img-top" style="max-height: 400px; object-fit: cover;" src="{{ URL::asset('assets/img/' . $type . 's/' . ($merchantInfo->id ?? null) . '/' . ($merchantInfo->featured_image ?? '')) }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ ($merchantInfo->name ?? null) }}</h5>
                        <p class="card-text">
                            {{ substr(($merchantInfo->description ?? ''), 0, 250) }}...
                        </p>
                        <a href="{{ route('merchant_form', $type) }}" class="btn btn-outline-primary">Go to Merchant Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Store Orders</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @forelse ($recent_store_orders as $recent_store_order)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        @if ($recent_store_order->status == 'approved')
                                            <img src="{{ URL::asset('assets/img/icons/unicons/transaction-success.png') }}"
                                                alt="User" class="rounded" />
                                        @else
                                            <img src="{{ URL::asset('assets/img/icons/unicons/transaction-warning.png') }}"
                                                alt="User" class="rounded" />
                                        @endif
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small
                                                class="text-muted d-block mb-1">{{ $recent_store_order->customer ? $recent_store_order->customer->email : 'Deleted User' }}</small>
                                            <h6 class="mb-0">
                                                <a href="{{ route('admin.orders.edit', $recent_store_order->id) }}">{{ $recent_store_order->product->name ?? null }}</a>
                                            </h6>
                                        </div>
                                        <div class="user-progress text-end">
                                            <small class="text-mute d-block mb-1">
                                               {{ $recent_store_order->payment_method }}
                                            </small> 
                                            <h6 class="mb-0 text-end">{{ number_format($recent_store_order->total_amount, 2) }}</h6>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="d-flex mb-4 pb-1">No Hotel Reservations Found</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
