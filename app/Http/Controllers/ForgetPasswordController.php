<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\Institution;
use App\Models\Member;
use App\Models\EmailReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;

use App\Services\MailService;

use Carbon\Carbon;

class ForgetPasswordController extends Controller
{
   /**
   * Creating User
   *
   * @param \Illuminate\Http\Request $request Description
   * @param array $spesific Description
   * @return string
   **/
   public function resetPassword(request $request)
   {
      $email = strtolower($request->input('email'));

      $chekUser = User::where('email', $email)->first();

      if(!empty($chekUser)){
         $emailReset = EmailReset::withTrashed()->updateOrCreate(
            ['email' => $email, 'type' => 2, 'user_id' => $chekUser->id],
            [
               'token' => Str::random(60),
               'deleted_at' => null,
               'deleted_by' => null,
         ]);

         if ($chekUser->type == 0){
            $model = Institution::find($chekUser->owner_id);
         } elseif ($chekUser->type == 1) {
            $model = Member::find($chekUser->owner_id);
         }

         $mail = new MailService;
         $mail->sendForgetPassword($model, $emailReset);

         $this->responseCode = 200;
         $this->responseMessage = 'Please Check Email for Reset Password';
      } else {
         $this->responseCode = 400;
         $this->responseMessage = 'Email Not Found';
      }
      // $this->responseData = $model;

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function checkResetPasswordToken(request $request,User $user){
      $token = $request->input('reset_password_token');

      $emailReset = EmailReset::where('token', $token)->where('type', 2)->first();

      if (!$emailReset){
         $this->responseCode = 404;
         $this->responseMessage = 'This token is invalid.';

         return response()->json($this->getResponse(), $this->responseCode);
      } elseif (Carbon::parse($emailReset->updated_at)->addMinutes(120)->isPast()) {
         $emailReset->delete();
         $this->responseCode = 400;
         $this->responseMessage = 'This token is expired.';

         return response()->json($this->getResponse(), $this->responseCode);
      } else {
         $this->responseCode = 200;
         $this->responseMessage = 'Token Valid';
         $this->responseData = $emailReset;

         return response()->json($this->getResponse(), $this->responseCode);
      }
   }

   public function changePassword(request $request, $token){

      $password = ['password' => bcrypt($request->password)];

      // $token = $request->input('token');

      $emailReset = EmailReset::where('token', $token)->where('type', 2)->first();

      if (!$emailReset){
         $this->responseCode = 404;
         $this->responseMessage = 'This token is invalid.';
      } else {
         User::where('id', $emailReset->user_id)->update($password);

         $emailReset->delete();

         $this->responseCode = 200;
         $this->responseMessage = 'Success Change Password';
      }


      return response()->json($this->getResponse(), $this->responseCode);
   }
}
