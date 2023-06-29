<?php


namespace App\Services;


use App\Models\BackGroundUser;
use App\Models\BackGroundUserBalance;
use App\Models\BackGroundUserRemain;
use App\Models\OrderGoods;

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
     */
    public function resetRemain($user_id, $email, $total_files, $package_type, $status, $type, $start_date = null, $end_date = null){
        //更新用户资产
        $backgroundUser = BackGroundUser::getByCompdfkitId($user_id);
        if(!$backgroundUser instanceof BackGroundUser){
            $this->activateUserRemain($user_id, $email);
        }

        //更新用户资产余额
        //有用户资产数据，推送到SaaS后，由SaaS同步到管理后台
        $remain = BackGroundUserRemain::getByTypeUserId($backgroundUser->id, $package_type);

        if(!$remain instanceof BackGroundUserRemain){
            $remain = BackGroundUserRemain::add($backgroundUser->tenant_id, $backgroundUser->id, $package_type, $total_files, $status, $start_date, $end_date);
        }else{
            $balance_change = $remain->total_files - $remain->used_files;
            $remain = BackGroundUserRemain::updateAssetType($remain, $total_files, $type, $start_date, $end_date);
        }

        //新增资产只增加新增消费记录，重置资产增加消费，充值两条记录，取消订阅增加消费记录
        if($type == BackGroundUserRemain::OPERATE_TYPE_1_ADD){
            BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $total_files, BackGroundUserBalance::CHANGE_TYPE_1_RECHARGE);
        }elseif($type == BackGroundUserRemain::OPERATE_TYPE_2_RESET){
            if(isset($balance_change) && !$balance_change){
                BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $balance_change, BackGroundUserBalance::CHANGE_TYPE_2_USED);
            }
            BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $total_files, BackGroundUserBalance::CHANGE_TYPE_1_RECHARGE);
        }elseif($type == BackGroundUserRemain::OPERATE_TYPE_3_CANCEL && isset($balance_change) && !$balance_change){
            BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $balance_change, BackGroundUserBalance::CHANGE_TYPE_2_USED);
        }

        //推送资产到SaaS
        $mqService = new RabbitMQService();
        $mqService->sendMessage(['tenant_id'=>$backgroundUser->tenant_id, 'asset'=>$total_files, 'assetType'=>$remain->asset_type, 'status'=>$remain->status]);
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

        return HttpClientService::get($url, [], $headers);
    }
}