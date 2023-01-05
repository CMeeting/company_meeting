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
        \Log::info('-----序列码过期状态改变脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
        $now = Carbon::now()->format('Y-m-d H:i:s');
        LicenseModel::where('expire_time', '<=', $now)->update(['status'=>3]);

        $ids = LicenseModel::where('expire_time', '<=', $now)->pluck('id');
        \Log::info('到期序列码', ['ids'=>$ids]);
        \Log::info('-----序列码过期状态改变脚本执行完成-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
