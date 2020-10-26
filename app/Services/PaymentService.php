<?php
namespace App\Services;

use App\Helpers\HelperPublic;
use App\Jobs\SendAcceptPayment;
use App\Jobs\SendDeclinePayment;
use App\Jobs\SendInvoice;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\PaymentToken;
use App\Models\User;

class PaymentService
{
    public function registerPackage($package_id, $user)
    {
        $package = Package::findOrFail($package_id);

        if ($package->price > 0) {
            $number = $this->generateInvoiceNumber($user);
            $invoice = Invoice::create([
                'package_id'    => $package_id,
                'user_id'       => $user->id,
                'price'         => $package->price,
                'number'        => $number,
            ]);

            $paymentToken = $this->generatePaymentToken($invoice);
            SendInvoice::dispatch($invoice);
            return $paymentToken;
        } else {
            $number = $this->generateInvoiceNumber($user);
            $invoice = Invoice::create([
                'package_id'    => $package_id,
                'user_id'       => $user->id,
                'price'         => $package->price,
                'number'        => $number,
                'payment_date'          => now(),
                'payment_confirm_at'    => now(),
                'valid_until'           => now()->addMonths(4)
            ]);

            $user = User::where('user.id', $user->id)->update(['confirm_at' => now()]);
        }
        return null;
    }

    public function generateInvoiceNumber(): string
    {
        $invoice = Invoice::latest()->first();
        $lastNumber = 0;
        if ($invoice) {
            $lastNumber = $invoice->id;
        }

        return date('YmdHis')+$lastNumber;
    }

    public function generatePaymentToken($invoice)
    {
        $paymentToken = new PaymentToken;

        $token = time().random_int(100,999);

        $paymentToken->token        = $token;
        $paymentToken->invoice_id   = $invoice->id;
        $paymentToken->expired      = now()->addHour(2);
        $paymentToken->save();
        return $token;
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

        return true;
    }

    public function saveUploadPayment(User $user, $request)
    {
        $file = $request->file('payment_attachment');
        $invoice = Invoice::where('user_id', $user->id)->latest()->first();
        if (!empty($file) && $file->isValid()) {
            $changedName = time().random_int(100,999).$file->getClientOriginalName();
            $file->storeAs('payment/' . $invoice->id, $changedName);

            $invoice->payment_attachment = $changedName;
            $invoice->save();
        }

        return true;
    }

    public function acceptPayment(Invoice $invoice)
    {
        if ($invoice->valid_until) {
            $responseCode = 403;
            $responseMessage = 'Pembayaran sudah disetujui';
            $responseData = null;
        } else if ($invoice->payment_date && $invoice->payment_attachment) {
            $package = Package::find($invoice->package_id);
            $invoice->payment_confirm_at    = now();
            $invoice->valid_until           = now()->addMonths($package->subscription_periode);
            $invoice->save();

            Invoice::where('user_id', $invoice->user_id)
            ->whereNull('valid_until')
            ->forceDelete();

            SendAcceptPayment::dispatch($invoice);

            $responseCode = 200;
            $responseMessage = 'Pembayaran berhasil disetujui';
            $responseData = $invoice;
        } else if ($invoice->payment_attachment == null) {
            $responseCode = 403;
            $responseMessage = 'Pengguna belum upload pembayaran';
            $responseData = null;
        } else if ($invoice->payment_date == null) {
            $responseCode = 403;
            $responseMessage = 'Pengguna belum melakukan pembayaran';
            $responseData = null;
        } else {
            $responseCode = 403;
            $responseMessage = 'Data tidak valid';
            $responseData = null;
        }

        return response()->json(HelperPublic::helpResponse($responseCode, $responseData, $responseMessage), $responseCode);
    }

    public function rejectPayment(Invoice $invoice)
    {
        $invoice->payment_confirm_at    = null;
        $invoice->payment_date          = null;
        $invoice->payment_attachment    = null;
        $invoice->valid_until           = null;
        $invoice->reason_for_rejection         = request()->reason_for_rejection;
        $invoice->solution              = request()->solution;
        $invoice->save();

        SendDeclinePayment::dispatch($invoice);
    }

