<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 16:26
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderGoods
 * @package App\Models
 * @property    $id
 * @property    $order_id
 * @property    $order_no
 * @property    $goods_no
 * @property    $merchant_no
 * @property    $details_merchant_no
 * @property    $pay_type
 * @property    $status
 * @property    $pay_time
 * @property    $type
 * @property    $details_type
 * @property    $appid
 * @property    $price
 * @property    $user_id
 * @property    $closetime
 * @property    $pay_years
 * @property    $goods_id
 * @property    $created_at
 * @property    $updated_at
 * @property    $third_trade_no
 * @property    $renwe_goodsid
 * @property    $special_assets
 * @property    $package_type
 * @property    $next_billing_time
 * @mixin       \Eloquent
 */

class OrderGoods extends Model
{

    const DETAILS_TYPE_1_SDK_TRY = 1;   //SDK试用订单
    const DETAILS_TYPE_2_SDK = 2;       //SDK购买订单
    const DETAILS_TYPE_3_SAAS = 3;      //SaaS订单

    const PACKAGE_TYPE_1_PLAN = 1;      //订阅
    const PACKAGE_TYPE_2_PACKAGE = 2;   //package

    const STATUS_0_UNPAID = 0;          //未支付
    const STATUS_1_PAID = 1;            //package:已支付 plan:订阅中
    const STATUS_2_COMPLETED = 2;       //已完成（以弃用）
    const STATUS_3_PENDING_REFUND = 3;  //待退款
    const STATUS_4_CLOSE = 4;           //已关闭
    const STATUS_5_UNSUBSCRIBE = 5;     //取消订阅
    const STATUS_6_REFUNDED = 6;        //已退款

    const PAY_TYPE_1_PADDLE = 1;        //paddle
    const PAY_TYPE_2_ALIPAY = 2;        //支付宝
    const PAY_TYPE_3_WECHAT = 3;        //微信支付
    const PAY_TYPE_4_OTHER = 4;         //其他支付
    const PAY_TYPE_5_PAYPAL = 5;         //paypal

    const TYPE_1_BACKGROUND = 1;        //后台创建
    const TYPE_2_BUY = 2;               //在线购买

    const CYCLE_1_MONTH = 1;            //月订阅
    const CYCLE_2_YEAR = 2;             //年订阅

    const EVENT_1_PAYMENT_SUCCESS = 1;  //支付成功
    const EVENT_2_PAYMENT_FAILED = 2;   //支付失败
    const EVENT_3_DEDUCTION_SUCCESS = 3;//订阅自动扣款成功
    const EVENT_4_DEDUCTION_FAILED = 4; //订阅自动扣款失败
    const EVENT_5_PLAN_CANCEL = 5;      //取消订阅

    protected $table = 'orders_goods';

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function orderComplete($order_no, $third_trade_no){
        \DB::table("orders_goods")
            ->whereRaw("order_no='{$order_no}'")
            ->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'third_trade_no' => $third_trade_no]);
    }

    public static function add($order_id, $order_no, $goods_no, $pay_type, $status, $type, $details_type, $price, $user_id, $goods_id, $package_type, $pay_years = '', $special_assets = 0){
        $model = new OrderGoods();
        $model->order_id = $order_id;
        $model->order_no = $order_no;
        $model->goods_no = $goods_no;
        $model->pay_type = $pay_type;
        $model->status = $status;
        $model->type = $type;
        $model->details_type = $details_type;
        $model->price = $price;
        $model->user_id = $user_id;
        $model->pay_years = $pay_years;
        $model->goods_id = $goods_id;
        $model->package_type = $package_type;
        $model->special_assets = $special_assets;

        //后台创建，支付时间等于当前时间
        if($type == self::TYPE_1_BACKGROUND){
            $model->pay_time = Carbon::now('Y-m-d H:i:s');
        }

        $model->save();

        return $model;
    }

    /**
     * 修改子订单状态
     * @param $order_id
     * @param $status
     */
    public static function updateStatus($order_id, $status){
        OrderGoods::query()
            ->where('order_id', $order_id)
            ->update(['status'=>$status]);
    }

    /**
     * 获取订单
     * @param $order_id
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getByOrderId($order_id){
        return OrderGoods::query()
            ->where('order_id', $order_id)
            ->first();
    }

    public static function getByOrderNo($order_no){
        return OrderGoods::query()
            ->where('order_no', $order_no)
            ->first();
    }
}