<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package_name' => $this->package->name??null,
            'bank' => [
                'bank_name' => $this->bank->name??null,
                'bank_account' => $this->bank->account_number??null,
                'owner_name' => $this->bank->owner_name??null,
            ],
            'number' => $this->number,
            'price' => $this->price,
            'bank_account' => $this->bank_account,
            'transfer_amount' => $this->transfer_amount,
            'payment_date' => $this->payment_date,
            'payment_confirm_at' => $this->payment_confirm_at,
            'valid_until' => $this->valid_until,
            'created_at' => $this->created_at,
        ];
    }
}