    public function changeRole(Invoice $invoice)
    {
        $invoice->payment_confirm_at    = now();
        $invoice->valid_until           = now()->addYear();
        $invoice->save();

        $mailService = new MailService;

        $mailService->sendApprovedPayment($invoice);
    }

    public function uploadPayment($file, Invoice $invoice)
    {
        if (!empty($file) && $file->isValid()) {
            $changedName = time().random_int(100,999).$file->getClientOriginalName();
            $file->storeAs('payment/' . $invoice->id, $changedName);

            $invoice->payment_attachment = $changedName;
            $invoice->save();
            return true;
        } else {
            return false;
        }
    }

    public function generateInvoice(User $user)
    {
        $invoice = (new Invoice)->getUnpaid($user);
        $lastInvoice = Invoice::where('user_id', $user->id)->latest()->first();
        if ($invoice == null && $lastInvoice != null) {
            $invoiceNumber = $this->generateInvoiceNumber($user);
            $invoice = Invoice::create([
                'package_id'    => $lastInvoice->package_id,
                'user_id'       => $user->id,
                'price'         => $lastInvoice->price,
                'number'        => $invoiceNumber,
            ]);

            $mailService = new MailService;
            $this->generatePaymentToken($invoice);
            $mailService->sendInvoice($invoice);
        }
        return $invoice;
    }

    public function myStatus(User $user)
    {
        if ($user->type != 2) {
            $invoices = Invoice::where('user_id', $user->id)->latest()->get();

            if ($invoices->count() == 1 && $invoices[0]->valid_until == null) {
                $status = 'New Member';
                $status_id = 1;
                $package = Package::find($invoices[0]->package_id);
                $packageName = $package->name;
            } else if ($invoices->count() > 1 && $invoices[0]->valid_until == null) {
                $status = 'Renew';
                $status_id = 2;
                $package = Package::find($invoices[0]->package_id);
                $packageName = $package->name;
            } else {
                $status = 'Active Member';
                $status_id = 0;

                $package = Package::find($invoices[0]->package_id);
                $packageName = $package->name;
            }

            return [
                'status' => $status,
                'status_id' => $status_id,
                'package_id' => $invoices[0]->package_id,
                'package_name' => $packageName,
                'valid_until' => $invoices[0]->valid_until,
                'bank_name' => $invoices[0]->bank->name,
                'account_number' => $invoices[0]->bank->account_number,
                'transfer_to' => $invoices[0]->bank->owner_name,
            ];
        } else {
            return null;
        }
    }

    public function sendNearExpirated(User $user)
    {
        $invoice = Invoice::where('user_id', $user->id)
        ->latest()
        ->firstOrFail();
        if ($invoice->valid_until != null && date('Y-m-d', strtotime($invoice->valid_until.' -7 days')) <= now()) {
            $this->generateInvoice($user);
        }
    }

    public function checkExpirated(User $user)
    {
        $secondInvoice = Invoice::where('user_id', $user->id)->skip(1)->take(1)->latest()->first();
        $invoice = new Invoice;
        $lastInvoice = $invoice->getLastInvoice($user);
        if ($lastInvoice != null && $lastInvoice->valid_until == null && $secondInvoice != null && $secondInvoice->valid_until <= now()) {
            $paymentToken = PaymentToken::where('invoice_id', $lastInvoice->id)->firstOrFail();
            return [
                'status' => [
                    'code' => 402,
                    'message' => 'User harus melakukan pembayaran terlebih dahulu!',
                ],
                '_link' => [
                    'method_upload' => 'POST',
                    'url_upload' => url('api/register/upload-payment?token='.$paymentToken->token),
                    'method_data' => 'POST',
                    'url_data' => url('api/register/send-data-payment?token='.$paymentToken->token),
                ]
            ];
        }

        return false;
    }
}
