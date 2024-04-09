<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelTaxPassengerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->when($request->travel_tax_id || $request->passenger_id, $this->payment_id),
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'suffix' => $this->suffix,
            'passport_number' => $this->when($request->travel_tax_id || $request->passenger_id, $this->passport_number),
            'ticket_number' => $this->when($request->travel_tax_id || $request->passenger_id, $this->ticket_number),
            'class' => $this->class,
            'mobile_number' => $this->mobile_number,
            'email_address' => $this->email_address,
            'destination' => $this->when($request->travel_tax_id || $request->passenger_id, $this->destination),
            'departure_date' => $this->when($request->travel_tax_id || $request->passenger_id, $this->departure_date),
            'passenger_type' => $this->passenger_type,
        ];
    }
}
