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
 */

class BackGroundUserRemain extends Model
{
    protected $table = 'background_user_remain';

    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    const STATUS_1_ACTIVE = 1;
    const STATUS_2_INACTIVE = 0;

    public static function getByTypeUserId($user_id, $type){
        return BackGroundUserRemain::query()
            ->where('user_id', $user_id)
            ->where('asset_type', $type)
            ->first();
    }

    public static function add($tenant_id, $user_id, $asset_type, $total_files){
        $model = new BackGroundUserRemain();
        $model->tenant_id = $tenant_id;
        $model->user_id = $user_id;
        $model->asset_type = $asset_type;
        $model->total_files = $total_files;
        $model->status = self::STATUS_1_ACTIVE;
        $model->save();
    }

    public static function updateAssetType(BackGroundUserRemain $model, $total_files){
        if($model->asset_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
            //订阅资产新增时，使用次数改为0, 直接重置资产
            $model->used_files = 0;
            $model->total_files = $total_files;
        }else{
            //package 累加资产
            $model->total_files = $model->total_files + $total_files;
        }

        $model->status = self::STATUS_1_ACTIVE;
        $model->save();
    }
}