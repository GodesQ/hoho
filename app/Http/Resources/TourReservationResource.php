<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourReservationResource extends JsonResource
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
            "id"=> $this->id,
            "tour_id"=> $this->tour_id,
            "type"=> $this->type,
            "total_additional_charges"=> $this->total_additional_charges,
            "discount"=> $this->discount,
            "sub_amount"=> $this->sub_amount,
            "amount"=> $this->amount,
            "reserved_user_id"=> $this->reserved_user_id,
            "reference_code"=> $this->reference_code,
            "order_transaction_id"=> $this->order_transaction_id,
            "start_date"=> $this->start_date,
            "end_date"=> $this->end_date,
            "status"=> $this->status,
            "number_of_pass"=> $this->number_of_pass,
            "ticket_pass"=> $this->ticket_pass,
            "payment_method"=> $this->payment_method,
            "referral_code"=> $this->referral_code,
            "promo_code"=> $this->promo_code,
            "requirement_file_path"=> $this->requirement_file_path,
            "discount_amount"=> $this->discount_amount,
            // "passengers"=> $this->when($this->passenger_ids, $this->passengers),
            "tour" => $this->tour,
            "feedback" => $this->feedback,
        ];
    }
}
