<?php

namespace App\Services;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\Http;

class SenangdaliService
{
    public function purchasing($body, $reservation)
    {
        try
        {
            $url = "https://api-commercial-dev.senang.io/api/hoponhopoff/purchasing";

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post($url, $body);

            $statusCode = $response->getStatusCode();

            if (! $response->successful())
            {
                $reservation->reservation_insurance->update([
                    'api_status_code' => $statusCode,
                    'api_response_body' => $response->getBody()->getContents(),
                ]);
            }
            $responseData = json_decode($response->getBody(), true);
            return $responseData;
        } catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function __map_request_model($user, $reservation)
    {
        return [
            "name" => ($user->firstname ?? " ")." ".($user->lastname ?? " "),
            "id_no" => rand(100000, 10000000),
            "email_id" => $user->email,
            "travel_date" => $reservation->start_date,
            "type_of_plan" => $reservation->reservation_insurance->type_of_plan,
            "no_of_pax" => [(string) $reservation->number_of_pass],
            "ticket_id" => $reservation->number_of_pass <= 1 ? [$reservation->reservation_codes[0]->code] : $reservation->reservation_codes->map(function ($reservation_code) {
                return $reservation_code->code;
            })->toArray(),
        ];
    }
}