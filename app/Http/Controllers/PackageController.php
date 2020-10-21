<?php

namespace App\Http\Controllers;

use App\Http\Resources\listForUserResource;
use App\Jobs\SendInvoice;
use App\Models\Categories;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $package = Package::where('package_type', request()->type)->get();

        $this->responseCode = 200;
        $this->responseData = $package;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function upgrade(Request $request)
    {
        $selectedPackage = $request->package_id;
        $user = auth()->user();
        $invoice = (new Invoice())->getLastPaidedInvoice($user);
        $package1 = Package::find($selectedPackage);
        $package2 = Package::find($invoice->package_id);
        if ($package1->order > $package2->order && $package2->package_type == $package1->package_type) {
            $paymentToken = (new PaymentService)->registerPackage($selectedPackage, $user);

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
        $invoice = (new Invoice())->getLastPaidedInvoice($user);
        $this->responseCode = 200;
        $this->responseData = $invoice;

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
        $package = Package::where('package_type', $type)->get();

        $this->responseCode = 200;
        $this->responseData = listForUserResource::collection($package);

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
