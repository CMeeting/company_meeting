<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BackGroundUserBalance
 * @package App\Models
 * @property    $id
 * @property    $user_id
 * @property    $tenant_id
 * @property    $date
 * @property    $description_type
 * @property    $description
 * @property    $change_type
 * @property    $balance_change
 * @property    $remaining_files
 * @property    $expired_date
 * @property    $create_date
 * @property    $create_by
 * @property    $update_date
 * @property    $update_by
 */

class BackGroundUserBalance extends Model
{
    protected $table = 'background_user_balance';

    const CREATED_AT =  'create_date';
    const UPDATED_AT = 'update_date';

    const DESCRIPTION_TYPE_1_PACKAGE = 1;
    const DESCRIPTION_TYPE_2_SUBSCRIPTION = 2;

    const CHANGE_TYPE_1_RECHARGE = 1;   //充值
    const CHANGE_TYPE_2_USED = 2;   //消费

    public static function add($user_id, $tenant_id, $asset_type, $balance_change, $change_type, $cycle){
        if($asset_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
            $description_type = self::DESCRIPTION_TYPE_2_SUBSCRIPTION;
            if($cycle == OrderGoods::CYCLE_1_MONTH){
                $description = 'Monthly Subscription Files';
            }else{
                $description = 'Annually Subscription Files';
            }
        }else{
            $description_type = self::DESCRIPTION_TYPE_1_PACKAGE;
            $description = "Package Flies";
        }

        $remaining_files = self::getRemainingFiles($user_id);

        if($change_type == self::CHANGE_TYPE_1_RECHARGE){
            $remaining_files = $remaining_files + $balance_change;
        }else{
            $remaining_files = $remaining_files - $balance_change;
        }

        $model = new BackGroundUserBalance();
        $model->user_id = $user_id;
        $model->tenant_id = $tenant_id;
        $model->date = date('Y-m-d H:i:s');
        $model->description_type = $description_type;
        $model->description = $description;
        $model->change_type = $change_type;
        $model->balance_change = $balance_change;
        $model->remaining_files = $remaining_files;
        $model->save();
    }

    public static function getRemainingFiles($user_id, $description_type = ''){
        $query = BackGroundUserBalance::query()
            ->where('user_id', $user_id);

        if($description_type){
            $query->where('description_type', $description_type);
        }

        return $query->orderByDesc('id')->value('remaining_files');
    }
}