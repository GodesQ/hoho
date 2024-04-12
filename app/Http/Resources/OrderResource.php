<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "id" => $this->id,
            "product_id" => $this->product_id,
            "customer_id" => $this->customer_id,
            "transaction_id" => $this->transaction_id,
            "reference_code" => $this->reference_code,
            "quantity" => $this->quantity,
            "sub_amount" => $this->sub_amount,
            "total_amount" => $this->total_amount,
            "payment_method" => $this->payment_method,
            "status" => $this->status,
            "order_date" => $this->order_date,
            "product" => $this->when($request->order_id || $request->user_id, $this->product),
            "customer" => $this->when($request->order_id, $this->customer)
        ];
    }
}
