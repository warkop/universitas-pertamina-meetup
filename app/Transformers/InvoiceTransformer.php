<?php

namespace App\Transformers;

use App\Models\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceTransformer extends TransformerAbstract
{
    /**
     * @param \App\Invoice $invoice
     * @return array
     */
    public function transform(Invoice $invoice)
    {
        if ($invoice->valid_until != null && $invoice->payment_date != null) {
            $status = 'Accepted';
        } else if ($invoice->payment_date != null && $invoice->valid_until == null) {
            $status = 'Pending';
        } else {
            $status = 'Unpaid';
        }


        $invoices = Invoice::where('user_id', $invoice->user_id)->count();

        if ($invoices > 1) {
            $necessity = 'Renew Package';
        } else if ($invoices == 1) {
            $necessity = 'New Member';
        } else {
            $necessity = 'Free Tier';
        }

        $to = null;

        if ($invoice->bank)  {
            $to = $invoice->bank->name.' - '.$invoice->bank->account_number;
        }

        return [
            'id'            => $invoice->id,
            'package'       => $invoice->package->name,
            'to'            => $to,
            'number'        => $invoice->number,
            'necessity'     => $necessity,
            'status'        => $status,
            'created_at'    => $invoice->created_at->format('d-m-Y'),
        ];
    }
}
