<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttractionResource extends JsonResource
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
            'attraction_provider' => $this->attraction_provider,
            'featured_image' => $this->featured_image,
            'organization_id' => $this->organization_id,
            'images' => $this->when($request->attraction_id, $this->images),
            'contact_no' => $this->when($request->attraction_id, $this->contact_no),
            'description' => $this->when($request->attraction_id, $this->description),
            'interest_ids' => $this->when($request->attraction_id, $this->interest_ids),
            'youtube_id' => $this->youtube_id,
            'product_category_ids' => $this->when($request->attraction_id, json_decode($this->product_category_ids)),
            'price' => $this->when($request->attraction_id, $this->price),
            'operating_hours' => $this->when($request->attraction_id, $this->operating_hours),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'nearest_attractions' => $this->when($request->attraction_id && $this->nearest_attraction_ids, $this->nearest_attractions),
            'nearest_hotels' => $this->when($request->attraction_id && $this->nearest_hotel_ids, $this->nearest_hotels),
            'nearest_stores' => $this->when($request->attraction_id && $this->nearest_store_ids, $this->nearest_stores),
            'nearest_restaurants' => $this->when($request->attraction_id && $this->nearest_restaurant_ids, $this->nearest_restaurants),
        ];
    }

}
