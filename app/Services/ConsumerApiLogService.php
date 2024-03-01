<?php

namespace App\Services;

use App\Models\ApiConsumer;
use App\Models\ConsumerApiLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsumerApiLogService {
    // public function log(Request $request) {
    //     $apiKey = $request->header('X-API-Key');
    //     $apiCode = $request->header('x-api-code');
    //     $consumer = ApiConsumer::where('api_code', $apiCode)->where('api_key', $apiKey)->first();

    //     $log = ConsumerApiLog::create([
    //         'consumer_id' => $consumer->id,
    //         'request_timestamp' => Carbon::now(),
    //         'http_method' => $request->method(),
    //         'request_path' => $request->path(),
    //         'request_headers' => json_encode($request->header()),
    //         'request_body' => json_encode($request->all()),
    //         'ip_address' => $request->ip(),
    //         'user_agent' => $request->header('User-Agent'),
    //     ]);
        
    // }
}