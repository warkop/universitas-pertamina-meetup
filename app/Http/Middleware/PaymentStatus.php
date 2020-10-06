<?php

namespace App\Http\Middleware;

use App\Helpers\HelperPublic;
use App\Models\Invoice;
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
                $data = [
                    'status' => [
                        'code' => 402,
                        'message' => 'User harus melakukan pembayaran terlebih dahulu!',
                    ]
                ];
                return response()->json($data, 200);
            }
        }

        return $next($request);
    }
}
