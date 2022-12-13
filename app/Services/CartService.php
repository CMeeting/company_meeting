<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 15:54
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

declare (strict_types=1);

namespace App\Services;

use App\Models\CartModels as cart;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\OrderGoods;
use App\Services\OrdersService;
use Auth;
use Illuminate\Support\Facades\Log;

class CartService
{
    public function __construct()
    {

    }

    public function updatedata($data)
    {
        $cart = new cart();
        $goods = new goods();
        if (isset($data['id'])) {
            $arr = ['pay_years' => $data['pay_years']];
            $rest = $cart->_update($arr, "id='{$data['id']}'");
        }elseif (isset($data['delid'])){
            $rest = $cart->_delete([["id","=",$data['delid']]]);
        } else {
            //app_id去空
            $data['appid'] = array_filter($data['appid']);
            //如果appid数量大于1则是machine_id需要校验是否重复
            $appid_num = count($data['appid']);
            if($appid_num > 1){
                if(count(array_unique($data['appid'])) != $appid_num){
                    return ['code'=>500,'msg'=>'machine_id cannot be repeated'];
                }
            }elseif($appid_num == 0){
                return ['code'=>500,'msg'=>'app_id cannot be empty'];
            }

            $appid=implode(",",$data['appid']);
            if(!isset($data['goods_id'])){
                $goods_data = $goods->_find("level1='{$data['products_id']}' and level2='{$data['platform_id']}' and level3='{$data['licensetype_id']}' and deleted=0 and status=1");
            }else{
                $goods_data = $goods->_find("id='{$data['goods_id']}' and deleted=0 and status=1");
            }
            $goods_data = $goods->objToArr($goods_data);
            if (!$goods_data) {
                return ['code' => 403, 'msg' => "该商品不存在或已下架"];
            }
            $cart_info = $cart->_find("goods_id='{$goods_data['id']}' and user_id='{$data['user_id']}' and appid='{$appid}'");
            $cart_info = $cart->objToArr($cart_info);
            if ($cart_info) {
                $arr = ['pay_years' => $data['pay_years'] + $cart_info['pay_years']];
                $rest = $cart->_update($arr, "id='{$cart_info['id']}'");
            } else {
                $arr = [
                    'user_id' => $data['user_id'],
                    'goods_id' => $goods_data['id'],
                    'level1' => $data['products_id'],
                    'level2' => $data['platform_id'],
                    'level3' => $data['licensetype_id'],
                    'appid' => $appid,
                    'pay_years' => $data['pay_years']
                ];
                $rest = $cart->insertGetId($arr);
            }
        }
       if($rest){
           $list=$cart->_where("user_id='{$data['user_id']}'");
           if($list){
               $goodsinfo=$this->get_goods();
               foreach ($list as $k=>$v){
                   $level1 = $goodsinfo['fenlei'][$v['level1']]['title'];
                   $level2 = $goodsinfo['fenlei'][$v['level2']]['title'];
                   $level3 = $goodsinfo['fenlei'][$v['level3']]['title'];
                   $list[$k]['price']=round($goodsinfo['goods'][$v['goods_id']]['price']*$v['pay_years'],2);
                   $list[$k]['goodsname'] = $level1 ." for ". $level2 ." (". $level3.")";
               }
           }
           return ['code'=>200,'msg'=>"ok",'data'=>$list];
       }else{
           return ['code'=>403,'msg'=>"未知错误"];
       }

    }


    public function getdata($user_id){
        $cart = new cart();
        $list=$cart->_where("user_id='{$user_id}'");
        if($list){
            $goodsinfo=$this->get_goods();
            foreach ($list as $k=>$v){
                $level1 = $goodsinfo['fenlei'][$v['level1']]['title'];
                $level2 = $goodsinfo['fenlei'][$v['level2']]['title'];
                $level3 = $goodsinfo['fenlei'][$v['level3']]['title'];
                $list[$k]['price']=$goodsinfo['goods'][$v['goods_id']]['price']*$v['pay_years'];
                $list[$k]['goodsname'] = $level1 ." for ". $level2 ." (". $level3.")";
            }
        }
        return ['code'=>200,'msg'=>"ok",'data'=>$list];
    }

