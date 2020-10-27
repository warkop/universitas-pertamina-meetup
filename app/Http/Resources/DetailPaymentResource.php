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
            'payment_date' => optional($this->payment_date)->format('Y-m-d H:i:s'),
            'payment_confirm_at' => optional($this->payment_confirm_at)->format('Y-m-d H:i:s'),
            'valid_until' => optional($this->valid_until)->format('Y-m-d H:i:s'),
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'status' => $status,
            'action' => $action,
        ];
    }
}
