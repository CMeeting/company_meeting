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

use App\Export\GoodsExport;
use App\Export\UserExport;
use App\Http\Controllers\Api\biz\AlipayBiz;
use App\Http\Controllers\Api\biz\PaddleBiz;
use App\Http\Controllers\Api\biz\WechatPay;
use App\Models\Goodsclassification;
use App\Models\LicenseModel;
use App\Models\Order;
use App\Models\Goods;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\OrderGoods;
use Auth;

class OrdersService
{
    public function __construct()
    {

    }
    public static $payments = ['paddle' => 1, 'alipay' => 2, 'wxpay' => 3];

    public function data_list($param)
    {
        $where = "1=1";
        if ($param['info']) {
            $where .= " and {$param['query_type']}='{$param['info']}'";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            $where .= " and orders.status={$param['status']}";
        }
        if ($param['details_type']) {
            $where .= " and orders.details_type={$param['details_type']}";
        }
        if ($param['type']) {
            $where .= " and orders.type={$param['type']}";
        }

        if (isset($param['pay_at']) && $param['pay_at'] && isset($param['endpay_at']) && $param['endpay_at']) {
            $where .= " AND orders.pay_time BETWEEN '" . $param['pay_at'] . "' AND '" . $param['endpay_at'] . "'";
        } elseif (isset($param['pay_at']) && $param['pay_at'] && empty($param['endpay_at'])) {
            $where .= " AND orders.pay_time >= '" . $param['pay_at'] . "'";
        } elseif (isset($param['endpay_at']) && $param['endpay_at'] && empty($param['pay_at'])) {
            $where .= " AND orders.pay_time <= '" . $param['endpay_at'] . "'";
        }

        if (isset($param['shelf_at']) && $param['shelf_at'] && isset($param['endshelf_at']) && $param['endshelf_at']) {
            $where .= " AND orders.created_at BETWEEN '" . $param['shelf_at'] . "' AND '" . $param['endshelf_at'] . "'";
        } elseif (isset($param['shelf_at']) && $param['shelf_at'] && empty($param['endshelf_at'])) {
            $where .= " AND orders.created_at >= '" . $param['shelf_at'] . "'";
        } elseif (isset($param['endshelf_at']) && $param['endshelf_at'] && empty($param['shelf_at'])) {
            $where .= " AND orders.created_at <= '" . $param['endshelf_at'] . "'";
        }

        $goods = new Order();

        if ($param['export'] == 1) {
            return $goods->whereRaw($where)->orderByRaw('id desc')->get()->toArray();
        } else {
            $data = $goods->leftJoin('users', 'orders.user_id', '=', 'users.id')->whereRaw($where)->orderByRaw('orders.id desc')->selectRaw("orders.*,users.email")->paginate(10);
        }

        return $data;
    }

    public function sum_data($param){
        $where = "1=1";
        if ($param['info']) {
            $where .= " and {$param['query_type']}='{$param['info']}'";
        }
        if ($param['details_type']) {
            $where .= " and orders.details_type={$param['details_type']}";
        }
        if ($param['type']) {
            $where .= " and orders.type={$param['type']}";
        }
        if (isset($param['pay_at']) && $param['pay_at'] && isset($param['endpay_at']) && $param['endpay_at']) {
            $where .= " AND orders.pay_time BETWEEN '" . $param['pay_at'] . "' AND '" . $param['endpay_at'] . "'";
        } elseif (isset($param['pay_at']) && $param['pay_at'] && empty($param['endpay_at'])) {
            $where .= " AND orders.pay_time >= '" . $param['pay_at'] . "'";
        } elseif (isset($param['endpay_at']) && $param['endpay_at'] && empty($param['pay_at'])) {
            $where .= " AND orders.pay_time <= '" . $param['endpay_at'] . "'";
        }
        if (isset($param['shelf_at']) && $param['shelf_at'] && isset($param['endshelf_at']) && $param['endshelf_at']) {
            $where .= " AND orders.created_at BETWEEN '" . $param['shelf_at'] . "' AND '" . $param['endshelf_at'] . "'";
        } elseif (isset($param['shelf_at']) && $param['shelf_at'] && empty($param['endshelf_at'])) {
            $where .= " AND orders.created_at >= '" . $param['shelf_at'] . "'";
        } elseif (isset($param['endshelf_at']) && $param['endshelf_at'] && empty($param['shelf_at'])) {
            $where .= " AND orders.created_at <= '" . $param['endshelf_at'] . "'";
        }
        $goods = new Order();
        $data = $goods->leftJoin('users', 'orders.user_id', '=', 'users.id')->whereRaw($where)->get()->toArray();
        $arr=[];
        $price=0;
        $sumcount=0;
        $sumnostatus=0;
        $sumyesstatus=0;
        $sumgbstatus=0;
        $sumwcstatus=0;
        foreach ($data as $k=>$v){
            $sumcount++;
            $price+=$v['price'];
            switch ($v['price']){
                case 0:
                    $sumnostatus++;
                    break;
                case 1:
                    $sumyesstatus++;
                    break;
                case 2:
                    $sumwcstatus++;
                    break;
                case 4:
                    $sumgbstatus++;
                    break;
            }
        }
        $arr['price']=$price;
        $arr['sumcount']=$sumcount;
        $arr['sumnostatus']=$sumnostatus;
        $arr['sumyesstatus']=$sumyesstatus;
        $arr['sumwcstatus']=$sumwcstatus;
        $arr['sumgbstatus']=$sumgbstatus;
        return $arr;
    }

