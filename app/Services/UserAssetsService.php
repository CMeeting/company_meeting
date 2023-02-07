<?php


namespace App\Services;


use App\Models\User;
use App\Models\UserAssets;

class UserAssetsService
{
    /**
     * 新增用户资产
     * @param $order_goods_id
     * @param $user_id
     * @param $combo
     * @param $gear
     * @param null $manual
     */
    public function addUserAssetsFromOrder($order_goods_id, $user_id, $combo, $gear, $manual = null){
        $type = $this->getTypeByCombo($combo);
        $assets = UserAssets::where('user_id')->where('type', $type)->where('order_goods_id', $order_goods_id)->first();
        if(!$assets instanceof UserAssets){
            $assets = new UserAssets();
            $assets->order_goods_id = $order_goods_id;
            $assets->user_id = $user_id;
            $assets->type = $type;
            $assets->total = 0;
            $assets->balance = 0;
            $assets->status = UserAssets::STATUS_1_ENABLE;
        }

        if($manual){
            $assets->total += $manual;
            $assets->balance += $manual;
        }else{
            $assets->total += $gear;
            $assets->balance += $gear;
        }

        $assets->save();
    }

    /**
     * 根据订单套餐获取资产类型
     * @param $combo
     * @return int
     */
    public function getTypeByCombo($combo){
        if($combo == '月订阅制' || $combo == '年订阅制'){
            return UserAssets::TYPE_1_SUB;
        }else{
            return UserAssets::TYPE_2_PACKAGE;
        }
    }

    /**
     * 更新用户资产
     * @param $order_goods_id
     * @param $user_id
     * @param $balance
     */
    public function updateSubBalance($order_goods_id, $user_id, $balance){
        $assets = UserAssets::where('user_id', $user_id)->where('type', UserAssets::TYPE_1_SUB)->where('order_goods_id', $order_goods_id)->first();

        $assets->total = $balance;
        $assets->balance = $balance;
        $assets->save();
    }

    /**
     * 更新用户资产状态
     * @param $order_goods_id
     * @param $user_id
     * @param $status
     */
    public function updateStatus($order_goods_id, $user_id, $status){
        $assets = UserAssets::where('user_id', $user_id)->where('type', UserAssets::TYPE_1_SUB)->where('order_goods_id', $order_goods_id)->first();

        $assets->status = $status;
        $assets->save();
    }
}