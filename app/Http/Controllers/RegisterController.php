<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use App\Models\EmailReset;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Creating User
     *
     * @param \Illuminate\Http\Request $request Description
     * @param array $spesific Description
     * @return string
     **/
    private function createUser($request, $spesific)
    {
        $user = User::create([
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'type'      => $spesific['type'],
            'role_id'   => $spesific['role_id'],
            'owner_id'  => $spesific['id'],
        ]);

        return $user;
    }

    private function registerPackage($package_id, $user)
    {
        $package = Package::find($package_id);

        $payment = new PaymentService;
        $number = $payment->generateInvoiceNumber($user);

        $invoice = new Invoice();
        $invoice->create([
            'package_id'    => $package_id,
            'user_id'       => $user->id,
            'price'         => $package->price,
            'number'        => $number,
        ]);


        $payment->sendInvoice($user);
    }

    public function signUpInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();

        $package_id = $request->input('package_id');

        $institution = new Institution();

        $institution->name      = $request->name;
        $institution->address   = $request->address;
        $institution->email     = $request->email;
        $institution->save();

        $spesific = [
            'id'        => $institution->id,
            'role_id'   => 3,
            'type'      => 0,
        ];
        // create user
        $user = $this->createUser($request, $spesific);
        $this->registerPackage($package_id, $user);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $institution->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function signUpResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();

        $package_id = $request->input('package_id');

        $member = new Member();

        $member->name               = $request->name;
        $member->title_id           = $request->title_id;
        $member->department_id      = $request->department_id;
        $member->nationality_id     = $request->nationality_id;
        $member->employee_number    = $request->employee_number;
        $member->office_address     = $request->office_address;
        $member->office_phone_number= $request->office_phone_number;
        $member->email              = $request->email;
        $member->save();

        $spesific = [
            'id'        => $member->id,
            'role_id'   => 2,
            'type'      => 1,
        ];
        // create user
        $user = $this->createUser($request, $spesific);
        $this->registerPackage($package_id, $user);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $member->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function uploadPayment(Request $request, Invoice $invoice)
    {
        $file = $request->file('attachment');

        if (!empty($file) && $file->isValid()) {
            $changedName = time().random_int(100,999).$file->getClientOriginalName();
            $file->storeAs('payment/' . $invoice->id, $changedName);

            $invoice->payment_attachment = $changedName;
            $invoice->save();

            $this->responseCode     = 200;
            $this->responseMessage  = 'Bukti pembayaran berhasil diunggah';

            return response()->json($this->getResponse(), $this->responseCode);
        } else {
            $this->responseCode     = 400;
            $this->responseMessage  = 'Bukti pembayaran wajib diunggah!';

            return response()->json($this->getResponse(), $this->responseCode);
        }
    }

    public function verifyMail(request $request){
      $token = $request->input('email_verify_token');

      $emailReset = EmailReset::where('token', $token)->where('type', 1)->first();

      if (!$emailReset){
         $this->responseCode = 404;
         $this->responseMessage = 'This token is invalid.';

         return response()->json($this->getResponse(), $this->responseCode);
      }
      // elseif (Carbon::parse($emailReset->updated_at)->addMinutes(120)->isPast()) {
      //    $emailReset->delete();
      //    $this->responseCode = 400;
      //    $this->responseMessage = 'This token is expired.';
      //
      //    return response()->json($this->getResponse(), $this->responseCode);
      // }
      else {
         $arrayUser = [
            'email_verified_at' => date("Y-m-d H:i:s"),
         ];

         User::where('id', $emailReset->user_id)->update($arrayUser);

         $emailReset->delete();

         $this->responseCode = 200;
         $this->responseMessage = 'Email Verify';

         return response()->json($this->getResponse(), $this->responseCode);
      }
   }
}