    public function rundata($param)
    {
        $data = $param['data'];
        $user = new User();
        $lisecosdmode=new LicenseModel();
        $goods = new Goods();
        $order = new Order();
        $orderGoods = new OrderGoods();
        $is_user = $user->existsEmail($data['email']);
        if (!$is_user) {
            $arr['full_name'] = $data['email'];
            $arr['email'] = $data['email'];
            $arr['flag'] = 2;
            $arr['created_at'] = date("Y-m-d H:i:s");
            $arr['updated_at'] = date("Y-m-d H:i:s");
            $user_id = Db::table("users")->insertGetId($arr);
        } else {
            $users = DB::table('users')->where('email', $data['email'])->first();
            $user_id = $users->id;
        }
        $goods_data = $goods->_where("deleted=0 and status=1");
        $arr = [];
        $sumprice = 0;
        $goodstotal = 0;
        if($data['status']==1){
            $pay_type=4;
        }else{
            $pay_type=0;
        }
        foreach ($data['level1'] as $k => $v) {
            foreach ($goods_data as $ks => $vs) {
                if ($v == $vs['level1'] && $data['level2'][$k] == $vs['level2'] && $data['level3'][$k] == $vs['level3']) {
                    $goodsid = $vs['id'];
                    $price = $vs['price'];
                }
            }
            $ordergoods_no=chr(rand(65,90)).time();
            $s = $k + 1;
            $lisecosd = str_pad("'".mt_rand(1,9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT);
            $license_secret = str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT);
            $lisecosdata[]=[
                'goods_no'=>$ordergoods_no,
                'user_id'=>$user_id,
                'products_id'=>$v,
                'platform_id'=>$data['level2'][$k],
                'licensetype_id'=>$data['level3'][$k],
                'license_key'=>$lisecosd,
                'license_secret'=>$license_secret,
                'uuid'=>implode(',', $data["appid$s"]),
                'period'=>$data['period'][$k],
                'type'=>2,
                'status'=>1,
                'expire_time'=>date("Y-m-d H:i:s",strtotime("+".$data['period'][$k]." year"))
            ];

            $arr[] = [
                'goods_no'=>$ordergoods_no,
                'status' => $data['status'],
                'type' => 1,
                'details_type' => 2,
                'pay_type'=>$pay_type,
                'price' => $price,
                'pay_years'=>$data['period'][$k],
                'user_id' => $user_id,
                'appid' => implode(',', $data["appid$s"]),
                'pay_years' => $data['period'][$k],
                'goods_id' => $goodsid,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];
            $goodstotal++;
            $sumprice += $price;
        }
        $orderno = time();
        $orderdata = [
            'pay_type'=>$pay_type,
            'order_no' => $orderno,
            'status' => $data['status'],
            'type' => 1,
            'details_type' => 2,
            'price' => $sumprice,
            'user_id' => $user_id,
            'goodstotal' => $goodstotal
        ];

        try {
            $order_id = $order->insertGetId($orderdata);
            foreach ($arr as $k => $v) {
                $arr[$k]['order_id'] = $order_id;
                $arr[$k]['order_no'] = $orderno;
                $ordergoods_id=$orderGoods->insertGetId($arr[$k]);
                if($ordergoods_id){
                    $lisecosdata[$k]['order_id']=$order_id;
                    $lisecosdata[$k]['ordergoods_id']=$ordergoods_id;
                    $lisecosdmode->insertGetId($lisecosdata[$k]);
                }
            }
            if ($data['status'] == 1) {
                $user_info = $user->_find("id='{$user_id}'");
                $user_info = $user->objToArr($user_info);
                $userprice = $user_info['order_amount'] + $sumprice;
                $userorder = $user_info['order_num'] + 1;
                $user->_update(['order_amount' => $userprice, 'order_num' => $userorder], "id='{$user_id}'");
            }
        } catch (Exception $e) {
            return ['code' => 500, 'message' => 'Invalid Token'];
        }
        return ['code' => 200];
    }

