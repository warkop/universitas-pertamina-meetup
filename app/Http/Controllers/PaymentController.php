<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\StoreUploadPaymentRequest;
use App\Http\Resources\DetailPaymentResource;
use App\Jobs\RenewInvoice;
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

    private function datatableAdmin($type)
    {
        $model = Invoice::query();
        if ($type == 0) {
            $datatable = DataTables::eloquent($model
                ->select([
                    'institution.*',
                    'invoice.valid_until',
                    'invoice.payment_date',
                    'invoice.id as invoice_id'
                ])
                ->join('user', 'user.id', '=', 'user_id')
                ->join('institution', 'institution.id', '=', 'owner_id')
                ->join('package', 'package.id', '=', 'package_id')
                ->where('user.type', $type)
                ->where('package.renewal', true)
            )
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
            ->filter(function ($query) {
                if (request()->has('search') && ! is_null(request()->get('search')['value']) ) {
                    $regex = request()->get('search')['value'];
                    $query->where('institution.name', 'ilike', '%' . $regex . '%');
                    $query->orWhere('institution.phone', 'ilike', '%' . $regex . '%');
                    $query->orWhere('institution.email', 'ilike', '%' . $regex . '%');
                    $query->orWhere('institution.address', 'ilike', '%' . $regex . '%');
                }
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
                ->join('user', 'user.id', '=', 'user_id')
                ->join('member', 'member.id', '=', 'owner_id')
                ->leftJoin('department', 'department.id', '=', 'department_id')
                ->leftJoin('institution', 'institution.id', '=', 'institution_id')
                ->join('package', 'package.id', '=', 'package_id')
                ->where('user.type', $type)
                ->where('package.renewal', true)
            )
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
            ->filter(function ($query) {
                if (request()->has('search') && ! is_null(request()->get('search')['value']) ) {
                    $regex = request()->get('search')['value'];
                    $query->where('member.name', 'ilike', '%' . $regex . '%');
                    $query->orWhere('member.email', 'ilike', '%' . $regex . '%');
                    $query->orWhere('institution_name', 'ilike', '%' . $regex . '%');
                    $query->orWhere('department_name', 'ilike', '%' . $regex . '%');
                }
            })
            ->toJson();
        } else {
            $datatable = null;
        }

        return $datatable;
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->type == 0 || $user->type == 1) {
            $model = Invoice::query();
            $datatable = DataTables::eloquent($model
                ->select('invoice.*')
                ->join('package', 'package.id', '=', 'package_id')
                ->where('user_id', $user->id)
                ->where('package.renewal', true)
            )
            ->setTransformer(new InvoiceTransformer)
            ->filterColumn('created_at', function($query, $keyword) {
                $sql = "TO_CHAR(created_at, 'dd-mm-yyyy') like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->toJson();
        } else {
            $type = request()->type;
            $datatable = $this->datatableAdmin($type);
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

    public function storeUploadPayment(StoreUploadPaymentRequest $request, Invoice $invoice)
    {
        $request->validated();
        $user = User::find($invoice->user_id);
        $this->payment->saveUploadPayment($user, $request);

        $this->responseCode = 200;
        $this->responseMessage = 'Pembayaran berhasil disimpan';
        $this->responseData = $invoice->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function acceptPayment(Invoice $invoice)
    {
        $this->authorize('basic', $invoice);
        return $this->payment->acceptPayment($invoice);
    }

    public function rejectPayment(RejectRequest $request, Invoice $invoice)
    {
        $this->authorize('basic', $invoice);
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
        RenewInvoice::dispatch()->delay(now()->addSecond(2));

        $this->responseMessage = 'Invoice telah dikirim';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function myPaymentStatus()
    {
        $user = auth()->user();

        $this->responseData = $this->payment->myStatus($user);

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
