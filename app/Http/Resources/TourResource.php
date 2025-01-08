<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
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
            'tour_provider_id' => $this->tour_provider_id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->when($request->tour_id, $this->description),
            'contact_no' => $this->contact_no,
            'featured_image' => asset('assets/img/tours/' . $this->id . '/' . $this->featured_image),
            'images' => $this->when($request->tour_id, $this->images),
            'interests' => $this->interests,
            'is_cancellable' => $this->when($request->tour_id, (boolean) $this->is_cancellable),
            'is_refundable' => $this->when($request->tour_id, (boolean) $this->is_refundable),
            'bypass_days' => (integer) $this->bypass_days,
            'disabled_days' => json_decode($this->disabled_days),
            'minimum_pax' => $this->minimum_pax,
            'tour_itinerary' => $this->when($request->tour_id, $this->tour_itinerary),
            'tour_inclusions' => $this->when($this->tour_id, $this->tour_inclusions),
            'operating_hours' => $this->when($request->tour_id, $this->operating_hours),
            'price' => (double) $this->price,
            'bracket_price_one' => (double) $this->bracket_price_one,
            'bracket_price_two' => (double) $this->bracket_price_two,
            'bracket_price_three' => (double) $this->bracket_price_three,
            'transport_id' => (integer) $this->transport_id,
            'organization_id' => (integer) $this->organization_id,
            'organization' => $this->when($request->tour_id, $this->organization),
            'attractions' => $this->when(is_array($this->attractions) || $this->attractions != null, AttractionResource::collection($this->attractions)),
        ];
    }
}
