<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $order_goods_id             子订单id
 * @property    $user_id                    用户id
 * @property    $type                       类型 1:订阅 2：package 3:免费
 * @property    $status                     状态 1：启用 2：无效
 * @property    $total                      总量
 * @property    $balance                    余量
 * @property    $updated_at
 * @property    $created_at
 * @mixin       \Eloquent
 * Class UserAssets
 * @package App\Models
 */
class UserAssets extends Model
{
    protected $table = 'user_assets';

    const TYPE_1_SUB = 1;
    const TYPE_2_PACKAGE = 2;
    const TYPE_3_FREE = 3;

    const STATUS_1_ENABLE = 1;
    const STATUS_2_DISABLE = 2;

    public function orderGoods(){
        return $this->hasOne(OrderGoods::class, 'id', 'order_goods_id');
    }
}