<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class UserSubscriptionHandle
 * @package App\Models
 * @property    $id
 * @property    $user_id
 * @property    $type
 * @property    $order_goods_id
 * @property    $status
 * @property    $reset_date
 * @property    $next_billing_time
 * @property    $remark
 * @property    $created_at
 * @property    $updated_at
 */

class UserSubscriptionProcess extends Model
{
    protected $table = 'user_subscription_process';

    const TYPE_1_DEDUCTED_SUCCESS = 1;
    const TYPE_2_DEDUCTED_FAILED = 2;
    const TYPE_3_CANCEL_SUBSCRIPTION = 3;

    const STATUS_1_UNPROCESSED = 1;
    const STATUS_2_PROCESSED = 2;

    public static function add($order_goods_id, $user_id, $type, $reset_date, $next_billing_time = null){
        $model = new UserSubscriptionProcess();
        $model->user_id = $user_id;
        $model->type = $type;
        $model->order_goods_id = $order_goods_id;
        $model->status = UserSubscriptionProcess::STATUS_1_UNPROCESSED;
        $model->reset_date = $reset_date;
        $model->next_billing_time = $next_billing_time;
        $model->save();
    }
}