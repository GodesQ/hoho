<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelReservationResource extends JsonResource
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
            "room_id" => $this->room_id,
            "number_of_pax" => $this->number_of_pax,
            "reservation_date" => $this->reservation_date,
            "reservation_time" => $this->reservation_time,
            "status" => $this->status,
            "approved_date" => $this->approved_date,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
