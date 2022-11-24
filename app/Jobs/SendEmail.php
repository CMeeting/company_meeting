<?php

namespace App\Jobs;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $data;
    protected $type;
    protected $email;

    public function __construct($data, $type, $email)
    {
        \Log::info('邮件发送队列:' . $email);
        $this->data = $data;
        $this->type = $type;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('开始发送邮件:' . $this->email);
        $emailService = new EmailService();
        $emailService->sendDiyContactEmail($this->data, $this->type, $this->email);
    }
}