    public function data_info($id)
    {
        $orderGoods = new OrderGoods();
        $ordergoodsdata = $orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->leftJoin('users', 'orders_goods.user_id', '=', 'users.id')
            ->whereRaw("order_id='{$id}'")
            ->selectRaw("orders_goods.*,users.email,goods.level1,goods.level2,goods.level3")
            ->get()->toArray();
        if (!empty($ordergoodsdata)) {
            $classification = $this->assembly_classification();
            foreach ($ordergoodsdata as $k => $v) {
                $ordergoodsdata[$k]['products'] = $classification[$v['level1']]['title'];
                $ordergoodsdata[$k]['platform'] = $classification[$v['level2']]['title'];
                $ordergoodsdata[$k]['licensie'] = $classification[$v['level3']]['title'];
            }
        }
        return $ordergoodsdata;
    }

    public function update_status($id)
    {
        $order = new Order();
        $orderGoods = new OrderGoods();
        try {
            $order->_update(['status' => 4], "id='{$id}'");
            $orderGoods->_update(['status' => 4], "order_id='{$id}'");
        } catch (Exception $e) {
            return ['code' => 500, 'message' => '关闭失败'];
        }
        return ['code' => 0];
    }


    public function get_orderinfo($pram)
    {
        $order = new Order();
        $orderGoods = new OrderGoods();
        $data = $order->_find("user_id='{$pram['user_id']}' and id='{$pram['order_id']}'");
        $data = $order->objToArr($data);
        if (!$data) {
            return ['code' => 403, 'msg' => "订单不存在或不是该用户订单"];
        }
        $ordergoodsdata = $orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->leftJoin('license_code', 'orders_goods.id', '=', 'license_code.ordergoods_id')
            ->whereRaw("orders_goods.order_id='{$pram['order_id']}'")
            ->selectRaw("orders_goods.appid,orders_goods.pay_type,orders_goods.status,orders_goods.price,orders_goods.id,goods.level1,goods.level2,goods.level3,license_code.license_key_url,license_code.period,license_code.period,license_code.expire_time,license_code.created_at")
            ->get()->toArray();
        if (!empty($ordergoodsdata)) {
            $classification = $this->assembly_orderclassification();
            foreach ($ordergoodsdata as $k => $v) {
                $ordergoodsdata[$k]['products'] = isset($classification[$v['level1']]['title'])?$classification[$v['level1']]['title']:"";
                $ordergoodsdata[$k]['platform'] = isset($classification[$v['level2']]['title'])?$classification[$v['level2']]['title']:"";
                $ordergoodsdata[$k]['licensie'] = isset($classification[$v['level3']]['title'])?$classification[$v['level3']]['title']:"";
                switch ($v['pay_type']){
                    case 1:
                        $ordergoodsdata[$k]['payname']="paddle";
                        break;
                    case 2:
                        $ordergoodsdata[$k]['payname']="AliPay";
                        break;
                    case 3:
                        $ordergoodsdata[$k]['payname']="WeChat Pay";
                        break;
                    case 4:
                        $ordergoodsdata[$k]['payname']="unpaid";
                        break;
                }
            }
        }
        $data['list'] = $ordergoodsdata;
        return ['code' => 200, 'msg' => 'ok', 'data' => $data];

    }


