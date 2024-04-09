<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelTaxPaymentResource extends JsonResource
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
            'transaction_id' => $this->id,
            'transaction_number' => $this->transaction_number,
            'reference_number' => $this->reference_number,
            'transaction_time' => $this->transaction_time,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'processing_fee' => $this->processing_fee,
            'discount' => $this->discount,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'payment_time' => $this->payment_time,
            'status' => $this->status,
            'passengers' => TravelTaxPassengerResource::collection($this->passengers),
        ];
    }
}
