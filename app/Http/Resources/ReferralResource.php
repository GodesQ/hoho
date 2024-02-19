<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
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
            'referral_name' => $this->referral_name,
            'referral_code' => $this->referral_code,
            'merchant_id' => $this->merchant_id,
            'commision' => $this->commision,
        ];
    }
}
