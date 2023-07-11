<?php

namespace App\Console\Commands;

use App\Models\BackGroundUserRemain;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\OrderGoods;
use App\Models\UserSubscriptionProcess;
use App\Models\User;
use App\Services\UserRemainService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UserSubscriptionHandle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:user:subscription:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订阅用户资产处理';

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
        \Log::info('-------在线购买月订阅资产重置定时任务开始执行-------');

        //获取未处理的订阅操作
        $date = date('Y-m-d');
        $orders_goods = UserSubscriptionProcess::query()
            ->leftJoin('orders_goods', 'user_subscription_process.order_goods_id', '=', 'orders_goods.id')
            ->leftJoin('users', 'user_subscription_process.user_id', '=', 'users.id')
            ->where('user_subscription_process.status', UserSubscriptionProcess::STATUS_1_UNPROCESSED)
            ->where('user_subscription_process.reset_date', $date)
            ->select(['user_subscription_process.id', 'users.id as user_id', 'users.email', 'orders_goods.package_type', 'orders_goods.goods_id', 'user_subscription_process.type', 'orders_goods.pay_time', 'order_goods.id as order_goods_id'])
            ->get();

        $remain_service = new UserRemainService();
        foreach ($orders_goods as $order){
            if($order['type'] == UserSubscriptionProcess::TYPE_1_DEDUCTED_SUCCESS){
                //订阅扣款成功重置资产
                $start_date = Carbon::now()->toDateTimeString();
                $goods = Goods::query()->find($order['order_goods_id']);
                $combo = Goodsclassification::getComboById($goods->level1);
                if($combo == Goods::COMBO_MONTHLY) {
                    $cycle = OrderGoods::CYCLE_1_MONTH;
                }else{
                    $cycle = OrderGoods::CYCLE_2_YEAR;
                }
                $end_date = $remain_service->getSubEndDate($order['order_goods_id'], $order['pay_time'], $cycle);
                $status = BackGroundUserRemain::STATUS_1_ACTIVE;
                $type = BackGroundUserRemain::OPERATE_TYPE_2_RESET;
            }else{
                //取消订阅或者订阅扣款失败，取消订阅
                $status = BackGroundUserRemain::STATUS_1_ACTIVE;
                $type = BackGroundUserRemain::OPERATE_TYPE_3_CANCEL;
            }

            $total_files = Goods::getTotalFilesByGoods($order['goods_id']);

            \Log::info('在线购买月订阅资产重置', ['user_id'=>$order['user_id'], 'order_goods_id'=>$order['goods_id'], 'total_files'=>$total_files]);

            $remain_service->resetRemain($order['user_id'], $order['email'], $total_files, $order['package_type'], $status, $type, $start_date ?? null, $end_date ?? null);

            //将记录修改为已处理
            $order->status = UserSubscriptionProcess::STATUS_2_PROCESSED;
            $order->save();
        }

        \Log::info('-------在线购买月订阅资产重置定时任务执行完成-------');

        return;
    }
}
