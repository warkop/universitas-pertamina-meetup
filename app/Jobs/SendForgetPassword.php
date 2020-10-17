<?php

namespace App\Jobs;

use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendForgetPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;
    private $emailReset;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $emailReset)
    {
        $this->model = $model;
        $this->emailReset = $emailReset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new MailService)->sendForgetPassword($this->model, $this->emailReset);
    }
}
