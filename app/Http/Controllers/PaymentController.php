<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\DetailPaymentResource;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\User;
use App\Services\PaymentService;
use App\Transformers\InvoiceTransformer;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->payment = new PaymentService;
    }

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
            if ($type == 0) {
                $datatable = DataTables::eloquent($model
                ->select(
                    'institution.*',
                    'invoice.*',
                    'invoice.id as invoice_id'
                )
                ->join('user', 'user.id', '=', 'user_id')
                ->join('institution', 'institution.id', '=', 'owner_id')
                ->where('user.type', $type))
                ->setTransformer(function($item){
                    if ($item->valid_until != null && $item->payment_date != null) {
                        $status = 'Accepted';
                    } else if ($item->payment_date != null && $item->valid_until == null) {
                        $status = 'Pending';
                    } else {
                        $status = 'Unpaid';
                    }

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'phone' => $item->phone,
                        'email' => $item->email,
                        'address' => $item->address,
                        'status' => $status,
                        'invoice_id' => $item->invoice_id,
                    ];
                })
                ->toJson();
            } else if ($type == 1) {
                $datatable = DataTables::eloquent($model
                ->select(
                    'member.*',
                    'invoice.*',
                    'institution.name as institution_name',
                    'department.name as department_name',
                    'invoice.id as invoice_id'
                )
                ->leftJoin('user', 'user.id', '=', 'user_id')
                ->leftJoin('member', 'member.id', '=', 'owner_id')
                ->leftJoin('department', 'department.id', '=', 'department_id')
                ->leftJoin('institution', 'institution.id', '=', 'institution_id')
                ->where('user.type', $type))
                ->setTransformer(function($item){
                    if ($item->valid_until != null && $item->payment_date != null) {
                        $status = 'Accepted';
                    } else if ($item->payment_date != null && $item->valid_until == null) {
                        $status = 'Pending';
                    } else {
                        $status = 'Unpaid';
                    }

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'email' => $item->email,
                        'institution_name' => $item->institution_name,
                        'department_name' => $item->department_name,
                        'status' => $status,
                        'invoice_id' => $item->invoice_id,
                    ];
                })
                ->toJson();
            } else {
                $datatable = null;
            }

        }

        return $datatable;
    }

    public function detailPayment(Invoice $invoice)
    {
        $this->responseCode = 200;
        $this->responseData = new DetailPaymentResource($invoice);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function storePayment(StorePaymentRequest $request, Invoice $invoice)
    {
        $request->validated();
        $user = User::find($invoice->user_id);
        $this->payment->savePayment($user, $request);

        $this->responseCode = 200;
        $this->responseMessage = 'Pembayaran berhasil disimpan';
        $this->responseData = $invoice->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function acceptPayment(Invoice $invoice)
    {
        $this->payment->acceptPayment($invoice);

        $this->responseCode = 200;
        $this->responseMessage = 'Pembayaran berhasil disetujui';
        $this->responseData = $invoice;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function rejectPayment(RejectRequest $request, Invoice $invoice)
    {
        $request->validated();
        $this->payment->rejectPayment($invoice);

        $this->responseCode = 200;
        $this->responseMessage = 'Pembayaran ditolak';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function showFile(Invoice $invoice)
    {
        $path = storage_path('app/payment/'.$invoice->id.'/'.$invoice->payment_attachment);
        return response()->file($path);
    }

    public function createInvoice()
    {

    }

    public function myPaymentStatus()
    {
        $user = auth()->user();

        $this->responseData = $this->payment->myStatus($user);

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
