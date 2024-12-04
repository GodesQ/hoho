<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'aqwire' => [
        'merchant_code' => env('AQWIRE_MERCHANT_CODE'),
        'client_id' => env('AQWIRE_MERCHANT_CLIENTID'),
        'secret_key' => env('AQWIRE_MERCHANT_SECURITY_KEY')
    ],

    'checkout' => [
        'type' => "HPP",
    ],

    'travel_tax_hoho_token' => 'aem3eex$i7oaLohqueegish3chai7pHu7THow3ain9ohQua*sia7webohHeeQU9sah4Ohth[ez4ob3up9pa3vIo4daexoo9Eiz7noovah4uu{shie^xohc7aiph9johxeIkoa3osh7tho7OhgU>r9kex4ahch9gaipae]cees7rAe7vooraI7chUb3ath9quier4Vah7eeMah-th3aeng7CeefooZo7ek;oosh9eeph9aepie9oom7ootee9oog',

];
