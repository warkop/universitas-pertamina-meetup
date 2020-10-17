<?php

namespace App\Jobs;

use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChangeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;
    private $emailReset;
    private $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $emailReset, $type)
    {
        $this->model = $model;
        $this->emailReset = $emailReset;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new MailService)->sendChangeEmail($this->model, $this->emailReset, $this->type);
    }
}
