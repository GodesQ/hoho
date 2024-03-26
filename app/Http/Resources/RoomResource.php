<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            "id"=> $this->id,
            "merchant_id" => $this->merchant_id,
            "room_name" => $this->room_name,
            "image" => $this->room_name,
            "price" => $this->price,
            "available_pax" => $this->available_pax,
            "amenities" => $this->amenities,
            "description" => $this->description,
            "other_images" => $this->other_images ? json_decode($this->other_images) : null,
            "product_categories" => $this->product_categories ? json_decode($this->product_categories) : null,
            "is_cancellable" => (bool) $this->is_cancellable,
            "is_refundable" => (bool) $this->is_refundable,
            "is_active" => (bool) $this->is_active,
            "merchant" => $this->when($request->room_id, $this->merchant),
        ];
    }
}