    public function get_orderlist($parm){
        $order = new Order();
        $orderGoods = new OrderGoods();
        $data=$order->_where("user_id='{$parm['user_id']}'","id DESC","id,status,created_at,price");
        if(!$data){
            return ['code' => 403, 'msg' => '当前没有订单数据', 'data' => []];
        }
        $ordergoodsdata=$orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->whereRaw("orders_goods.user_id='{$parm['user_id']}'")
            ->selectRaw("goods.level1,goods.level2,goods.level3,orders_goods.order_id")
            ->get()->toArray();
        $classification = $this->assembly_orderclassification();
        foreach ($data as $k=>$v){
            foreach ($ordergoodsdata as $ks=>$vs){
                if($v['id']==$vs['order_id']){
                    if(isset($classification['fenlei'][$vs['level1']]['title'])){
                        $level1=$classification['fenlei'][$vs['level1']]['title'];
                    }else{
                        $level1="";
                    }
                    if(isset($classification['fenlei'][$vs['level2']]['title'])){
                        $level2=$classification['fenlei'][$vs['level2']]['title'];
                    }else{
                        $level2="";
                    }
                    if(isset($classification['fenlei'][$vs['level3']]['title'])){
                        $level3=$classification['fenlei'][$vs['level3']]['title'];
                    }else{
                        $level3="";
                    }
                    $data[$k]['list'][]=$level1.$level2.$level3;
                }
            }
        }
        return ['code' => 200, 'msg' => 'ok', 'data' => $data];
    }

    public function get_ordertryoutlist($parm){
        $orderGoods = new OrderGoods();
        $ordergoodsdata=$orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->whereRaw("orders_goods.user_id='{$parm['user_id']}' and orders_goods.details_type=1")
            ->selectRaw("goods.level1,goods.level2,goods.level3,orders_goods.order_id,orders_goods.goods_id,orders_goods.appid,orders_goods.created_at")
            ->get()->toArray();
        $classification = $this->assembly_orderclassification();
            foreach ($ordergoodsdata as $ks=>$vs){
                $ordergoodsdata[$ks]['goodsname']=$classification[$vs['level1']]['title'].$classification[$vs['level2']]['title'].$classification[$vs['level3']]['title'];
                $ordergoodsdata[$ks]['peroid']="1 month";
            }
        return ['code' => 200, 'msg' => 'ok', 'data' => $ordergoodsdata];
    }

    public function createorder($data){
        $order = new Order();
        $orderGoods = new OrderGoods();
        $goods = new goods();
        $orderno=time();
        $goods_data = $goods->_find("level1='{$data['products_id']}' and level2='{$data['platform_id']}' and level3='{$data['licensetype_id']}' and deleted=0 and status=1");
        $goods_data = $goods->objToArr($goods_data);
        if (!$goods_data) {
            return ['code' => 403, 'msg' => "该商品不存在或已下架"];
        }
        $orderarr=[
            'order_no'=>$orderno,
            'pay_type'=>0,
            'status'=>0,
            'type'=>2,
            'details_type'=>$data['details_type'],
            'user_id'=>$data['user_id'],
            'user_bill'=>serialize($data['info']),
            'goodstotal'=>1
        ];
        $ordergoodsarr=[
            'pay_type'=>0,
            'order_no'=>$orderno,
            'status'=>0,
            'type'=>2,
            'appid'=>implode(",",$data['appid']),
            'goods_id'=>$goods_data['id'],
            'details_type'=>$data['details_type'],
            'user_id'=>$data['user_id']
        ];
        if($data['details_type']==1){
            $data['appid']=implode(",",$data['appid']);
            $gooodsdata=$orderGoods->_find("user_id='{$data['user_id']}' and appid='{$data['appid']}' and details_type='{$data['details_type']}' and status=2 and goods_id='{$goods_data['id']}'");
            $gooodsdata=$orderGoods->objToArr($gooodsdata);
            if($gooodsdata){
                return ['code' => 403, 'msg' => "该APPID在当前商品已存在试用订单"];
            }
            $orderarr['status']=2;
            $orderarr['pay_type']=4;
            $orderarr['price']=0.00;
            $ordergoodsarr['status']=2;
            $ordergoodsarr['pay_type']=4;
            $ordergoodsarr['price']=0.00;
            $ordergoodsarr['pay_years']=1;
            try {
                $order_id=$order->insertGetId($orderarr);
                $ordergoodsarr['order_id']=$order_id;
                $orderGoods->insertGetId($ordergoodsarr);
            } catch (Exception $e) {
                return ['code' => 500, 'message' => '创建失败'];
            }
            return ['code' => 200, 'msg' => "创建试用订单成功",'data'=>['order_id'=>$order_id]];
        }else{
            if(!isset($data['pay_type'])){
                return ['code' => 403, 'msg' => "请选择支付方式"];
            }
            $data['appid']=implode(",",$data['appid']);
            $price=$data['pay_years']*$goods_data['price'];
            $orderarr['status']=0;
            $orderarr['pay_type']=$data['pay_type'];
            $orderarr['price']=$price;
            $ordergoodsarr['status']=0;
            $ordergoodsarr['pay_type']=$data['pay_type'];
            $ordergoodsarr['price']=$price;
            $ordergoodsarr['pay_years']=$data['pay_years'];
            try {
                $order_id=$order->insertGetId($orderarr);
                $ordergoodsarr['order_id']=$order_id;
                $pay=$this->comparePriceCloseAndCreateOrder($orderarr);
                $orderGoods->insertGetId($ordergoodsarr);
            } catch (Exception $e) {
                return ['code' => 500, 'message' => '创建失败'];
            }
            return ['code' => 200, 'msg' => "创建订单成功",'data'=>['order_id'=>$order_id,'pay'=>$pay]];
        }

    }

