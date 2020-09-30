<?php
namespace App\Services;

use App\Mail\Invoice as MailInvoice;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class Payment
{
    public function generateInvoiceNumber(): string
    {
        $invoice = Invoice::latest()->first();
        $lastNumber = 0;
        if ($invoice) {
            $lastNumber = $invoice->id;
        }

        return date('YmdHis')+$lastNumber;
    }

    public function sendInvoice(User $user): void
    {
        Mail::to($user->email)->send(new MailInvoice($user));
    }

    public function savePayment(User $user, $request)
    {
        $invoice = Invoice::where('user_id', $user->id)->latest()->first();

        $invoice->bank_id           = $request->bank_id;
        $invoice->buyer             = $request->buyer;
        $invoice->transfer_amount   = $request->transfer_amount;
        $invoice->bank_account      = $request->bank_account;
        $invoice->payment_date      = date('Y-m-d H:i:s', strtotime($request->payment_date));
        $invoice->save();

        $file = $request->file('attachment');
        if (!empty($file) && $file->isValid()) {
            $changedName = time().random_int(100,999).$file->getClientOriginalName();
            $file->storeAs('payment/' . $invoice->id, $changedName);

            $invoice->payment_attachment = $changedName;
            $invoice->save();
        }
    }

    public function acceptPayment(Invoice $invoice)
    {
        $invoice->payment_confirm_at    = now();
        $invoice->valid_until           = now()->addYear();
        $invoice->save();
    }
}

