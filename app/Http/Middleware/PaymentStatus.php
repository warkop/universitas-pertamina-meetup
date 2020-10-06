<?php

namespace App\Http\Middleware;

use App\Helpers\HelperPublic;
use App\Models\Invoice;
use App\Models\PaymentToken;
use Closure;
use Illuminate\Http\Request;

class PaymentStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user->type != 2) {
            $invoice = new Invoice;
            $lastInvoice = $invoice->getLastInvoice($user);
            if ($lastInvoice != null && $lastInvoice->valid_until == null) {
                $paymentToken = PaymentToken::where('invoice_id', $lastInvoice->id)->firstOrFail();
                $data = [
                    'status' => [
                        'code' => 402,
                        'message' => 'User harus melakukan pembayaran terlebih dahulu!',
                    ],
                    '_link' => [
                        'method_upload' => 'POST',
                        'url_upload' => url('api/register/upload-payment?token='.$paymentToken->token),
                        'method_data' => 'POST',
                        'url_data' => url('api/register/send-data-payment?token='.$paymentToken->token),
                    ]
                ];
                return response()->json($data, 200);
            }
        }

        return $next($request);
    }
}