    public function get_license($parm){

        if(isset($parm['type'])){
            $wehere="orders_goods.user_id='{$parm['user_id']}' and (orders_goods.status=1 or orders_goods.status=2) and orders_goods.details_type=1";
        }else{
            $wehere="orders_goods.user_id='{$parm['user_id']}' and (orders_goods.status=1 or orders_goods.status=2)";
        }
        $orderGoods = new OrderGoods();
        $ordergoodsdata=$orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->leftJoin('license_code', 'orders_goods.order_id', '=', 'license_code.order_id')
            ->whereRaw($wehere)
            ->selectRaw("goods.level1,goods.level2,goods.level3,orders_goods.order_id,orders_goods.goods_id,license_code.uuid as appid,license_code.expire_time,license_code.status,license_code.license_key,license_code.license_secret")
            ->get()->toArray();
        if(!$ordergoodsdata){
            return ['code' => 403, 'msg' => '没有数据', 'data' => []];
        }
        $classification = $this->assembly_orderclassification();
            foreach ($ordergoodsdata as $ks=>$vs){
                $ordergoodsdata[$ks]['goodsname']=$classification[$vs['level1']]['title'].$classification[$vs['level2']]['title'].$classification[$vs['level3']]['title'];
            }

        return ['code' => 200, 'msg' => 'ok', 'data' => $ordergoodsdata];
    }

    function assembly_classification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("deleted=0");
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }

    function assembly_orderclassification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("1=1");
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }


    public function comparePriceCloseAndCreateOrder($order)
    {
        $ordernew = new Order();
        if (empty($order['page_pay_url'])) {
            $pay_url_data = $this->generatePayUrl($order['pay_type'],'test', $order['order_no'], $order['price']);
            $newOrderData = [
                'third_order_no' => $pay_url_data['id'] ?? '',
                'page_pay_url' => $pay_url_data['url'],
            ];
            //$ordernew->_update($newOrderData, "id='{$order['id']}'");
        }
        return $newOrderData;
    }


    public function generatePayUrl($payment, $product, $trade_no, $price)
    {
        $call_back = $this->headerurl();
        $pay_url_data = [];
        if ($payment == self::$payments['paddle']) {
            $paddle = new PaddleBiz();
            $pay_url_data = $paddle->createPayLink($trade_no,$product,$price);
        } elseif ($payment == self::$payments['alipay']) {
            $pay_redirect_path = '/resubscribe/payed';
            $return_url = $call_back . $pay_redirect_path;
            $pay_url_data = $this->getAliPayUrl($trade_no, $product, $price, $call_back, $return_url);
        } else if ($payment == self::$payments['wxpay']) {
            $pay_url_data = WechatPay::wechatPay($trade_no, $product, $price, $call_back);
        }
        return $pay_url_data;
    }

    public function getAliPayUrl($trade_no, $name, $price, $call_back,$return_url)
    {
        $paramss = [
            'out_trade_no' => $trade_no,
            'subject' => $name,
            'payment_type' => 1,//支付类型 只取值为1(商品购买) 固定值
            'total_fee' => $price,
            'body' => $name
        ];
        $obj = new AlipayBiz($paramss);

        // 支付宝验证KEY统一调整为扫码支付
        return $obj->pay($call_back . '/api/orders/alipayNotify',$return_url);
    }

    function headerurl(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return  $http_type . $_SERVER['HTTP_HOST'];
    }

}