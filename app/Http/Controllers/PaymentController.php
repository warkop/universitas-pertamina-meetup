<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->type == 0) {

        } else if ($user->type == 1) {

        }

        $model = Invoice::query();

        return DataTables::eloquent($model)->toJson();
    }

    public function detailPayment(Invoice $invoice)
    {
        $this->responseCode = 200;
        $this->responseData = $invoice;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function storePayment(StorePaymentRequest $request, Invoice $invoice)
    {
        $request->validated();
        $payment = new PaymentService;
        $user = User::find($invoice->user_id);
        $payment->savePayment($user, $request);

        $this->responseCode = 200;
        $this->responseMessage = 'Pembayaran berhasil disimpan';
        $this->responseData = $invoice->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function acceptPayment(Invoice $invoice)
    {
        $payment = new PaymentService;
        $payment->acceptPayment($invoice);

        $this->responseCode = 200;
        $this->responseMessage = 'Pembayaran berhasil disetujui';
        $this->responseData = $invoice;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function showFile(Invoice $invoice)
    {
        $path = storage_path('app/payment/'.$invoice->id.'/'.$invoice->payment_attachment);
        return response()->file($path);
    }
}
