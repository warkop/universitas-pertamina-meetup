<?php

namespace App\Observers;

use App\Models\User;
use App\Services\MailService;

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
      $mailService = new MailService;
      $mailService->sendVerify($user);
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