    public function get_goods(){
        $goodsfenlei = new Goodsclassification();
        $goods = new Goods();
        $fenleidata = $goodsfenlei->_where("1=1");
        $goodsdata = $goods->_where("1=1");
        $arr=array();
        foreach ($fenleidata as $k=>$v){
            $arr['fenlei'][$v['id']]=$v;
        }
        foreach ($goodsdata as $k=>$v){
            $arr['goods'][$v['id']]=$v;
        }
        return $arr;
    }


    public function createorder($data)
    {
        $order = new Order();
        $cart = new cart();
        $orderGoods = new OrderGoods();
        $orderserve = new OrdersService();
        $goods = new goods();
        $orderno = time();
        $list = $cart->_where("user_id='{$data['user_id']}'");
        Log::info("用户ID：[" . $data["user_id"] . "]购物车信息：" . json_encode($list, JSON_UNESCAPED_UNICODE));
        $arr = [];
        $sumprice = $goodstotal = 0;
        foreach ($list as $k => $v) {
            $goods_data = obj_to_arr($goods->_find("level1='{$v['level1']}' and level2='{$v['level2']}' and level3='{$v['level3']}' and deleted=0 and status=1"));
//            $goods_data = $goods->objToArr($goods_data);
            if (!$goods_data) {
                Log::info("用户ID：[" . $data["user_id"] . "]该商品不存在或已下架：" . json_encode($v, JSON_UNESCAPED_UNICODE));
                return ['code' => 403, 'msg' => "商品ID：" . $v['goods_id'] . "该商品不存在或已下架"];
            }
            $ordergoods_no = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . time();
            $price = $v['pay_years'] * $goods_data['price'];
            $arr[] = [
                'goods_no' => $ordergoods_no,
                'pay_type' => $data['pay_type'],
                'order_no' => $orderno,
                'status' => 0,
                'type' => 2,
                'details_type' => 2,
                'price' => $price,
                'user_id' => $data['user_id'],
                'appid' => $v["appid"],
                'goods_id' => $goods_data['id'],
                'pay_years' => $v['pay_years'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];
            $goodstotal++;
            $sumprice += $price;
        }
        $orderdata = [
            'order_no' => $orderno,
            'pay_type' => $data['pay_type'],
            'status' => 0,
            'type' => 2,
            'details_type' => 2,
            'price' => $sumprice,
            'user_id' => $data['user_id'],
            'user_bill' => serialize($data['info']),
            'goodstotal' => $goodstotal
        ];
        try {
            DB::beginTransaction();
            Log::info("用户ID：[" . $data["user_id"] . "]创建购物车主订单：" . json_encode($orderdata, JSON_UNESCAPED_UNICODE));
            $order_id = $order->insertGetId($orderdata);
            foreach ($arr as $k => $v) {
                $arr[$k]['order_id'] = $order_id;
                $arr[$k]['order_no'] = $orderno;
            }
            Log::info("用户ID：[" . $data["user_id"] . "]创建购物车子订单：" . json_encode($arr, JSON_UNESCAPED_UNICODE));
            $orderGoods->_insert($arr);
            $orderdata['email'] = $data['info']['email'] ?? '';
            $orderdata['id'] = $order_id;
            $pay = $orderserve->comparePriceCloseAndCreateOrder($orderdata);
            DB::table("order_cart")->whereRaw("user_id='{$data['user_id']}'")->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '创建失败'];
        }
        DB::commit();
        Log::info("用户ID：[" . $data["user_id"] . "]创建购物车订单成功：" . json_encode($arr, JSON_UNESCAPED_UNICODE));
        return ['code' => 200, 'msg' => "创建订单成功", 'data' => ['order_id' => $order_id, 'pay' => $pay]];
    }
}