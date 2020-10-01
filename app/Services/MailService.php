<?php
namespace App\Services;

use App\Mail\VerifyChangeMail;
use App\Mail\ResetPassword;
use App\Mail\Invoice as MailInvoice;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class MailService
{
   public function sendChangeEmail($dataUser, $emailReset, $type)
   {
      $dataUser = $dataUser->toArray();
      $url = env('URL_FRONTEND').'/check-change-email/approve?type='.$type.'&change_email_token='.$emailReset->token;

      $dataUser['url'] = $url;

      Mail::to($emailReset->email)->send(new VerifyChangeMail($dataUser));
   }

   public function sendForgetPassword($dataUser, $emailReset)
   {
      $dataUser = $dataUser->toArray();
      $url = env('URL_FRONTEND').'/check-reset-password?reset_password_token='.$emailReset->token;

      $dataUser['url'] = $url;

      Mail::to($emailReset->email)->send(new ResetPassword($dataUser));
   }

    public function sendInvoice(Invoice $invoice): void
    {
        $user = User::find($invoice->user_id);
        if ($user->type == 0) {
            $model = Institution::find($user->owner_id);
        } else if ($user->type == 1) {
            $model = Member::find($user->owner_id);
        }

        $package = Package::find($invoice->package_id);

        $dataMail = [
            'name' => $model->name,
            'email' => $model->email,
            'orderDate' => date("F j, Y", strtotime($invoice->created_at)),
            'expirationDate' => date("F j, Y", strtotime($invoice->valid_until)),
            'packageName' => $package->name,
            'price' => $invoice->price,
            'url' => env('URL_FRONTEND').'/renew-package',
        ];

        Mail::to($user->email)->send(new MailInvoice($dataMail));
   }
}
