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
 * Class Order
 * @package App\Models
 * @mixin       \Eloquent
 */

class Order extends Model
{

    protected $table = 'orders';

    const STATUS_0_UNPAID = 0;

    const STATUS_1_PAYED = 1;

    const DETAILS_STATUS_1_TRIAL = 1;
    const DETAILS_STATUS_2_SDK = 2;
    const DETAILS_STATUS_3_SAAS = 3;

    const TYPE_1_BACKGROUND_CREATE = 1;
    const TYPE_2_USER_BUY = 2;

    public static function orderComplete($third_trade_no, $order_no, $invoice_url){
        \DB::table("orders")
            ->whereRaw("order_no='$order_no'")
            ->update(['status' => Order::STATUS_1_PAYED, 'pay_time' => date("Y-m-d H:i:s"), 'bill_url' => $invoice_url, 'paddle_no' => $third_trade_no]);
    }
}