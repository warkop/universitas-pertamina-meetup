<?php

namespace App\Http\Resources;

use App\Models\Invoice;
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
        if ($this->valid_until != null && $this->payment_date != null) {
            $status = 'Accepted';
        } else if ($this->payment_date != null && $this->valid_until == null) {
            $status = 'Pending';
        } else {
            $status = 'Unpaid';
        }

        $invoice = Invoice::where('user_id', $this->user_id)->count();
        if ($invoice > 1) {
            $action = 'Renew';
        } else {
            $action = 'New Member';
        }

        return [
            'id' => $this->id,
            'package_name' => $this->package->name??null,
            'bank' => [
                'bank_id' => $this->bank->id??null,
                'bank_name' => $this->bank->name??null,
                'bank_account' => $this->bank->account_number??null,
                'owner_name' => $this->bank->owner_name??null,
            ],
            'buyer' => $this->buyer,
            'number' => $this->number,
            'price' => $this->price,
            'bank_account' => $this->bank_account,
            'transfer_amount' => $this->transfer_amount,
            'payment_date' => $this->payment_date,
            'payment_confirm_at' => $this->payment_confirm_at,
            'valid_until' => $this->valid_until,
            'created_at' => $this->created_at,
            'status' => $status,
            'action' => $action,
        ];
    }
}
