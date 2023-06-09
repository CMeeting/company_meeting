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

        //SaaS订阅资产重置
        $schedule->command('command:update:user:remain')->daily();

        //取消订阅资产处理
        $schedule->command('command:cancel:plan:process')->daily();
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
