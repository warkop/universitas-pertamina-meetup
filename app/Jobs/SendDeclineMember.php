<?php

namespace App\Jobs;

use App\Models\Member;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDeclineMember implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $member;
    private $email;
    private $reason;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Member $member, string $email, string $reason)
    {
        $this->member = $member;
        $this->email = $email;
        $this->reason = $reason;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new MailService)->sendDecline($this->member, $this->email, $this->reason);
    }
}
