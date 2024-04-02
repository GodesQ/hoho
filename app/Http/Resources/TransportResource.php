<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransportResource extends JsonResource
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
            'available_seats' => $this->available_seats,
            'capacity' => $this->capacity,
            'duration' => (integer) $this->duration,
            'transport_provider_id' => $this->transport_provider_id,
            'operator_id' => $this->operator_id,
            'tour_assigned_id' => $this->tour_assigned_id,
            'hub_id' => $this->hub_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'current_location' => json_decode($this->current_location),
            'next_location' => json_decode($this->nexlot_cation),
            'assigned_tour' => TourResource::make($this->assigned_tour),
        ];
    }
}
