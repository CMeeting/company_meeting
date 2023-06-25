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

    public static function updateAssetType(BackGroundUserRemain $model, $total_files, $status, $start_date = null, $end_date = null){
        if($model->asset_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
            //订阅重置 total_files
            $model->total_files = $total_files;
        }else{
            //package 累加资产
            $model->total_files = $model->total_files + $total_files;
        }

        if($start_date != null){
            $model->start_date = $start_date;
        }

        if($end_date != null){
            $model->end_date = $end_date;
        }

        $model->status = $status;
        $model->save();

        return $model;
    }
}