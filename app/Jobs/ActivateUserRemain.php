<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\UserRemainService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ActivateUserRemain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        \Log::info('开始激活用户SaaS资产', ['email'=>$user->email]);
        $service = new UserRemainService();
        $res = $service->activateUserRemain($user->id, $user->email);
        \Log::info('激活用户SaaS资产结果', ['email'=>$user->email, 'result'=>$res]);
    }
}
