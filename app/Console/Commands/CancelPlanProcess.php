<?php

namespace App\Console\Commands;

use App\Models\BackGroundUserRemain;
use App\Models\OrderGoods;
use App\Models\OrderGoodsCancel;
use App\Models\User;
use App\Services\UserRemainService;
use Illuminate\Console\Command;

class CancelPlanProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cancel:plan:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '取消订阅用户资产处理';

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
     * @return mixed
     */
    public function handle()
    {
        $date = date('Y-m-d');
        $orders = OrderGoodsCancel::query()
            ->leftJoin('order_goods', 'order_goods_cancel.order_goods_id', '=', 'order_goods.id')
            ->leftJoin('users', 'order_goods.user_id', '=', 'users.id')
            ->where('order_goods_cancel.status', OrderGoodsCancel::STATUS_1_UNPROCESSED)
            ->where('order_goods_cancel.reset_date', $date)
            ->select(['users.id', 'users.email', 'order_goods.package_type'])
            ->get();

        foreach ($orders as $order){
            //取消订阅的用户修改资产为0
            $remain_service = new UserRemainService();
            $remain_service->resetRemain($order['id'], $order['email'], 0, $order['package_type'], BackGroundUserRemain::STATUS_2_INACTIVE, 'cancel');
        }

        return;
    }
}
