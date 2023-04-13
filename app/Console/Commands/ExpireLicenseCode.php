<?php

namespace App\Console\Commands;

use App\Models\LicenseModel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireLicenseCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:expire:license:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '序列码过期';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('-----序列码状态监听脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);

        //到期前45天发送内部提醒邮件并更新状态为即将到期
        $expire45 = Carbon::now()->addDays(45)->format('Y-m-d');
        $query = LicenseModel::whereRaw("date(expire_time) = '$expire45'")->where('status', 1);
        (clone $query)->update(['status'=>3]);
        $expire45_data = $query->get('id')->toArray();
        \Log::info('到期前45天序列码', ['ids'=>array_column($expire45_data, 'id')]);
        //TODO:发送提醒邮件

        //到期前15天发送内部提醒邮件
        $expire15 = Carbon::now()->addDays(15)->format('Y-m-d');
        $expire15_data = LicenseModel::whereRaw("date(expire_time) = '$expire15'")->get()->toArray();
        \Log::info('到期前15天序列码', ['ids'=>array_column($expire15_data, 'id')]);
        //TODO:发送提醒邮件

        //序列码过期
        $now = Carbon::now()->format('Y-m-d H:i:s');
        LicenseModel::where('expire_time', '<=', $now)->update(['status'=>3]);

        $ids = LicenseModel::where('expire_time', '<=', $now)->pluck('id');
        \Log::info('到期序列码', ['ids'=>$ids]);
        \Log::info('-----序列码过期状态改变脚本执行完成-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
