<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromocodeResource extends JsonResource
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
            'code' => $this->code,
            'description' => $this->description,
            'is_need_requirement' => $this->is_need_requirement,
            'type' => $this->type,
            'is_need_approval' => $this->is_need_approval,
            'discount_amount' => $this->discount_amount,
            'discount_type' => $this->discount_type,
        ];
    }
}
