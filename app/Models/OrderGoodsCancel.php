<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderGoodsCancel
 * @package App\Models
 * @property    $id
 * @property    $order_goods_id
 * @property    $status
 * @property    $reset_date
 * @property    $remark
 * @property    $created_at
 * @property    $updated_at
 */

class OrderGoodsCancel extends Model
{
    protected $table = 'order_goods_cancel';

    const STATUS_1_UNPROCESSED = 1;
    const STATUS_2_PROCESSED = 2;

    public static function add($order_goods_id, $status, $reset_date, $remark){
        $model = new OrderGoodsCancel();
        $model->order_goods_id = $order_goods_id;
        $model->status = $status;
        $model->reset_date = $reset_date;
        $model->remark = $remark;
        $model->save();
    }
}