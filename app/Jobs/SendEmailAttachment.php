<?php

namespace App\Jobs;

use App\Services\EmailBlacklistService;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailAttachment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $subject;
    protected $description;
    protected $email;
    protected $files;

    public function __construct($description, $subject, $email, $files)
    {
        \Log::info('邮件发送队列:', ['email'=> $email, 'subject'=>$subject]);
        $this->subject = $subject;
        $this->description = $description;
        $this->email = $email;
        $this->files = $files;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('开始发送邮件:', ['email'=> $this->email, 'subject'=>$this->subject]);
        $emailService = new EmailService();

        //邮件黑名单筛选
        $emails = $this->filterBlackEmail($this->email);

        $emailService->sendEmail($this->description,$emails,$this->subject,$this->files);
    }

    /**
     * 过滤邮件黑名单
     * @param $emails
     * @return array
     */
    public function filterBlackEmail($emails){
        if(!is_array($emails)){
            $emails = explode(',', $emails);
        }

        $new_emails = [];
        $service = new EmailBlacklistService();
        foreach ($emails as $email){
            //符合过滤规则  chuge\..*@test\.com
            $num = preg_match("/chuge\..*@test\.com/", $email);
            if($num > 0){
                \Log::info('邮件发送黑名单过滤(符合过滤规则): ' . $email);
                continue;
            }

            if($service->exitsEmail($email)){
                \Log::info('邮件发送黑名单过滤(黑名单数据库): ' . $email);
                continue;
            }

            $new_emails[] = $email;
        }

        return $new_emails;
    }
}
