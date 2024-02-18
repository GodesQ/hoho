<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'featured_image' => $this->featured_image,
            'main_featured_image' => $this->main_featured_image,
            'nature_of_business' => $this->when($request->merchant_id, $this->nature_of_business),
            'organization_id' => $this->organization_id,
            'code' => $this->when($request->merchant_id, $this->code),
            'description' => $this->when($request->merchant_id, $this->description),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'store_info' => $this->when($this->type == 'Store' && ($request->merchant_id || $request->is('api/*/merchants')), $this->store_info),
            'restaurant_info' => $this->when($this->type == 'Restaurant' && ($request->merchant_id || $request->is('api/*/merchants')), $this->restaurant_info),
            'hotel_info' => $this->when($this->type == 'Hotel' && ($request->merchant_id || $request->is('api/*/merchants')), $this->hotel_info),
        ];
    }
}
