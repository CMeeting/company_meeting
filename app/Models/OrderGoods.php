<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 16:26
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderGoods
 * @package App\Models
 *  * @mixin       \Eloquent
 */

class OrderGoods extends Model
{

    protected $table = 'orders_goods';

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function orderComplete($order_no, $third_trade_no){
        \DB::table("orders_goods")
            ->whereRaw("order_no='{$order_no}'")
            ->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'paddle_no' => $third_trade_no]);
    }
}