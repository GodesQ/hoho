<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'featured_image' => $this->featured_image,
            'icon' => $this->icon,
            'description' => $this->when($request->organization_id, $this->description),
            'acronym' => $this->when($request->organization_id, $this->acronym),
            'visibility' => $this->when($request->organization_id, $this->visibility),
            'region' => $this->when($request->organization_id, $this->region),
            'is_active' => $this->is_active,
            'attractions' => $this->when($request->organization_id, MerchantResource::collection($this->attractions)),
            'stores' => $this->when($request->organization_id, MerchantResource::collection($this->stores)),
            'restaurants' => $this->when($request->organization_id, MerchantResource::collection($this->restaurants)),
            'hotels' => $this->when($request->organization_id, MerchantResource::collection($this->hotels)),
        ];
    }
}
