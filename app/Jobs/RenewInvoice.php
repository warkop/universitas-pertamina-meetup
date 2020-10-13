<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoices = Invoice::whereNotNull('valid_until')->get();
        $paymentService = new PaymentService;
        foreach ($invoices as $invoice) {
            $user = User::find($invoice->user_id);
            $paymentService->sendNearExpirated($user);
        }
    }
}
