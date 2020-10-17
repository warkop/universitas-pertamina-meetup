<?php

namespace App\Jobs;

use App\Models\Member;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAcceptMember implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $member;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Member $member, string $email)
    {
        $this->member = $member;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new MailService)->sendApproved($this->member, $this->email);
    }
}
