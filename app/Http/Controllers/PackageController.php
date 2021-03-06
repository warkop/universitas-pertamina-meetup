<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailPaymentResource;
use App\Http\Resources\ListForUserResource;
use App\Http\Resources\MyPackageResource;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $package = Package::where('package_type', request()->type)->oldest('order')->get();

        $this->responseCode = 200;
        $this->responseData = $package;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function upgrade(Request $request)
    {
        $selectedPackage = $request->package_id;
        $user = auth()->user();
        $invoice = (new Invoice())->getLastPaidedInvoice($user);
        $package1 = Package::findOrFail($selectedPackage);
        $package2 = Package::findOrFail($invoice->package_id);
        if ($package1->order > $package2->order && $package2->package_type == $package1->package_type) {
            $paymentToken = (new PaymentService)->registerPackage($selectedPackage, $user);

            $uploadLink = null;
            $dataLink = null;
            if ($paymentToken) {
                $uploadLink = [
                    'method' => 'POST',
                    'link' => url('api/register/upload-payment?token='.$paymentToken),
                    'body' => [
                        'payment_attachment'
                    ],
                ];

                $dataLink = [
                    'method_data' => 'POST',
                    'url_data' => url('api/register/send-data-payment?token='.$paymentToken),
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

            $this->responseCode = 200;
            $this->responseMessage = 'Paket berhasil ditingkatkan, invoice akan dikirim akun email Anda!';
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Paket tidak boleh lebih rendah atau sama dengan paket yang berlaku saat ini!';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function myPackage()
    {
        $user = auth()->user();
        $invoice = (new Invoice())->getLastInvoice($user);
        if ($user->type != 2) {
            if ($invoice) {
                $this->responseCode = 200;
                $this->responseData = new DetailPaymentResource($invoice);
            } else {
                $this->responseCode = 400;
                $this->responseMessage = 'Paket belum aktif atau Anda tidak memiliki paket!';
            }
        } else {
            $this->responseCode = 400;
            $this->responseMessage = 'Admin tidak punya paket!';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function listForUser()
    {
        $user = auth()->user();

        $type = 2;
        if ($user->type == 0) {
            $type = 0;
        } else if ($user->type == 1) {
            $type = 1;
            $member = Member::find($user->owner_id);
            if ($member->is_extenstion) {
                $type = 2;
            }
        }
        $package = Package::where('package_type', $type)->oldest('order')->get();

        $this->responseCode = 200;
        $this->responseData = ListForUserResource::collection($package);

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
