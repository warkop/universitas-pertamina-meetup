<?php
namespace App\Services;

use App\Mail\VerifyChangeMail;
use App\Mail\ResetPassword;
use App\Mail\Approved;
use App\Mail\Disapproved;
use App\Mail\ApprovedPayment;
use App\Mail\DeclinePayment;
use App\Mail\Invoice as MailInvoice;
use App\Mail\VerifyMail;
use App\Models\EmailReset;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use App\Helpers\HelperPublic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

   public function sendApproved($dataUser, $email)
   {
      $dataUser = $dataUser->toArray();
      $url = env('URL_FRONTEND').'/login';

      $dataUser['url'] = $url;

      Mail::to($email)->send(new Approved($dataUser));
   }

   public function sendDecline($dataUser, $email, $reason)
   {
      $dataUser = $dataUser->toArray();
      $url = env('URL_FRONTEND').'/login';

      $dataUser['url'] = $url;
      $dataUser['reason'] = $reason;

      Mail::to($email)->send(new Disapproved($dataUser));
   }

    public function sendVerify($user)
    {
        $emailReset = EmailReset::withTrashed()->updateOrCreate(
        ['email' => $user->email, 'type' => 1, 'user_id' => $user->id],
        [
            'token' => Str::random(60),
            'deleted_at' => null,
            'deleted_by' => null,
        ]);

        if ($user->type == 0){
            $model = Institution::find($user->owner_id);
         } elseif ($user->type == 1) {
            $model = Member::find($user->owner_id);
         }

        $dataUser = $model->toArray();

        $url = env('URL_FRONTEND').'/verify-email?email_verify_token='.$emailReset->token;

        $dataUser['url'] = $url;

        Mail::to($user->email)->send(new VerifyMail($dataUser));
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

      $helper = new HelperPublic;

      $dataMail = [
         'name' => $model->name,
         'email' => $model->email,
         'orderDate' => date("F j, Y", strtotime($invoice->created_at)),
         'packageName' => $package->name,
         'price' => $helper->helpCurrency($invoice->price),
         'url' => env('URL_FRONTEND').'/renew-package',
      ];

      Mail::to($user->email)->send(new MailInvoice($dataMail));
   }

   public function sendDeclinePayment(Invoice $invoice): void
   {
      $user = User::find($invoice->user_id);

      if ($user->type == 0) {
         $model = Institution::find($user->owner_id);
      } else if ($user->type == 1) {
         $model = Member::find($user->owner_id);
      }

      $dataMail = [
         'email' => $model->email,
      ];

      Mail::to($user->email)->send(new DeclinePayment($dataMail));
   }

   public function sendApprovedPayment(Invoice $invoice): void
   {
      $user = User::find($invoice->user_id);

      if ($user->type == 0) {
         $model = Institution::find($user->owner_id);
      } else if ($user->type == 1) {
         $model = Member::find($user->owner_id);
      }

      $package = Package::find($invoice->package_id);

      $helper = new HelperPublic;

      $dataMail = [
         'name' => $model->name,
         'email' => $model->email,
         'number' => $invoice->number,
         'packageName' => $package->name,
         'price' => $helper->helpCurrency($invoice->price),
         'url' => env('URL_FRONTEND').'/renew-package',
      ];

      Mail::to($user->email)->send(new ApprovedPayment($dataMail));
   }
}
