<?php


namespace App\Services;


use App\Models\BackGroundUser;
use App\Models\BackGroundUserBalance;
use App\Models\BackGroundUserRemain;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\OrderCashFlow;
use App\Models\OrderGoods;
use Carbon\Carbon;

class UserRemainService
{
    /**
     * 更新用户SaaS资产信息
     * @param $user_id
     * @param $email
     * @param $total_files
     * @param $package_type
     * @param $status
     * @param $type
     * @param $start_date
     * @param $end_date
     * @param $cycle
     */
    public function resetRemain($user_id, $email, $total_files, $package_type, $status, $type, $start_date = null, $end_date = null, $cycle = null){
        //更新用户资产
        $backgroundUser = BackGroundUser::getByCompdfkitId($user_id);
        if(!$backgroundUser instanceof BackGroundUser){
            $this->activateUserRemain($user_id, $email);
        }

        //更新用户资产余额
        //有用户资产数据，推送到SaaS后，由SaaS同步到管理后台
        $remain = BackGroundUserRemain::getByTypeUserId($backgroundUser->id, $package_type);

        if(!$remain instanceof BackGroundUserRemain){
            $remain = BackGroundUserRemain::add($backgroundUser->tenant_id, $backgroundUser->id, $package_type, $total_files, $status, $start_date, $end_date, $cycle);
        }else{
            $balance_change = $remain->total_files - $remain->used_files;
            $remain = BackGroundUserRemain::updateAssetType($remain, $total_files, $type, $start_date, $end_date, $cycle);
        }

        //新增资产只增加新增消费记录，重置资产增加消费，充值两条记录，取消订阅增加消费记录
        if($type == BackGroundUserRemain::OPERATE_TYPE_1_ADD){
            BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $total_files, BackGroundUserBalance::CHANGE_TYPE_1_RECHARGE, $cycle);
        }elseif($type == BackGroundUserRemain::OPERATE_TYPE_2_RESET){
            if(isset($balance_change) && $balance_change > 0){
                BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $balance_change, BackGroundUserBalance::CHANGE_TYPE_2_USED, $cycle);
            }
            BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $total_files, BackGroundUserBalance::CHANGE_TYPE_1_RECHARGE, $cycle);
        }elseif($type == BackGroundUserRemain::OPERATE_TYPE_3_CANCEL){
            if(isset($balance_change)){
                //取消订阅，用户还存在订阅资产，证明用户取消订阅后，又购买了，扣除的资产不能超过本订单的资产数
                $balance_change_new = $balance_change - $remain->total_files;
                if($balance_change_new > 0){
                    BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $balance_change_new, BackGroundUserBalance::CHANGE_TYPE_2_USED, $cycle);
                }
            }
        }

        //推送资产到SaaS
        $mqService = new RabbitMQService();
        if($remain->asset_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
            //订阅推送实时的资产
            $asset = $remain->total_files;
        }else{
            //package推送新增资产
            $asset = $total_files;
        }
        $message = ['tenant_id'=>$backgroundUser->tenant_id, 'asset'=>$asset, 'assetType'=>$remain->asset_type, 'status'=>$remain->status];
        \Log::info('同步资产到SaaS', ['data'=>$message]);
        $mqService->sendMessage($message);
    }

    /**
     * 调用SaaS用户管理后台资产激活接口
     * @param $user_id
     * @param $email
     * @return array
     */
    public function activateUserRemain($user_id, $email){
        $jti = JWTService::getJTI();

        //缓存token
        JWTService::saveToken($email, $jti);

        $payload = ['email' => $email, 'iat' => time(), 'jti'=>$jti, 'id'=>$user_id];
        $token = JWTService::getToken($payload);

        $headers = [
            'Content-type' =>  'application/json',
            'Accept' => 'application/json',
            'Authorization' => $token
        ];
        $url = env('BACKGROUND_USER_SAAS') . '/user-api/v1/user/verify';
        \Log::info('激活接口URL#' . $url);

        return HttpClientService::get($url, [], $headers);
    }

    /**
     * 根据周期计算套餐结束时间。规避31号购买这种特殊情况。比如7.31-8.31，9.1-9.30, 10.1-10.31
     * @param $order_goods_id
     * @param $start_date
     * @param $cycle
     * @return string
     */
    public function  getSubEndDate($order_goods_id, $start_date, $cycle){
        $period = OrderCashFlow::getPeriodByOrderId($order_goods_id);

        if($cycle == OrderGoods::CYCLE_1_MONTH){
            return Carbon::parse($start_date)->addMonthsNoOverflow($period)->toDateString();
        }else{
            return Carbon::parse($start_date)->addYearsNoOverflow($period)->toDateString();
        }
    }

    /**
     * 获取取消订阅，资产重置时间，根据扣款成功流水计算最后一次套餐有效期，不能直接拿资产表结束时间，可能存在取消订阅后，又购买订阅的情况
     * @param OrderGoods $order_goods
     * @return string
     */
    public function getResetDate(OrderGoods $order_goods){
        $goods = Goods::query()->find($order_goods->goods_id);
        $combo = Goodsclassification::getComboById($goods->level1);
        if($combo == Goods::COMBO_MONTHLY){
            $cycle = OrderGoods::CYCLE_1_MONTH;
        }else{
            $cycle = OrderGoods::CYCLE_2_YEAR;
        }

        $last_end_date = $this->getSubEndDate($order_goods->id, $order_goods->pay_time, $cycle);
        return  Carbon::parse($last_end_date)->addDay()->toDateString();
    }
}