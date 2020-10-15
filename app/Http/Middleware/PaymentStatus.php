<?php

namespace App\Http\Middleware;

use App\Helpers\HelperPublic;
use App\Models\Invoice;
use App\Models\PaymentToken;
use App\Services\PaymentService;
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
        if ($user != null && $user->type != 2) {
            $paymentService = new PaymentService;
            $result = $paymentService->checkExpirated($user);

            if ($result) {
                return response()->json($result, 200);
            }
        }

        return $next($request);
    }
}
