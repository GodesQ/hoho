@extends('layouts.admin.layout')

@section('title', 'Merchant Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3 class="card-title text-primary">Good Day,
            {{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->username)) }}! 🎉</h3>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    
                    @fileExists('assets/img/' . $type . 's/' . ($merchantInfo->id ?? null) . '/' . ($merchantInfo->featured_image ?? ''))
                        <img class="card-img-top" style="max-height: 400px; object-fit: cover;"
                            src="{{ URL::asset('assets/img/' . $type . 's/' . ($merchantInfo->id ?? null) . '/' . ($merchantInfo->featured_image ?? '')) }}">
                    @elsefileExists
                        <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                            id="previewImage1" alt="Default Image" width="100%" height="210px"
                            style="border-radius: 10px 10px 0px 0px; object-fit: cover;">
                    @endfileExists

                    <div class="card-body">
                        <h5 class="card-title">{{ ($merchantInfo->name ?? null) }}</h5>
                        <p class="card-text">
                            {{ substr(($merchantInfo->description ?? ''), 0, 250) }}...
                        </p>
                        <a href="{{ route('merchant_form', $type) }}" class="btn btn-outline-primary">Go to Merchant Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Book Reservation</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @forelse ($recentTourReservations as $recentTourReservation)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        @if ($recentTourReservation->status == 'approved')
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
                                                class="text-muted d-block mb-1">{{ $recentTourReservation->user ? $recentTourReservation->user->email : 'Deleted User' }}</small>
                                            <h6 class="mb-0">
                                                <a href="{{ route('admin.tour_reservations.edit', $recentTourReservation->id) }}">{{ $recentTourReservation->tour->name ?? null }}</a>
                                            </h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{ number_format($recentTourReservation->amount,2) }}</h6>
                                            {{-- <span class="text-muted">USD</span> --}}
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="d-flex mb-4 pb-1">No Tour Reservations Found</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
