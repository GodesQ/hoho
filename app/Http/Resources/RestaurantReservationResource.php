<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantReservationResource extends JsonResource
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
            "id" => $this->id,
            "reserved_user_id" => $this->reserved_user_id,
            "merchant_id" => $this->merchant_id,
            "seats" => $this->seats,
            "reservation_date" => $this->reservation_date,
            "reservation_time" => $this->reservation_time,
            "food_ids" => $this->food_ids,
            "status" => $this->status,
            "approved_date" => $this->approved_date,
            "merchant" => MerchantResource::make($this->merchant),
        ];
    }
}
