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
    const CHANGE_TYPE_2_used = 2;   //消费

    public static function add($user_id, $tenant_id, $asset_type, $balance_change, $change_type){
        if($asset_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
            $description_type = self::DESCRIPTION_TYPE_2_SUBSCRIPTION;
            $description = "Monthly Subscription($balance_change files)";
        }else{
            $description_type = self::DESCRIPTION_TYPE_1_PACKAGE;
            $description = "Package($balance_change files)";
        }

        $remaining_files = self::getRemainingFiles($user_id, $balance_change);

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

    public static function getRemainingFiles($user_id, $balance_change){
        $remaining_files = BackGroundUserBalance::query()
            ->where('user_id', $user_id)
            ->orderByDesc('create_date')
            ->value('remaining_files');

        if(!$remaining_files && $remaining_files != 0){
            $remaining_files = 0;
        }

        return $remaining_files + $balance_change;
    }
}