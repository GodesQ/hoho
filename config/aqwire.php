<?php

return [
    "success"=> [
        "book_tour"=> env('AQWIRE_SUCCESS_URL'),
        "travel_tax" => env('AQWIRE_TRAVEL_TAX_SUCCESS_URL'),
        "order" => env("AQWIRE_ORDER_SUCCESS_URL"),
        "hotel_reservation" => env('AQWIRE_HOTEL_RESERVATION_SUCCESS_URL'),
    ],
    "cancel"=> [
        "book_tour"=> env('AQWIRE_CANCEL_URL'),
        "travel_tax" => env('AQWIRE_TRAVEL_TAX_CANCEL_URL'),
        "order" => env("AQWIRE_ORDER_CANCEL_URL"),
        "hotel_reservation" => env('AQWIRE_HOTEL_RESERVATION_CANCEL_URL'),
    ],
    "callback"=> [
        "url" => env('AQWIRE_CALLBACK_URL'),
    ],
];