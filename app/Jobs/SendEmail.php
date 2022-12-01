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
    protected $arr;
    protected $subject;

    public function __construct($data,$arr,$subject,$type)
    {
        \Log::info('邮件发送队列:', ['email'=> $arr, 'subject'=>$subject]);
        $this->data = $data;
        $this->type = $type;
        $this->arr = $arr;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('开始发送邮件:', ['email'=> $this->arr, 'subject'=>$this->subject]);
        $emailService = new EmailService();
        $emailService->send_email($this->data,$this->arr,$this->subject,$this->type);
    }
}
