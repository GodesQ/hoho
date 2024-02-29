<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'merchant_id' => $this->merchant_id,
            'name' => $this->name,
            'image' => $this->image,
            'description' => $this->description,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "other_images" => $this->other_images,
            "is_active" => $this->is_active,
            "merchant" => $this->when($request->merchant_id, $this->nerchant)
        ];
    }
}
