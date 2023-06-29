<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class BackGroundUserRemain
 * @package App\Models
 * @property    $id
 * @property    $tenant_id
 * @property    $user_id
 * @property    $asset_type
 * @property    $total_files
 * @property    $used_files
 * @property    $balance
 * @property    $status
 * @property    $create_date
 * @property    $create_by
 * @property    $update_date
 * @property    $update_by
 * @property    $start_date
 * @property    $end_date
 * @property    $change_num     修改数量，数据库没有这个字段，方便代码调用添加
 */

class BackGroundUserRemain extends Model
{
    protected $table = 'background_user_remain';

    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    const STATUS_1_ACTIVE = 1;
    const STATUS_2_INACTIVE = 0;

    const OPERATE_TYPE_1_ADD = 1;           //购买
    const OPERATE_TYPE_2_RESET = 2;         //重置
    const OPERATE_TYPE_3_CANCEL = 3;        //取消订阅

    public static function getByTypeUserId($user_id, $type){
        return BackGroundUserRemain::query()
            ->where('user_id', $user_id)
            ->where('asset_type', $type)
            ->first();
    }

    public static function add($tenant_id, $user_id, $asset_type, $total_files, $status, $start_date, $end_date){
        $model = new BackGroundUserRemain();
        $model->tenant_id = $tenant_id;
        $model->user_id = $user_id;
        $model->asset_type = $asset_type;
        $model->total_files = $total_files;
        $model->status = $status;
        $model->start_date = $start_date;
        $model->end_date = $end_date;
        $model->save();

        return $model;
    }

    /**
     * 更新用户资产表
     * @param BackGroundUserRemain $model
     * @param $total_files
     * @param $type
     * @param null $start_date
     * @param null $end_date
     * @return BackGroundUserRemain
     */
    public static function updateAssetType(BackGroundUserRemain $model, $total_files, $type, $start_date = null, $end_date = null){
        if($model->asset_type == OrderGoods::PACKAGE_TYPE_2_PACKAGE){
            //package 累加资产
            $model->total_files += $total_files;
        }else{
            if($type == BackGroundUserRemain::OPERATE_TYPE_1_ADD){
                //订阅购买，累加
                $model->total_files += $total_files;
            }elseif($type == BackGroundUserRemain::OPERATE_TYPE_2_RESET){
                //订阅重置资产, 直接重置
                $model->total_files = $total_files;
            }elseif($type == BackGroundUserRemain::OPERATE_TYPE_3_CANCEL){
                //订阅取消资产，扣除当前订单的档位
                $model->total_files -= $total_files;
                //扣除后total_files = 0 证明用户没有订阅了
                if($model->total_files == 0){
                    $model->status = BackGroundUserRemain::STATUS_2_INACTIVE;
                }
            }
        }

        if($start_date != null){
            $model->start_date = $start_date;
        }

        if($end_date != null){
            $model->end_date = $end_date;
        }

        $model->save();

        return $model;
    }
}