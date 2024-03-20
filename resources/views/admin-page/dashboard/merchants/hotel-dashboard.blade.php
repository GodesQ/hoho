@extends('layouts.admin.layout')

@section('title', 'Hotel Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3 class="card-title text-primary">Good Day,
            {{ ucwords(str_replace('_', ' ', Auth::guard('admin')->user()->username)) }}! 🎉</h3>
        <div class="row">
            <div class="col-lg-6">
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
                        <h5 class="card-title">{{ $merchantInfo->name ?? null }}</h5>
                        <p class="card-text">
                            {{ substr($merchantInfo->description ?? '', 0, 250) }}...
                        </p>
                        <a href="{{ route('merchant_form', $type) }}" class="btn btn-outline-primary">Go to Merchant Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Hotel Reservation</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @forelse ($recent_hotel_reservations as $recent_hotel_reservation)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        @if ($recent_hotel_reservation->status == 'approved')
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
                                                class="text-muted d-block mb-1">{{ $recent_hotel_reservation->reserved_user ? $recent_hotel_reservation->reserved_user->email : 'Deleted User' }}</small>
                                            <h6 class="mb-0">
                                                <a
                                                    href="{{ route('admin.hotel_reservations.edit', $recent_hotel_reservation->id) }}">{{ $recent_hotel_reservation->room->room_name ?? null }}</a>
                                            </h6>
                                        </div>
                                        <div class="user-progress text-end">
                                            <small class="text-mute d-block mb-1">
                                                {{ $recent_hotel_reservation->reservation_date->format('M d, Y') }}
                                                {{ date_format(new \DateTime($recent_hotel_reservation->reservation_time), 'h:i A') }}
                                            </small>
                                            <h6 class="mb-0 text-end">{{ $recent_hotel_reservation->number_of_pax }} Pax
                                            </h6>
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
