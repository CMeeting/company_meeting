<?php


namespace App\Models;

use App\Services\CommonService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class OrderCashFlow
 * @package App\Models
 * @property    $id
 * @property    $serial_number
 * @property    $order_id
 * @property    $pay_type
 * @property    $trade_type
 * @property    $price
 * @property    $tax
 * @property    $rate
 * @property    $real_price
 * @property    $trade_id
 * @property    $pay_id
 * @property    $currency
 * @property    $invoice_num
 * @property    $invoice_url
 * @property    $user_bill
 * @property    $created_at
 * @property    $updated_at
 * @property    $del_flag
 */

class OrderCashFlow extends Model
{

    const CURRENCY_1_USD = 1;
    const CURRENCY_2_CNY = 2;

    public static function add($order_id, $pay_type, $trade_type, $price, $tax, $rate, $real_price, $trade_id, $pay_id, $currency){
        $serial_number = CommonService::createUuid();

        $model = new OrderCashFlow();
        $model->serial_number = $serial_number;
        $model->order_id = $order_id;
        $model->pay_type = $pay_type;
        $model->trade_type = $trade_type;
        $model->price = $price;
        $model->tax = $tax;
        $model->rate = $rate;
        $model->real_price = $real_price;
        $model->trade_id = $trade_id;
        $model->pay_id = $pay_id;
        $model->currency = $currency;
        $model->save();
    }
}