<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use App\Transformers\InvoiceTransformer;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->type == 0 || $user->type == 1) {
            $model = Invoice::query();
            $datatable = DataTables::eloquent($model->where('user_id', $user->id))
            ->setTransformer(new InvoiceTransformer)
            ->filterColumn('created_at', function($query, $keyword) {
                $sql = "TO_CHAR(created_at, 'dd-mm-yyyy') like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->toJson();
        } else {
            $type = request()->type;

            $model = Invoice::query();
            $datatable = DataTables::eloquent($model
            ->where('type', $type))
            ->setTransformer(new InvoiceTransformer)
            ->toJson();
        }

        return $datatable;
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
