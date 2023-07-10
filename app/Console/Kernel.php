<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Psy\Command\Command;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\RemoveExportFile::class,
        Commands\RenameFlagsFilename::class,
        Commands\CloseOrder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //订单关闭脚本
        //$schedule->command('command:close:order')->daily();

        //未支付订单提醒
        //$schedule->command('command:unpaid:order:notice')->hourly();

        //试用订单到期提醒
        //$schedule->command('command:trial:order:notice')->hourly();

        //序列码过期状态改变
        $schedule->command('command:expire:license:code')->daily();

        //订单到期续订提醒
        //$schedule->command('command:order:renew:notice')->hourly();

        //订阅资产重置 (后台创建以及在线购买年订阅) 五分钟后执行，需要管理后台先同步使用记录
        $schedule->command('command:update:user:remain')->dailyAt('00:05');

        //订阅资产处理 (在线购买月订阅) 五分钟后执行，需要管理后台先同步使用记录
        $schedule->command('command:user:subscription:process')->dailyAt('00:05');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
