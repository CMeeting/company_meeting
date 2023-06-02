<?php


namespace App\Services;


use App\Models\BackGroundUser;
use App\Models\BackGroundUserBalance;
use App\Models\BackGroundUserRemain;

class UserRemainService
{
    /**
     * 更新用户SaaS资产信息
     * @param $user_id
     * @param $email
     * @param $total_files
     * @param $package_type
     * @param $status
     */
    public function resetRemain($user_id, $email, $total_files, $package_type, $status){
        //更新用户资产
        $backgroundUser = BackGroundUser::getByCompdfkitId($user_id);
        if(!$backgroundUser instanceof BackGroundUser){
            $this->activateUserRemain($user_id, $email);
        }

        //更新用户资产余额
        $remain = BackGroundUserRemain::getByTypeUserId($backgroundUser->id, $package_type);
        if(!$remain instanceof BackGroundUserRemain){
            BackGroundUserRemain::add($backgroundUser->tenant_id, $backgroundUser->id, $package_type, $total_files, $status);
        }else{
            BackGroundUserRemain::updateAssetType($remain, $total_files, $status);
        }
        // TODO 重置资产应该有两条记录（还是不需要处理）更新用户资产充值记录
        BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $total_files, BackGroundUserBalance::CHANGE_TYPE_1_RECHARGE);

        //推送资产到SaaS
        $mqService = new RabbitMQService();
        $mqService->sendMessage(['tenant_id'=>$backgroundUser->tenant_id, 'asset'=>$remain->total_files, 'assetType'=>$remain->asset_type, 'status'=>$remain->status]);
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