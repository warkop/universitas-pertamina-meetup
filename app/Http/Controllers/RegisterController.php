<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendDataPaymentRequest;
use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Http\Requests\uploadPaymentRequest;
use App\Models\Bank;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use App\Models\EmailReset;
use App\Models\PaymentToken;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
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

        // register package
        $paymentToken = $this->paymentService->registerPackage($package_id, $user);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $institution->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        $uploadLink = null;
        if ($paymentToken) {
            $uploadLink = [
                'method' => 'POST',
                'link' => url('api/upload-payment?token='.$paymentToken),
            ];
        }
        $this->responseData['upload_link'] = $uploadLink;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function signUpResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();

        $package_id = $request->input('package_id');

        $member = new Member();

        if ($request->department_id != null) {
            $member->department_id      = $request->department_id;
        } else {
            $member->is_independent = true;
        }

        $member->name               = $request->name;
        $member->title_id           = $request->title_id;
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

        // register package
        $paymentToken = $this->paymentService->registerPackage($package_id, $user);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $member->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);
        $uploadLink = null;
        if ($paymentToken) {
            $uploadLink = [
                'method' => 'POST',
                'link' => url('api/upload-payment?token='.$paymentToken),
            ];

            $dataLink = [
                'method_data' => 'POST',
                'url_data' => url('api/register/send-data-payment?token='.$paymentToken),
            ];
        }
        $this->responseData['upload_link'] = $uploadLink;
        $this->responseData['send_data_link'] = $dataLink;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function listBank()
    {
        $this->responseCode = 200;
        $this->responseData = Bank::get(['id', 'name']);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function uploadPayment(uploadPaymentRequest $request)
    {
        $request->validated();
        $token = $request->get('token');
        $paymentToken = PaymentToken::where('token', $token)->first();
        if ($paymentToken) {
            $invoice = Invoice::find($paymentToken->invoice_id);
            $user = User::find($invoice->user_id);

            $result = $this->paymentService->saveUploadPayment($user, $request);
            if ($result) {
                $this->responseCode     = 200;
                $this->responseMessage  = 'Bukti pembayaran berhasil diunggah';
            } else {
                $this->responseCode     = 400;
                $this->responseMessage  = 'Kirim bukti pembayaran gagal, silahkan hubungi administrator!';
            }
        } else {
            $this->responseCode     = 403;
            $this->responseMessage  = 'Token tidak valid!';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function sendDataPayment(SendDataPaymentRequest $request)
    {
        $request->validated();
        $token = $request->get('token');
        $paymentToken = PaymentToken::where('token', $token)->first();
        if ($paymentToken) {
            $invoice = Invoice::find($paymentToken->invoice_id);
            $user = User::find($invoice->user_id);

            $result = $this->paymentService->savePayment($user, $request);
            if ($result) {
                $this->responseCode     = 200;
                $this->responseMessage  = 'Bukti data pembayaran berhasil disimpan';
            } else {
                $this->responseCode     = 400;
                $this->responseMessage  = 'Kirim bukti pembayaran gagal, silahkan hubungi administrator!';
            }
        } else {
            $this->responseCode     = 403;
            $this->responseMessage  = 'Token tidak valid!';
        }

        return response()->json($this->getResponse(), $this->responseCode);
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
         $this->responseData = [
            'email' : $emailReset->email;
         ];

         return response()->json($this->getResponse(), $this->responseCode);
      }
   }
}
