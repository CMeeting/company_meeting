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
 * @property    $id
 * @property    $order_no
 * @property    $merchant_no
 * @property    $pay_type
 * @property    $status
 * @property    $pay_time
 * @property    $type
 * @property    $details_type
 * @property    $price
 * @property    $user_id
 * @property    $user_bill
 * @property    $closetime
 * @property    $goodstotal
 * @property    $created_at
 * @property    $updated_at
 * @property    $bill_no
 * @property    $bill_url
 * @property    $paddle_no
 * @property    $pay_url
 * @property    $isrenwe
 * @property    $renwe_id
 * @property    $tax
 * @property    $remark
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
            ->update(['status' => Order::STATUS_1_PAYED, 'pay_time' => date("Y-m-d H:i:s"), 'bill_url' => $invoice_url, 'third_trade_no' => $third_trade_no]);
    }

    public static function add($order_no, $pay_type, $status, $type, $details_type, $price, $user_id, $goodstotal, $user_bill = ''){
        $model = new Order();
        $model->order_no = $order_no;
        $model->pay_type = $pay_type;
        $model->status = $status;
        $model->type = $type;
        $model->details_type = $details_type;
        $model->price = $price;
        $model->user_id = $user_id;
        $model->user_bill = $user_bill;
        $model->goodstotal = $goodstotal;

        $model->save();

        return $model->id;
    }
}