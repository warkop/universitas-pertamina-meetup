<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class ListForUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user();
        $invoice = (new Invoice)->getLastPaidedInvoice($user);
        $is_get = false;
        $is_pay_now = false;
        $is_upgrade = false;
        if ($invoice) {
            if ($this->id == $invoice->package_id && $this->renewal) {
                $is_pay_now = true;
                $is_upgrade = true;
            }
        } else {
            $is_upgrade = true;
        }


        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile' => $this->profile,
            'package_type' => $this->package_type,
            'announcement' => $this->announcement,
            'subscription_periode' => $this->subscription_periode,
            'renewal' => $this->renewal,
            'price' =>  $this->price,
            'institution_showed_in_home' => $this->institution_showed_in_home,
            'max_member' => $this->max_member,
            'member_showed_in_home' => $this->member_showed_in_home,
            'posting_opportunity' => $this->posting_opportunity,
            'order' => $this->order,
            'is_get' => $is_get,
            'is_pay_now' => $is_pay_now,
            'is_upgrade' => $is_upgrade,
        ];

    }
}
