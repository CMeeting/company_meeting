<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\UserAssets;
use App\Services\OrdersService;
use App\Services\UserAssetsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateUserAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update:user:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新用户订阅制资产';

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
        \Log::info('-----更新用户订阅制资产脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
        $assets_service = new UserAssetsService();

        $order_goods =  UserAssets::leftJoin('orders_goods', 'orders_goods.id', '=', 'user_assets.order_goods_id')
            ->leftJoin('goods', 'goods.id', '=', 'orders_goods.goods_id')
            ->where('user_assets.status', UserAssets::STATUS_1_ENABLE)
            ->where('user_assets.type', UserAssets::TYPE_1_SUB)
            ->get(['user_assets.user_id', 'orders_goods.*', 'goods.level1', 'goods.level2'])
            ->toArray();

        $order_service = new OrdersService();
        $classification = $order_service->assembly_saasorderclassification();

        $now = date('Y-m-d');
        foreach ($order_goods as $order_good){
            $created_at = $order_good['created_at'];
            //档位
            $gear = $classification[$order_good['level2']];
            $special_assets = $order_good['special_assets'];
            if($special_assets){
                $gear = $special_assets;
            }

            $user_id = $order_good['user_id'];

            $pay_years = $order_good['pay_years'];
            $end_date = $this->getEndDate($created_at, $pay_years);
            for($i = 1; $i <= $pay_years; $i++){
                $add_date = $this->getEndDate($created_at, $i);
                if($now == $add_date && $add_date <= $end_date){
                    \Log::info('更新用户订阅制资产', ['user_id'=>$user_id, 'order_no'=>$order_good['order_no']]);
                    $assets_service->updateSubBalance($order_good['id'], $user_id, $gear);
                }

                //超过有效期将用户关联资产设置为无效
                if($now > $end_date){
                    $assets_service->updateStatus($order_good['id'], $order_good['user_id'], UserAssets::STATUS_2_DISABLE);
                }
            }
        }
        \Log::info('-----更新用户订阅制资产脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }

    public function getEndDate($created_at, $pay_years){
        $start_month = Carbon::parse($created_at)->format('m');
        $end_date = Carbon::parse($created_at)->addMonth($pay_years);
        $end_month = $end_date->format('m');
        if(($end_month-$start_month) > $pay_years){
            $end_date = $end_date->subMonth()->lastOfMonth();
        }

        return $end_date->format('Y-m-d');
    }
}
