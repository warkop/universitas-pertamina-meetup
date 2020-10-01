<?php

namespace App\Transformers;

use App\InvoiceAsAdmin;
use App\Models\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceAsAdminTransformer extends TransformerAbstract
{
    /**
     * @param \App\InvoiceAsAdmin $invoiceAsAdmin
     * @return array
     */
    public function transform(Invoice $invoice)
    {
        $type = request()->type;
        if ($type == 0) {

        } else if ($type == 1) {

        }

        return [
            'id'            => $invoice->id,
            'user'  => $invoice->user->name,
        ];
    }
}
