<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderGoods;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:close:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '关闭订单任务';

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
        $now = Carbon::now()->format('Y-m-d H:i:s');
        \Log::info('----关闭订单开始执行----', ['start_time'=>$now]);
        $end_at = date('Y-m-d 23:59:59');
        $seven_days_ago = Carbon::parse($end_at)->subDays(7)->format('Y-m-d H:i:s');
        $query = Order::where('created_at', '<=', $seven_days_ago)->where('status', 0);
        //获取需要关闭的订单id
        $order_id_arr = (clone $query)->pluck('id')->toArray();
        //将过期订单关闭
        $query->update(['closetime'=>$now, 'status'=>4]);
        //将子订单关闭
        OrderGoods::whereIn('order_id', $order_id_arr)->update(['closetime'=>$now, 'status'=>4]);
        \Log::info('关闭订单', ['order_id'=>$order_id_arr]);
        \Log::info('----关闭订单开始执行----', ['end_time'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
