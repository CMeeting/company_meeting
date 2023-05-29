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
     * @param $total_files
     * @param $package_type
     */
    public function resetRemain($user_id, $total_files, $package_type){
        //更新用户资产
        $backgroundUser = BackGroundUser::getByCompdfkitId($user_id);
        if(!$backgroundUser instanceof BackGroundUser){
            //TODO 调用SaaS激活
            sleep(1);
        }

        //更新用户资产余额
        $remain = BackGroundUserRemain::getByTypeUserId($backgroundUser->id, $package_type);
        if(!$remain instanceof BackGroundUserRemain){
            BackGroundUserRemain::add($backgroundUser->tenant_id, $backgroundUser->id, $package_type, $total_files);
        }else{
            BackGroundUserRemain::updateAssetType($remain, $total_files);
        }
        //更新用户资产充值记录
        BackGroundUserBalance::add($backgroundUser->id, $backgroundUser->tenant_id, $package_type, $total_files);

        //TODO 推送资产变更到SaaS
    }
}