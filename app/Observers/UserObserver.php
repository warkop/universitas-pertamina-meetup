<?php

namespace App\Observers;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Models\EmailReset;
use App\Models\Institution;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
   /**
   * Handle the user "created" event.
   *
   * @param  \App\User  $user
   * @return void
   */
   public function created(User $user)
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
      $url = env('URL_FRONTEND').'/verify-email?verifiy_email_token='.$emailReset->token;

      $dataUser['url'] = $url;

      Mail::to($user->email)->send(new VerifyMail($dataUser));
   }

   /**
   * Handle the user "updated" event.
   *
   * @param  \App\User  $user
   * @return void
   */
   public function updated(User $user)
   {
      //
   }

   /**
   * Handle the user "deleted" event.
   *
   * @param  \App\User  $user
   * @return void
   */
   public function deleted(User $user)
   {
      //
   }

   /**
   * Handle the user "restored" event.
   *
   * @param  \App\User  $user
   * @return void
   */
   public function restored(User $user)
   {
      //
   }

   /**
   * Handle the user "force deleted" event.
   *
   * @param  \App\User  $user
   * @return void
   */
   public function forceDeleted(User $user)
   {
      //
   }
}
