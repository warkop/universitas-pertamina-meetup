<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendDataPaymentRequest;
use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Http\Requests\UploadPaymentRequest;
use App\Models\Bank;
use App\Models\Invoice;
use App\Models\User;
use App\Models\EmailReset;
use App\Models\Institution;
use App\Models\PaymentToken;
use App\Services\PaymentService;
use App\Services\RegisterService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private function isJson(...$args)
    {
        @json_decode(...$args);
        return (json_last_error()===JSON_ERROR_NONE);
    }

    public function signUpInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();

        // register institution
        $resultRegistration = (new RegisterService)->registerInstitution($request);
        $institution = $resultRegistration['model'];

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $institution->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        $uploadLink = null;
        $dataLink = null;
        if ($resultRegistration['token']) {
            $uploadLink = [
                'method' => 'POST',
                'link' => url('api/register/upload-payment?token='.$resultRegistration['token']),
                'body' => [
                    'payment_attachment'
                ],
            ];

            $dataLink = [
                'method_data' => 'POST',
                'url_data' => url('api/register/send-data-payment?token='.$resultRegistration['token']),
                'body' => [
                    'payment_date',
                    'bank_id',
                    'buyer',
                    'bank_account',
                    'transfer_amount',
                ]
            ];
        }
        $this->responseData['upload_link'] = $uploadLink;
        $this->responseData['send_data_link'] = $dataLink;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function signUpResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();

        // register researcher
        $resultRegistration = (new RegisterService)->registerResearcher($request);
        if (!$this->isJson($resultRegistration)) {
            return $resultRegistration;
        }
        $member = $resultRegistration['model'];

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $member->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        $uploadLink = null;
        $dataLink = null;
        if ($resultRegistration['token']) {
            $uploadLink = [
                'method' => 'POST',
                'link' => url('api/register/upload-payment?token='.$resultRegistration['token']),
                'body' => [
                    'payment_attachment'
                ],
            ];

            $dataLink = [
                'method_data' => 'POST',
                'url_data' => url('api/register/send-data-payment?token='.$resultRegistration['token']),
                'body' => [
                    'payment_date',
                    'bank_id',
                    'buyer',
                    'bank_account',
                    'transfer_amount',
                ]
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

    public function checkStatusPayment()
    {
        $paymentToken = PaymentToken::where('token', request()->token)->firstOrFail();
        if ($paymentToken) {
            $invoice = Invoice::findOrFail($paymentToken->invoice_id);

            if ($invoice->valid_until) {
                $this->responseCode     = 200;
                $this->responseMessage  = 'Bukti pembayaran berhasil diunggah';
            } else if (!$invoice->valid_until && ($invoice->payment_date || $invoice->payment_attachment)) {
                $this->responseCode     = 200;
                $this->responseMessage  = 'Menunggu pembayaran dikonfirmasi';
            }
        } else {
            $this->responseCode     = 403;
            $this->responseMessage  = 'Token tidak valid!';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function uploadPayment(UploadPaymentRequest $request)
    {
        $request->validated();
        $token = $request->get('token');
        $paymentToken = PaymentToken::where('token', $token)->first();
        if ($paymentToken) {
            $invoice = Invoice::find($paymentToken->invoice_id);
            $user = User::find($invoice->user_id);

            $result = (new PaymentService)->saveUploadPayment($user, $request);
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

            $result = (new PaymentService)->savePayment($user, $request);
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

    public function verifyMail(Request $request)
    {
        $token = $request->input('email_verify_token');

        $emailReset = EmailReset::where('token', $token)->where('type', 1)->first();

        $this->responseData = [
            'email' => $emailReset->email,
        ];

        if (!$emailReset){
            $this->responseCode = 404;
            $this->responseMessage = 'This token is invalid.';

            return response()->json($this->getResponse(), $this->responseCode);
        } else {
            $arrayUser = [
                'email_verified_at' => now(),
            ];

            User::where('id', $emailReset->user_id)->update($arrayUser);

            $emailReset->delete();

            $this->responseCode = 200;
            $this->responseMessage = 'Email Verify successful';

            return response()->json($this->getResponse(), $this->responseCode);
        }
    }

    public function checkAvaibility(Institution $institution)
    {
        $result = (new RegisterService)->checkAvaibility($institution);
        $this->responseCode = 200;
        $this->responseData = $result;

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
