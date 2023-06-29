<?php

namespace App\Console\Commands;

use App\Models\BackGroundUserRemain;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\OrdersService;
use App\Services\UserRemainService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mpdf\Tag\P;

class UpdateUserRemain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update:user:remain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '后台创建订阅以及购买年订阅更新用户资产';

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
        //重置资产规则，购买日下个月第二天凌晨更新，比如2023-5-29购买，2023-6-30凌晨更新
        //后台创建月订阅年订阅 以及在线购买年订阅 有效期内更新资产信息

        $remain_service = new UserRemainService();
        $order_service = new OrdersService();
        $classification = $order_service->assembly_saasorderclassification();

        $order_goods_arr = OrderGoods::query()
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->leftJoin('users', 'users.id', '=', 'orders_goods.user_id')
            ->where('orders_goods.status', OrderGoods::STATUS_1_PAID)
            ->where('orders_goods.package_type', OrderGoods::PACKAGE_TYPE_1_PLAN)
            ->select(['orders_goods.id', 'orders_goods.created_at', 'orders_goods.pay_years', 'orders_goods.order_id', 'orders_goods.special_assets', 'orders_goods.user_id', 'users.email', 'goods.level1', 'goods.level2'])
            ->get()
            ->toArray();

        $now = Carbon::now();
        foreach ($order_goods_arr as $order_goods){
            $combo = $classification[$order_goods['level1']] ?? '';
            $gear = $classification[$order_goods['level2']] ?? '';
            //在线购买只有年订阅定时任务重置资产，月订阅扣款成功后才重置资产
            if($order_goods['type'] == OrderGoods::TYPE_2_BUY && $combo != 'Annually'){
                continue;
            }

            $pay_at = Carbon::parse($order_goods['pay_time']);
            $validity_period = $order_goods['pay_years']; //有效期
            $max_date = $pay_at->addMonthsNoOverflow($validity_period)->addDay();

            //到达有效期，修改订单状态为取消订阅
            if($now->format('Y-m-d') == $max_date->format('Y-m-d')){
                OrderGoods::query()
                    ->where('id', $order_goods['id'])
                    ->update(['status' => OrderGoods::STATUS_5_UNSUBSCRIBE]);

                Order::query()
                    ->where('id', $order_goods['order_id'])
                    ->update(['status' => OrderGoods::STATUS_5_UNSUBSCRIBE]);

                continue;
            }

            // 变更资产数
            $total_files = $order_goods['special_assets'] ? $order_goods['special_assets'] : $gear;
            $user_id = $order_goods['user_id'];
            $email = $order_goods['email'];

            for($i = 1; $i < $validity_period; $i++){
                $next_date = $pay_at->addMonthsNoOverflow($i)->addDay();
                //重置资产
                if($now->format('Y-m-d') == $next_date->format('Y-m-d')){
                    $remain_service->resetRemain($user_id, $email, $total_files, OrderGoods::PACKAGE_TYPE_1_PLAN, BackGroundUserRemain::STATUS_1_ACTIVE, BackGroundUserRemain::OPERATE_TYPE_2_RESET);
                    continue;
                }
            }
        }

        return;
    }
}
