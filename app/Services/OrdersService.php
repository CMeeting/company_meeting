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
use App\Http\Controllers\Api\biz\PaypalBiz;
use App\Models\BackGroundUser;
use App\Models\BackGroundUserBalance;
use App\Models\BackGroundUserRemain;
use App\Models\OrderCashFlow;
use App\Models\UserAssets;
use Carbon\Carbon;
use PDF;
use App\Http\Controllers\Api\biz\AlipayBiz;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\biz\PaddleBiz;
use App\Http\Controllers\Api\biz\WechatPay;
use App\Http\extend\wechat\example\WxPayConfig;
use App\Services\CommonService;
use App\Models\Goodsclassification;
use App\Models\LicenseModel;
use App\Http\extend\core\helper\ObjectHelper;
use App\Models\Mailmagicboard;
use App\Models\Order;
use App\Models\Goods;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\OrderGoods;


class OrdersService
{
    public function __construct()
    {

    }

    /**
     * Renew直接获取订单信息并生成新的支付链接（弃用）
     * @param $user_id
     * @param $order_no
     * @param $login_user_email
     * @return array
     */
    public function checkAndCreate($user_id, $order_no, $login_user_email)
    {
        if (!$order_no) {
            return ["code" => 201, "msg" => "订单号不能为空！"];
        }

        $where = ["status" => 1, "user_id" => $user_id, "order_no" => $order_no];
        $result = DB::table("orders")->where($where)->first();
        if (!$result) {
            return ["code" => 301, "msg" => "该用户没有购买过该订单！"];
        }

        $where = " o.status = 1 and o.type = 2 and o.details_type != 1 and o.user_id = " . $user_id . " and o.order_no = '" . $order_no . "'";
        $result = DB::table("orders as o")
            ->leftJoin("orders_goods as og", "o.order_no", "=", "og.order_no")
            ->leftJoin("goods as g", "g.id", "=", "og.goods_id")
            ->select("o.user_bill", "og.appid", "og.pay_years", "og.details_type", "og.pay_type", "og.goods_id", "g.status", "g.deleted",
                "g.level1 as product_id", "g.level2 as platform_id", "g.level3 as license_type_id")
            ->whereRaw($where)
            ->get();
        $data = obj_to_arr($result);
        if (count($data) == 1) {//一个商品
            $info = current($data);
            if ($info['status'] != 1 || $info['deleted'] == 1) {
                return ["code" => 301, "msg" => $info["goods_id"] . "商品已下架或不存在"];
            }
            $param = ["products_id" => $info['product_id'], "platform_id" => $info['platform_id'], "licensetype_id" => $info["license_type_id"],
                "pay_years" => $info["pay_years"], "details_type" => $info["details_type"], "pay_type" => $info["pay_type"], "user_id" => $user_id,
                "login_user_email" => $login_user_email, "s" => "//api/createorder", "appid" => [$info["appid"]],
                "info"=>unserialize($info["user_bill"])];
            return $this->createorder($param);
        } else {//多个商品
            foreach ($data as $key => $value) {
                if ($value['status'] != 1 || $value['deleted'] == 1) {
                    return ["code" => 301, "msg" => $value["goods_id"] . "商品已下架或不存在"];
                }
            }
        }
    }

    public static $payments = ['paddle' => 1, 'alipay' => 2, 'wxpay' => 3, 'paypal' => 5];

    public function data_list($param)
    {
        $where = "orders.details_type!=3";
        if ($param['info']) {
            $where .= " and {$param['query_type']}='{$param['info']}'";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            $where .= " and orders.status={$param['status']}";
        }
        if ($param['pay_type']) {
            $param['pay_type'] = $param['pay_type'] - 1;
            $where .= " and orders.pay_type={$param['pay_type']}";
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
            return $goods->leftJoin('users', 'orders.user_id', '=', 'users.id')->whereRaw($where)->orderByRaw('orders.id desc')->selectRaw("orders.*,users.email")->get()->toArray();
        } else {
            $data = $goods->leftJoin('users', 'orders.user_id', '=', 'users.id')->whereRaw($where)->orderByRaw('orders.id desc')->selectRaw("orders.*,users.email")->paginate(10);
        }

        return $data;
    }

    public function data_saaslist($param)
    {
        $where = "orders.details_type=3";
        if ($param['info']) {
            $where .= " and {$param['query_type']}='{$param['info']}'";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            //已支付订单包括取消订阅, 关闭订单包括退款
            if($param['status'] == OrderGoods::STATUS_1_PAID){
                $where .= " and orders.status in (1, 5)";
            }elseif ($param['status'] == OrderGoods::STATUS_4_CLOSE){
                $where .= " and orders.status in (4, 6)";
            }else{
                $where .= " and orders.status = {$param['status']}";
            }
        }
        if ($param['pay_type']) {
            $param['pay_type'] = $param['pay_type'] - 1;
            $where .= " and orders.pay_type={$param['pay_type']}";
        }

        if ($param['type']) {
            $where .= " and orders.type={$param['type']}";
        }

        if($param['combo']){
            $where .= " and goods.level1 = {$param['combo']}";
        }

        if($param['gear']){
            $where .= " and goods.level2 = {$param['gear']}";
        }

//        if (isset($param['pay_at']) && $param['pay_at'] && isset($param['endpay_at']) && $param['endpay_at']) {
//            $where .= " AND orders.pay_time BETWEEN '" . $param['pay_at'] . "' AND '" . $param['endpay_at'] . "'";
//        } elseif (isset($param['pay_at']) && $param['pay_at'] && empty($param['endpay_at'])) {
//            $where .= " AND orders.pay_time >= '" . $param['pay_at'] . "'";
//        } elseif (isset($param['endpay_at']) && $param['endpay_at'] && empty($param['pay_at'])) {
//            $where .= " AND orders.pay_time <= '" . $param['endpay_at'] . "'";
//        }

        if(isset($param['created_at']) && $param['created_at']){
            $created_at_arr = explode('/', $param['created_at']);
            $start_data = $created_at_arr[0];
            $end_data = Carbon::parse($created_at_arr[1])->addDay()->format('Y-m-d H:i:s');
            $where .= " AND orders.created_at BETWEEN '" . $start_data . "' AND '" . $end_data . "'";
        }

        if(isset($param['closetime']) && $param['closetime']){
            $close_at_arr = explode('/', $param['closetime']);
            $start_data = $close_at_arr[0];
            $end_data = Carbon::parse($close_at_arr[1])->addDay()->format('Y-m-d H:i:s');
            $where .= " AND orders.closetime BETWEEN '" . $start_data . "' AND '" . $end_data . "'";
        }

        $goods = new Order();
        $classification = $this->assembly_saasclassification();
        if ($param['export'] == 1) {
            return $goods->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->leftJoin('orders_goods', 'orders_goods.order_id', '=', 'orders.id')
                ->leftJoin('goods', 'goods.id', '=', 'orders_goods.goods_id')
                ->whereRaw($where)
                ->orderByRaw('orders.id desc')
                ->selectRaw("orders.*,users.email,goods.level1,goods.level2")
                ->get()
                ->toArray();
        } else {
            $data = $goods
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->leftJoin('orders_goods', 'orders_goods.order_id', '=', 'orders.id')
                ->leftJoin('goods', 'goods.id', '=', 'orders_goods.goods_id')
                ->whereRaw($where)
                ->orderByRaw('orders.id desc')
                ->selectRaw("orders.*,users.email,goods.level1,goods.level2,orders_goods.goods_no,orders_goods.package_type")->paginate(10);
              foreach ($data as $k=>$v){
                  $level1 = $v->level1;
                  $level2 = $v->level2;
                  $v->level1name = array_get($classification, "$level1.title");
                  $v->level2name = array_get($classification, "$level2.title");
              }

        }
        return $data;
    }

    function assembly_saasclassification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("deleted=0 and is_saas=1",'displayorder');
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }


    public function export($list, $field)
    {
        $title_arr = [
            'id' => 'ID',
            'order_no' => '订单编号',
            'email' => '用户账号',
            'pay_type' => '支付方式',
            'price' => '订单金额',
            'status' => '订单状态',
            'type' => '订单来源',
            'details_type' => '订单类型',
            'created_at' => '创建时间',
            'pay_time' => '支付时间',
        ];


        $field = explode(',', $field);

        $header = [];
        foreach ($field as $title) {
            $header[] = array_get($title_arr, $title);
        }
        $rows[] = $header;

        foreach ($list as $data) {
            $row = [];
            foreach ($field as $key) {
                $value = array_get($data, $key);
                if ($key == 'status') {
                    switch ($value) {
                        case 0:
                            $value = "待付款";
                            break;
                        case 1:
                            $value = "已付款";
                            break;
                        case 2:
                            $value = "已完成";
                            break;
                        case 3:
                            $value = "待退款";
                            break;
                        case 4:
                            $value = "已关闭";
                            break;
                    }
                }
                if ($key == 'pay_type') {
                    switch ($value) {
                        case 0:
                            $value = "未支付";
                            break;
                        case 1:
                            $value = "paddle支付";
                            break;
                        case 2:
                            $value = "支付宝";
                            break;
                        case 3:
                            $value = "微信";
                            break;
                        case 4:
                            $value = "不需支付";
                            break;
                        case 5:
                            $value = "paypal";
                            break;
                    }
                }
                if ($key == 'type') {
                    switch ($value) {
                        case 1:
                            $value = "后台创建";
                            break;
                        case 2:
                            $value = "在线购买";
                            break;
                    }
                }
                if ($key == 'details_type') {
                    switch ($value) {
                        case 1:
                            $value = "SDK试用";
                            break;
                        case 2:
                            $value = "SDK订单";
                            break;
                    }
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $userExport = new GoodsExport($rows);
        $fileName = 'SDK订单列表' . time() . '.xlsx';
        return \Excel::download($userExport, $fileName);
    }

    public function exportSaaS($list, $field)
    {
        $title_arr = [
            'id' => 'ID',
            'order_no' => '订单编号',
            'email' => '用户账号',
            'level1name' => '套餐类型',
            'level2name' => '档位（资产数）',
            'price' => '订单金额（$）',
            'pay_type' => '支付方式',
            'type' => '订单来源',
            'status' => '订单状态',
        ];


        $field = explode(',', $field);

        $header = [];
        foreach ($field as $title) {
            $header[] = array_get($title_arr, $title);
        }
        $rows[] = $header;

        $classification = $this->assembly_saasclassification();
        foreach ($list as $data) {
            $row = [];
            foreach ($field as $key) {
                $value = array_get($data, $key);
                if ($key == 'status') {
                    switch ($value) {
                        case 0:
                            $value = "待付款";
                            break;
                        case 1:
                            $value = "已付款";
                            break;
                        case 2:
                            $value = "已完成";
                            break;
                        case 3:
                            $value = "待退款";
                            break;
                        case 4:
                            $value = "已关闭";
                            break;
                    }
                }
                elseif ($key == 'pay_type') {
                    switch ($value) {
                        case 0:
                            $value = "未支付";
                            break;
                        case 1:
                            $value = "paddle支付";
                            break;
                        case 2:
                            $value = "支付宝";
                            break;
                        case 3:
                            $value = "微信";
                            break;
                        case 4:
                            $value = "不需支付";
                            break;
                    }
                }
                elseif ($key == 'type') {
                    switch ($value) {
                        case 1:
                            $value = "后台创建";
                            break;
                        case 2:
                            $value = "在线购买";
                            break;
                    }
                }
                elseif ($key == 'level1name'){
                    $level1 = $data['level1'];
                    $value = array_get($classification, "$level1.title");
                }
                elseif($key == 'level2name'){
                    $level2 = $data['level2'];
                    $value = array_get($classification, "$level2.title");
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $userExport = new GoodsExport($rows);
        $fileName = 'SaaS订单列表' . time() . '.xlsx';
        return \Excel::download($userExport, $fileName);
    }

    public function sum_data($param)
    {
        $where = "details_type!=3";
        if ($param['info']) {
            $where .= " and {$param['query_type']}='{$param['info']}'";
        }
        if ($param['details_type']) {
            $where .= " and orders.details_type={$param['details_type']}";
        }
        if ($param['type']) {
            $where .= " and orders.type={$param['type']}";
        }
        if ($param['pay_type']) {
            $param['pay_type'] = $param['pay_type'] - 1;
            $where .= " and orders.pay_type={$param['pay_type']}";
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
        $arr = [];
        $price = 0;
        $sumcount = 0;
        $sumnostatus = 0;
        $sumyesstatus = 0;
        $sumgbstatus = 0;
        $sumwcstatus = 0;
        foreach ($data as $k => $v) {
            $sumcount++;
            $price += $v['price'];
            switch ($v['status']) {
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
        $arr['price'] = $price;
        $arr['sumcount'] = $sumcount;
        $arr['sumnostatus'] = $sumnostatus;
        $arr['sumyesstatus'] = $sumyesstatus;
        $arr['sumwcstatus'] = $sumwcstatus;
        $arr['sumgbstatus'] = $sumgbstatus;
        return $arr;
    }


    public function sum_saasdata($param)
    {
        $where = "orders.details_type=3";
        if ($param['info']) {
            $where .= " and {$param['query_type']}='{$param['info']}'";
        }
        if ($param['type']) {
            $where .= " and orders.type={$param['type']}";
        }
        if ($param['pay_type']) {
            $param['pay_type'] = $param['pay_type'] - 1;
            $where .= " and orders.pay_type={$param['pay_type']}";
        }

        if($param['combo']){
            $where .= " and goods.level1 = {$param['combo']}";
        }

        if($param['gear']){
            $where .= " and goods.level2 = {$param['gear']}";
        }

        if(isset($param['created_at']) && $param['created_at']){
            $created_at_arr = explode('/', $param['created_at']);
            $start_data = $created_at_arr[0];
            $end_data = Carbon::parse($created_at_arr[1])->addDay()->format('Y-m-d H:i:s');
            $where .= " AND orders.created_at BETWEEN '" . $start_data . "' AND '" . $end_data . "'";
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

        $order = new Order();
        $data = $order->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('orders_goods', 'orders_goods.order_id', '=', 'orders.id')
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->whereRaw($where)
            ->select(['orders.id', 'orders.status', 'orders.price'])
            ->get()->toArray();

        $arr = [];
        $price = 0;
        $sumcount = 0;
        $sumnostatus = 0;
        $sumyesstatus = 0;
        $sumgbstatus = 0;
        $sumwcstatus = 0;
        foreach ($data as $k => $v) {
            $sumcount++;
            $price += $v['price'];
            switch ($v['status']) {
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
        $arr['price'] = $price;
        $arr['sumcount'] = $sumcount;
        $arr['sumnostatus'] = $sumnostatus;
        $arr['sumyesstatus'] = $sumyesstatus;
        $arr['sumwcstatus'] = $sumwcstatus;
        $arr['sumgbstatus'] = $sumgbstatus;
        return $arr;
    }

    public function rundata($param)
    {
        $data = $param['data'];
        $user = new User();
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $lisecosdmode = new LicenseModel();
        $goods = new Goods();
        $order = new Order();
        $orderGoods = new OrderGoods();
        $is_user = $user->existsEmail($data['email']);
        if (!$is_user) {
            $password = User::getRandStr();
            $arr['full_name'] = $data['full_name'];
            $arr['email'] = $data['email'];
            $arr['flag'] = 2;
            $arr['password'] = User::encryptPassword($password);
            $arr['created_at'] = date("Y-m-d H:i:s");
            $arr['updated_at'] = date("Y-m-d H:i:s");
            $user_id = Db::table("users")->insertGetId($arr);
            //自动订阅电子报
            $subsService = new SubscriptionService();
            $subsService->update_status(['email'=>$data['email'], 'subscribed'=>1], false);
            //发送邮件
            $emailModel = Mailmagicboard::getByName('后台新增订单（用户注册成功邮件）');
            $data['title'] = $emailModel->title;
            $data['info'] = $emailModel->info;
            $data['info'] = str_replace("#@username", $arr['full_name'], $data['info']);
            $data['info'] = str_replace("#@mail", $arr['email'], $data['info']);
            $data['info'] = str_replace("#@password", $password, $data['info']);
            $url = env('WEB_HOST') . '/login';
            $url_info = "<a href='$url'>$url</a>";
            $data['info'] = str_replace("#@url", $url_info, $data['info']);
            $data['id'] = $emailModel->id;
            $email->sendDiyContactEmail($data, 0, $arr['email']);
            $emailarr['username']=$arr['full_name'];
            $user_email=$arr['email'];
        } else {
            $users = DB::table('users')->where('email', $data['email'])->first();
            $user_id = $users->id;
            $emailarr['username']=$users->full_name;
            $user_email=$users->email;
        }

        $goods_data = $goods->_where("deleted=0 and status=1");
        $arr = [];
        $sumprice = 0;
        $goodstotal = 0;
        if ($data['status'] == 1) {
            $pay_type = 4;
            $mailedatas = $maile->getFindcategorical(59);
            $sarr=$mailedatas;
        } else {
            $pay_type = 0;
            $mailedatas = $maile->getFindcategorical(60);
            // $mailedatas['title'] = str_replace("(产品名)",$a,$mailedatas['title']);
            $sarr=$mailedatas;
        }
        $classification = $this->assembly_orderclassification();
        $parcudt=[];
        foreach ($data['level1'] as $k => $v) {
            $goodsid=0;
            foreach ($goods_data as $ks => $vs) {
                if ($v == $vs['level1'] && $data['level2'][$k] == $vs['level2'] && $data['level3'][$k] == $vs['level3']) {
                    $goodsid = $vs['id'];
                    $price = $vs['price']*$data['period'][$k];
                }
            }
            $a=$classification[$v]['title'] ." for ". $classification[$data['level2'][$k]]['title'] ." (". $classification[$data['level3'][$k]]['title'].")";
            $parcudt[]=$a;
            
            if (!$goodsid) return ['code' => 500, 'msg' => $classification[$v]['title'] . '-' . $classification[$data['level2'][$k]]['title'] . '-' . $classification[$data['level3'][$k]]['title'] . '下没有商品'];
            $ordergoods_no = chr(rand(65, 90)) .chr(rand(65, 90)) .chr(rand(65, 90)). time();
            $s = $k + 1;

            $appid[]=$data["appid$s"];
            $arr[] = [
                'goods_no' => $ordergoods_no,
                'status' => $data['status'],
                'type' => 1,
                'details_type' => 2,
                'pay_type' => $pay_type,
                'price' => $price,
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
        $orderno = chr(rand(65, 90)) .chr(rand(65, 90)) .chr(rand(65, 90)) .time();
        $orderdata = [
            'pay_type' => $pay_type,
            'order_no' => $orderno,
            'status' => $data['status'],
            'bill_no'=>$this->getBillNo(),
            'type' => 1,
            'details_type' => 2,
            'user_bill' => serialize(['email'=>$user_email]),
            'price' => $sumprice,
            'user_id' => $user_id,
            'goodstotal' => $goodstotal
        ];
        $emailarr['products']=$parcudt[$k];
        $emailarr['order_id']=$orderno;
        $emailarr['pay_time']=CommonService::formatDate(date("Y-m-d H:i:s"));
        $emailarr['goodsprice']="$".$sumprice;
        $emailarr['price']="$".$sumprice;
        if($data['status']==1){
            $emailarr['payprice']="$".$sumprice;
            $emailarr['noorderprice']="$0.00";
        }else{
            $emailarr['payprice']="$0.00";
            $emailarr['noorderprice']="$".$sumprice;
        }
        $html='<table style="margin-top:0px;">';
        $i=1;
        try {
            $order_id = $order->insertGetId($orderdata);
            foreach ($arr as $k => $v) {
                $arr[$k]['order_id'] = $order_id;
                $arr[$k]['order_no'] = $orderno;
                $ordergoods_id = $orderGoods->insertGetId($arr[$k]);
                if ($ordergoods_id) {
                    if($v['pay_years'] > 1){
                        $unity = 'Years';
                    }else{
                        $unity = 'Year';
                    }
                    $products=$parcudt[$k];
                    $html.='<tr><td>&nbsp;- Order Item '.$i.' (ID:'.$v['goods_no'].'）</td>';
                    $html.='<tr><td>&nbsp;&nbsp;&nbsp;'.$products.'</td></tr>';
                    $html.='<tr><td>&nbsp;&nbsp;&nbsp;Purchase Period:'.$v['pay_years'].$unity.'</td>';
                    $i++;
                    if($data['status']==1){
                        $lisecosdata = LicenseService::buildLicenseCodeData($arr[$k]['goods_no'], $arr[$k]['pay_years'], $user_id, $data['level1'][$k], $data['level2'][$k], $data['level3'][$k],  $appid[$k], $data['email'],$order_id,$ordergoods_id);
                        $lisecosdmode->_insert($lisecosdata);
                        $OrderController=new OrderController();
                        $fapiao=$OrderController->get_pdfurl($order_id);
                        $order->_update(['bill_url'=>$fapiao],"id='{$order_id}'");
                        $emailarr['url']=$fapiao;
                    }else{
                        $emailarr['url']= env('WEB_HOST') . '/personal/orders/checkout?order_id=' . $order_id . '&type=1';//跳转到购买页面替换地址
                    }
                    $emailarr['yesprice']="$".$price;
                }
            }
            $html.='</table>';
            $emailarr['products']=$html;
            $email->sendDiyContactEmail($emailarr,9,$user_email,$sarr);
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
            $classification = $this->assembly_orderclassification();
            foreach ($ordergoodsdata as $k => $v) {
                $ordergoodsdata[$k]['products'] = $classification[$v['level1']]['title'];
                $ordergoodsdata[$k]['platform'] = $classification[$v['level2']]['title'];
                $ordergoodsdata[$k]['licensie'] = $classification[$v['level3']]['title'];
            }
        }
        return $ordergoodsdata;
    }

    public function data_saasinfo($id)
    {
        $orderGoods = new OrderGoods();
        $ordergoodsdata = $orderGoods
            ->leftJoin('users', 'orders_goods.user_id', '=', 'users.id')
            ->leftJoin('goods', 'goods.id', '=', 'orders_goods.goods_id')
            ->whereRaw("orders_goods.order_id='{$id}'")
            ->selectRaw("orders_goods.*,users.email,goods.level1,goods.level2")
            ->get()->toArray();
        if (!empty($ordergoodsdata)) {
            $classification = $this->assembly_saasorderclassification();
            foreach ($ordergoodsdata as $k => $v) {
                $ordergoodsdata[$k]['products'] = $classification[$v['level1']]['title'];
                $ordergoodsdata[$k]['platform'] = $classification[$v['level2']]['title'];
            }
        }
        return $ordergoodsdata;
    }

    /**
     * 获取主订单信息
     * @param $id
     * @return \Illuminate\Database\Query\Builder|mixed
     */
    public function getOrderInfo($id)
    {
        return obj_to_arr(DB::table("orders")->find($id));
    }

    public function update_status($id)
    {
        $order = new Order();
        $orderGoods = new OrderGoods();
        try {
            $now = date('Y-m-d H:i:s');
            $order->_update(['status' => 4, 'closetime' => $now], "id='{$id}'");
            $orderGoods->_update(['status' => 4, 'closetime' => $now], "order_id='{$id}'");
        } catch (Exception $e) {
            return ['code' => 500, 'message' => '关闭失败'];
        }
        return ['code' => 0];
    }

    public function get_orderinfo($pram)
    {
        $order = new Order();
        $orderGoods = new OrderGoods();

        $LicenseModel = new LicenseModel();
        $data = $order->_find("user_id='{$pram['user_id']}' and id='{$pram['order_id']}'");

        $data = $order->objToArr($data);
        if (!$data) {
            return ['code' => 403, 'msg' => "订单不存在或不是该用户订单"];
        }

        $data['user_bill'] = $data['user_bill'] ? unserialize($data['user_bill']) : '';
        $ordergoodsdata = $orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->whereRaw("orders_goods.order_id='{$pram['order_id']}'")
            ->selectRaw("orders_goods.appid,orders_goods.goods_no,orders_goods.pay_type,orders_goods.status,orders_goods.price,orders_goods.id,goods.level1,goods.level2,goods.level3,orders_goods.pay_years period")
            ->get()->toArray();
        if (!empty($ordergoodsdata)) {
            $classification = $this->assembly_orderclassification();
            foreach ($ordergoodsdata as $k => $v) {
                $ordergoodsdata[$k]['products'] = isset($classification[$v['level1']]['title']) ? $classification[$v['level1']]['title'] : "";
                $ordergoodsdata[$k]['platform'] = isset($classification[$v['level2']]['title']) ? $classification[$v['level2']]['title'] : "";
                $ordergoodsdata[$k]['licensie'] = isset($classification[$v['level3']]['title']) ? $classification[$v['level3']]['title'] : "";
                $license_code=$LicenseModel->_find("ordergoods_id=".$v['id']);
                $license_code=$LicenseModel->objToArr($license_code);
                $ordergoodsdata[$k]['expire_time']=$license_code['expire_time'];
                $ordergoodsdata[$k]['created_at']=$license_code['created_at'];

                switch ($v['pay_type']) {
                    case 1:
                        $ordergoodsdata[$k]['payname'] = "paddle";
                        break;
                    case 2:
                        $ordergoodsdata[$k]['payname'] = "AliPay";
                        break;
                    case 3:
                        $ordergoodsdata[$k]['payname'] = "WeChat Pay";
                        break;
                    case 4:
                        $ordergoodsdata[$k]['payname'] = "unpaid";
                        break;
                }
            }
        }
        $data['list'] = $ordergoodsdata;
        $data['fapiao']=$data['bill_url'] ?? '';
        return ['code' => 200, 'msg' => 'ok', 'data' => $data];
    }

    /**
     * 获取新的发票编号(需要将方法移到服务层)
     * @return mixed|string
     */
    public function getBillNo()
    {
        $rand_str = "S" . time() . get_rand_str(4);
        $info = Order::where("bill_no", $rand_str)->get();
        if (count($info) > 0) {
            $rand_str = $this->getBillNo();
        }
        return $rand_str;
    }


    public function get_orderlist($parm)
    {
        $order = new Order();
        $orderGoods = new OrderGoods();
        $data = $order->_where("user_id='{$parm['user_id']}' and details_type=2", "id DESC", "id,order_no,status,created_at,price,isrenwe");
        if (!$data) {
            return ['code' => 403, 'msg' => '当前没有订单数据', 'data' => []];
        }
        $ordergoodsdata = $orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->whereRaw("orders_goods.user_id='{$parm['user_id']}' and orders_goods.details_type=2")
            ->selectRaw("goods.level1,goods.level2,goods.level3,orders_goods.order_no as order_id, goods.status as goods_status")
            ->get()->toArray();
        $classification = $this->assembly_orderclassification();
        foreach ($data as $k => $v) {
            $data[$k]['good_status'] = 1;
            foreach ($ordergoodsdata as $ks => $vs) {
                if ($v['order_no'] == $vs['order_id']) {
                        $level1 = $classification[$vs['level1']]['title'] ?? '';
                        $level2 = $classification[$vs['level2']]['title'] ?? '';
                        $level3 = $classification[$vs['level3']]['title'] ?? '';
                        $data[$k]['list'][] = ['product_name'=> $level1 ." for ". $level2 ." (". $level3.")" , 'status'=>$vs['goods_status']];
                        if($vs['goods_status'] == 0){
                            $data[$k]['good_status'] = 0;
                        }
                }
            }
        }

        return ['code' => 200, 'msg' => 'ok', 'data' => $data];
    }

    public function get_ordertryoutlist($parm)
    {
        $orderGoods = new OrderGoods();
        $ordergoodsdata = $orderGoods
            ->leftJoin('goods', 'orders_goods.goods_id', '=', 'goods.id')
            ->whereRaw("orders_goods.user_id='{$parm['user_id']}' and orders_goods.details_type=1")
            ->orderByRaw("orders_goods.order_id desc")
            ->selectRaw("goods.level1,goods.level2,goods.level3,orders_goods.order_no as order_id,orders_goods.goods_id,orders_goods.appid,orders_goods.created_at")
            ->get()->toArray();
        $classification = $this->assembly_orderclassification();
        foreach ($ordergoodsdata as $ks => $vs) {
            $level1 = $classification[$vs['level1']]['title'];
            $level2 = $classification[$vs['level2']]['title'];
            $level3 = $classification[$vs['level3']]['title'];
            $ordergoodsdata[$ks]['goodsname'] = $level1 ." for ". $level2 ." (". $level3.")";
            $ordergoodsdata[$ks]['peroid'] = "1 month";
            $ordergoodsdata[$ks]['platform'] = $level2;
        }
        return ['code' => 200, 'msg' => 'ok', 'data' => $ordergoodsdata];
    }

    public function createorder($data)
    {
        $order = new Order();
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $orderGoods = new OrderGoods();
        $goods = new goods();
        $userobj = new User();
        $userserver = new UserService();
        $lisecosdmode= new LicenseModel();
        $orderno = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) .time();
        $goodsfeilei = $this->assembly_orderclassification();
        $emailarr=[];
        $goods_data = $goods->_find("level1='{$data['products_id']}' and level2='{$data['platform_id']}' and level3='{$data['licensetype_id']}' and deleted=0 and status=1");
        $goods_data = $goods->objToArr($goods_data);
        if (!$goods_data) {
            return ['code' => 403, 'msg' => "该商品不存在或已下架"];
        }
        $user_info =$userobj->_find("id='{$data['user_id']}'");
        $user_info =$userobj->objToArr($user_info);
        $emailarr['username']=$user_info['full_name'];
        $emailarr['products']= $goodsfeilei[$data['products_id']]['title'] ." for ". $goodsfeilei[$data['platform_id']]['title'] ." (". $goodsfeilei[$data['licensetype_id']]['title'].")";
        $ordergoods_no = chr(rand(65, 90)) .chr(rand(65, 90)) .chr(rand(65, 90)) . time();
        $emailarr['orderno']=$orderno;
        $orderarr = [
            'order_no' => $orderno,
            'pay_type' => 0,
            'status' => 0,
            'type' => 2,
            'details_type' => $data['details_type'],
            'user_id' => $data['user_id'],
            'user_bill' => serialize($data['info']),
            'goodstotal' => 1
        ];
        $ordergoodsarr = [
            'goods_no' => $ordergoods_no,
            'pay_type' => 0,
            'order_no' => $orderno,
            'status' => 0,
            'type' => 2,
            'appid' => implode(",", $data['appid']),
            'goods_id' => $goods_data['id'],
            'details_type' => $data['details_type'],
            'user_id' => $data['user_id']
        ];
        if ($data['details_type'] == 1) {
            $mailedatas = $maile->getFindcategorical(34);
            $appid=$data['appid'];
            $data['appid'] = implode(",", $data['appid']);
            $gooodsdata = $orderGoods->_find("user_id='{$data['user_id']}' and appid='{$data['appid']}' and details_type='{$data['details_type']}' and status=2 and goods_id='{$goods_data['id']}'");
            $gooodsdata = $orderGoods->objToArr($gooodsdata);
            if ($gooodsdata) {
                return ['code' => 403, 'msg' => "`该APPID在当前商品已存在试用订单`"];
            }
            $orderarr['status'] = 2;
            $orderarr['pay_type'] = 4;
            $orderarr['price'] = 0.00;
            $ordergoodsarr['status'] = 2;
            $ordergoodsarr['pay_type'] = 4;
            $ordergoodsarr['price'] = 0.00;
            $ordergoodsarr['pay_years'] = 1;
            try {
                $order_id = $order->insertGetId($orderarr);
                $ordergoodsarr['order_id'] = $order_id;
                $ordergoods_id=$orderGoods->insertGetId($ordergoodsarr);
                $licensecodedata=LicenseService::buildLicenseCodeData($ordergoods_no, 1, $data['user_id'], $data['products_id'], $data['platform_id'], $data['licensetype_id'],  $appid, $data['info']['email'],$order_id,$ordergoods_id, 'month');
                $lisecosdmode->_insert($licensecodedata);

                $email->sendDiyContactEmail($emailarr,4,$data['info']['email'],$mailedatas);
                if($user_info['type']==1){
                    $userserver->changeType(2,$data['user_id']);
                }
            } catch (Exception $e) {
                return ['code' => 500, 'message' => '创建失败'];
            }
            return ['code' => 200, 'msg' => "创建试用订单成功", 'data' => ['order_id' => $order_id]];
        } else {
            if (!isset($data['pay_type'])) {
                return ['code' => 403, 'msg' => "请选择支付方式"];
            }
            $mailedatas = $maile->getFindcategorical(40);
            $data['appid'] = implode(",", $data['appid']);
            $price = $data['pay_years'] * $goods_data['price'];
            $orderarr['status'] = 0;
            $orderarr['pay_type'] = $data['pay_type'];
            $orderarr['price'] = $price;
            $ordergoodsarr['status'] = 0;
            $ordergoodsarr['pay_type'] = $data['pay_type'];
            $ordergoodsarr['price'] = $price;
            $ordergoodsarr['pay_years'] = $data['pay_years'];
            try {
                $order_id = $order->insertGetId($orderarr);
                $ordergoodsarr['order_id'] = $order_id;
                $orderGoods->insertGetId($ordergoodsarr);
                $emailarr['order_id']=$orderno;
                $emailarr['pay_years']=$data['pay_years'];
                $emailarr['price']="$".$price; 
                $emailarr['payprice']="$0.00";
                $emailarr['taxes']="$0.00";
                $emailarr['yesprice']="$".$price;
                $emailarr['url']=env('WEB_HOST')."/order/checkout";
                //$email->sendDiyContactEmail($emailarr,6,$data['info']['email'],$mailedatas);
                $orderarr['email'] = $data['info']['email'] ?? '';
                $orderarr['id'] = $order_id ?? 0;
                $pay = $this->comparePriceCloseAndCreateOrder($orderarr);
            } catch (Exception $e) {
                return ['code' => 500, 'message' => '创建失败'];
            }
            return ['code' => 200, 'msg' => "创建订单成功", 'data' => ['order_id' => $order_id, 'pay' => $pay]];
        }
    }

    public function gitinfo($pram)
    {
        $order = new Order();
        $data = $order->_find("id='{$pram['id']}' and user_id='{$pram['user_id']}'");
        $data = $order->objToArr($data);
        if(!$data)return['code'=>403,'msg'=>'没有该订单'];
        $info=unserialize($data['user_bill']);
        return['code'=>200,'msg'=>'ok','data'=>$info];
    }

    public function runrepurchase($pram)
    {
        $order = new Order();
        $orderGoods = new OrderGoods();
        $data = $order->_find("id='{$pram['id']}' and user_id='{$pram['user_id']}'");
        $data = $order->objToArr($data);
        if (!$data) {
            return ['code' => 403, 'msg' => '订单不存在'];
        }
        $goodsdata = $this->assembly_ordergoods();
        $ordergoods = $orderGoods->_where("order_id='{$pram['id']}' and user_id='{$pram['user_id']}'");
        $arr = [];
        $orderno = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) .time();
        $sumprice = $goodstotal = 0;
        foreach ($ordergoods as $k => $v) {
            if (!$goodsdata[$v['goods_id']]) {
                return ['code' => 403, 'msg' => "商品ID：" . $v['goods_id'] . "该商品不存在或已下架"];
            }
            $ordergoods_no = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . time();
            $price = $v['pay_years'] * $goodsdata[$v['goods_id']]['price'];
            $arr[] = [
                'goods_no' => $ordergoods_no,
                'pay_type' => $pram['pay_type'],
                'order_no' => $orderno,
                'status' => 0,
                'type' => 2,
                'details_type' => 2,
                'price' => $price,
                'user_id' => $v['user_id'],
                'appid' => $v["appid"],
                'goods_id' => $v['goods_id'],
                'pay_years' => $v['pay_years'],
                'renwe_goodsid'=>$v['id'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];
            $goodstotal++;
            $sumprice += $price;
        }
        $orderdata = [
            'order_no' => $orderno,
            'pay_type' => $pram['pay_type'],
            'status' => 0,
            'type' => 2,
            'details_type' => 2,
            'price' => $sumprice,
            'user_id' => $data['user_id'],
            'user_bill' => serialize($pram['info']),
            'goodstotal' => $goodstotal,
            'renwe_id'=>$pram['id']
        ];
        try {
            $order_id = $order->insertGetId($orderdata);
            foreach ($arr as $k => $v) {
                $arr[$k]['order_id'] = $order_id;
                $arr[$k]['order_no'] = $orderno;
            }
            $orderGoods->_insert($arr);
            $order->_update(['isrenwe'=>1],"id='{$pram['id']}'");
            $orderdata['email'] = $pram['info']['email'] ?? '';
            $orderdata['id'] = $order_id;
            $pay = $this->comparePriceCloseAndCreateOrder($orderdata);
        } catch (Exception $e) {
            return ['code' => 500, 'message' => '创建失败'];
        }
        return ['code' => 200, 'msg' => "创建续订订单成功", 'data' => ['order_id' => $order_id, 'pay' => $pay]];
    }

    public function noorderpay($data)
    {
        $orderGoods = new OrderGoods();
        if (!isset($data['pay_type'])) {
            return ['code' => 403, 'msg' => "请选择支付方式"];
        }

        $order_model = Order::where('id', $data['order_id'])->where('user_id', $data['user_id'])->where('status', 0)->first();
        if(!$order_model){
            return ['code' => 403, 'msg' => "该订单不存在或已关闭"];
        }
        //更新订单的账单信息
        $order_model->user_bill = serialize($data['user_bill']);
        $order_model->save();

        $order_data = $order_model->toArray();
        $emaildata = $data['user_bill'];
        $order_goodsdata = $orderGoods->_where("order_id='{$order_data['id']}' and order_no='{$order_data['order_no']}' and status=0");
        if (!$order_goodsdata) return ['code' => 403, 'msg' => "该订单商品明细单不存在或已关闭"];
        $goodadata = $this->assembly_ordergoods();
        foreach ($order_goodsdata as $k => $v) {
            if (!isset($goodadata[$v['goods_id']])) return ['code' => 403, 'msg' => "商品id" . $v['goods_id'] . "已下架"];
        }
        $order_data['pay_type'] = $data['pay_type'];
        $order_data['email'] = $emaildata['email'];
        $pay = $this->comparePriceCloseAndCreateOrder($order_data);
        return ['code' => 200, 'msg' => "创建订单成功", 'data' => ['order_id' => $order_data['id'], 'pay' => $pay]];

    }

    public function get_license($parm)
    {
        if (isset($parm['type'])) {
            $wehere = "license_code.user_id='{$parm['user_id']}' and license_code.type=1";
        } else {
            $wehere = "license_code.user_id='{$parm['user_id']}' and license_code.type=2";
        }
        $orderGoods = new LicenseModel();
        $ordergoodsdata = $orderGoods
            ->leftJoin("orders_goods as g","license_code.ordergoods_id",'=','g.id')
            ->orderByRaw("license_code.order_id desc")
            ->whereRaw($wehere)
            ->selectRaw("license_code.*,g.order_no")
            ->get()->toArray();
        if (!$ordergoodsdata) {
            return ['code' => 403, 'msg' => '没有数据', 'data' => []];
        }
        $classification = $this->assembly_orderclassification();
        $arr=array();
        foreach ($ordergoodsdata as $ks => $vs) {
            $arr[$vs['ordergoods_id']]=$vs;
            $level1 = $classification[$vs['products_id']]['title'];
            $level2 = $classification[$vs['platform_id']]['title'];
            $level3 = $classification[$vs['licensetype_id']]['title'];
            $arr[$vs['ordergoods_id']]['platform'] = $level2;
            $arr[$vs['ordergoods_id']]['goodsname'] = $level1 ." for ". $level2 ." (". $level3.")";
            $arr[$vs['ordergoods_id']]['data'][]=[
                'name'=>$vs['platform_name'],
                'key'=>$vs['license_key'],
                'license_secret'=>$vs['license_secret'],
            ];
            foreach ($ordergoodsdata as $kk =>$v){
                  if($v['products_id']==$vs['products_id'] && $v['platform_id']==$vs['platform_id'] && $v['licensetype_id']==$vs['licensetype_id'] && $v['ordergoods_id']==$vs['ordergoods_id'] && $v['platform_name']!=$vs['platform_name']){
                      $arr[$vs['ordergoods_id']]['data'][]=[
                          'name'=>$v['platform_name'],
                          'key'=>$v['license_key'],
                          'license_secret'=>$v['license_secret'],
                      ];
                  }
            }
        }
        $arr=array_values($arr);
        return ['code' => 200, 'msg' => 'ok', 'data' => $arr];
    }

    public function findThirdOrderNotifyHandle($trade_no)
    {
        $order = new Order();
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $data = $order->_find("merchant_no='{$trade_no}'");
        $data = $order->objToArr($data);
        if (empty($data)) {
            return [];
        }
        if ($data['pay_type'] == 2 && !($data['status']==1||$data['status']==2)) {
            self::AlipayNotifyService($data['merchant_no']);
        }

        //paypal支付 未付款订单主动查询状态
        if($data['pay_type'] == 5 && $data['status'] == 0){
            $paypal = new PaypalBiz();
            $result = $paypal->findByPaymentId($data['third_trade_no']);
            //支付完成
            \Log::info('paypal主动查询订单状态数据', ['order_no'=>$data['order_no'], 'status'=>$data['status'], 'result'=>$result->toArray()]);
            if($result->getState() == 'approved' && $data['status'] == Order::STATUS_0_UNPAID){
                $order_service = new OrdersService();
                $order_service->notifyHandle($data['order_no'], $trade_no);
            }
        }

        $data = $order->_find("merchant_no='{$trade_no}'");
        $data = $order->objToArr($data);
        $emaildata = unserialize($data['user_bill']);
        $goodsfeilei = $this->assembly_orderclassification();
        //组装邮件内容发送邮件
        if($data['status']==1||$data['status']==2){
            if($data['renwe_id']){
                $mailedatas = $maile->getFindcategorical(63);
                $mailedatas['title'] = str_replace("+订单号",$data['order_no'],$mailedatas['title']);
            }else{
                $mailedatas = $maile->getFindcategorical(39);
            }
            $goods_data=DB::table("orders_goods as o")
                ->leftJoin("goods as g","o.goods_id",'=','g.id')
                ->whereRaw("o.order_id='{$data['id']}'")
                ->selectRaw("o.*,g.level1,g.level2,g.level3,g.price as goodsprice")
                ->get()
                ->toArray();
        $html='<table style="margin-top:0px;">';
        $emailarr['order_id'] = $data['order_no'];
        $emailarr['goodsprice'] = "$" . $data['price'];
        $emailarr['taxes']="$".$data['tax'];
        $sumprice=floatval($data['price'])+floatval($data['tax']);
        $emailarr['price']="$" .$sumprice;
        $emailarr['payprice']="$" . $sumprice;
        $emailarr['noorderprice'] = "$0.00";
        $emailarr['pay_time'] = CommonService::formatDate($data['pay_time']);
        $emailarr['url']=env('WEB_HOST')."/order/checkout";
        $emailarr['fapiao']=$data['bill_url'];
        $i=1;
        foreach ($goods_data as $value){
            $value = collect($value)->toArray();
            $prrducts=$goodsfeilei[$value['level1']]['title'] ." for ". $goodsfeilei[$value['level2']]['title'] ." (". $goodsfeilei[$value['level3']]['title'].")";
            if($value['pay_years'] > 1){
                $unity = 'Years';
            }else{
                $unity = 'Year';
            }
           $html.='<tr><td>&nbsp;- Order Item '.$i.' (ID:'.$value['goods_no'].'）</td>';
           $html.='<tr><td>&nbsp;&nbsp;&nbsp;'.$prrducts.'</td></tr>';
           $html.='<tr><td>&nbsp;&nbsp;&nbsp;Purchase Period:'.$value['pay_years'].$unity.'</td>';
           $i++;
        }
        $html.='</table>';
        $emailarr['products'] = $html;
        $email->sendDiyContactEmail($emailarr,7,$emaildata['email'],$mailedatas);
        }
        return $data;
    }

    public function wechatnot($xml){
        try {
            $result = WxPayNotifyResults::Init(new WxPayConfig(), $xml);
            $result = array_values(ObjectHelper::convertObjectToArray($result))[0];
            Db::table("callback_log")->insert(['info' => 'wxtext='. json_encode($result), 'pay_type' => 3]);
            if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
                Db::table("callback_log")->insert(['info' => 'orderno=' . $result['out_trade_no'] . json_encode($result), 'pay_type' => 3]);
                $order = new Order();
                $order_goods = new OrderGoods();
                $order->_update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s")], "merchant_no='{$result['out_trade_no']}'");
                $order_goods->_update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s")], "merchant_no='{$result['out_trade_no']}'");
            }
        } catch (WxPayException $e) {
            return json_encode(['code' => 200, 'message' => '失败']);
        }
    }

    public function getgoodsprice($data)
    {
        $Goods = new Goods();
        if (!isset($data['years'])) return ['code' => 403, 'msg' => '缺少购买年限'];
        $datas = $Goods->_find("level1='{$data['products_id']}' and level2='{$data['platform_id']}'and level3='{$data['licenseType_id']}' and deleted=0");
        $datas = $Goods->objToArr($datas);
        $price = round($datas['price'] * $data['years'], 2);
        return ['code' => 200, 'msg' => 'ok', 'price' => $price];
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

    function assembly_ordergoods()
    {
        $Goods = new Goods();
        $data = $Goods->_where("status=1 and deleted=0");
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }

    function assembly_orderclassification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("is_saas=0");
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }

    function assembly_saasorderclassification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("is_saas=1");
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }


    public function comparePriceCloseAndCreateOrder($order)
    {
        $ordernew = new Order();
        $ordergoods = new OrderGoods();
        if (empty($order['page_pay_url'])) {
            $pay_url_data = $this->generatePayUrl($order['pay_type'], 'ComPDFKit', $order['order_no'], $order['price'],$order['email'],$order['id']);
            if ($order['pay_type'] == 2) {
                $pay_url_data['id'] = 'ali' . $order['order_no'];
            }elseif ($order['pay_type'] == 1){
                $pay_url_data['id'] = 'paddle' . $order['order_no'];
            }elseif ($order['pay_type'] == 5){
                $payment_id = $pay_url_data['id'];
                $pay_url_data['id'] = 'paypal' . $order['order_no'];
            }
            $newOrderData = [
                'third_order_no' => $pay_url_data['id'] ?? '',
                'page_pay_url' => $pay_url_data['url'],
                'payment_id' => $payment_id ?? null
            ];
            $bill_no = $this->getBillNo();
            $ordernew->_update(['merchant_no' => $pay_url_data['id']?? '', 'third_trade_no' => $payment_id ?? null, 'pay_url'=>$pay_url_data['url'],'bill_no'=>$bill_no,'pay_type'=>$order['pay_type']], "order_no='{$order['order_no']}'");
            $ordergoods->_update(['merchant_no' => $pay_url_data['id']?? '', 'third_trade_no' => $payment_id ?? null,'pay_type'=>$order['pay_type']], "order_no='{$order['order_no']}'");
        }
        return $newOrderData;
    }


    public function generatePayUrl($payment, $product, $trade_no, $price,$email,$order_id=0)
    {
        $call_back = $this->headerurl();
        $pay_url_data = [];
        #20230110 支付白名单添加 方便测试线上支付测试
        if (in_array($email, config("constants.white_list"))) {
            $orders = Order::where("order_no", $trade_no)->first()->toArray();
            $user_id = $orders['user_id'];
            if (in_array($user_id, config("constants.white_users_list"))) {
                $price = 1;
            }
        }
        if ($payment == self::$payments['paddle']) {
            $paddle = new PaddleBiz();
            $pay_url_data = $paddle->createPayLink($trade_no, $product, $price,1,$email,$order_id);
        } elseif ($payment == self::$payments['alipay']) {
            $pay_redirect_path = '/resubscribe/payed';
            $return_url = $call_back . $pay_redirect_path;
            $pay_url_data = $this->getAliPayUrl($trade_no, $product, $price, $call_back, $return_url);
        } elseif ($payment == self::$payments['wxpay']) {
            $pay_url_data = WechatPay::wechatPay($trade_no, $product, $price, $call_back);
        } elseif($payment == self::$payments['paypal']) {
            $paypal = new PaypalBiz();
            $pay_url_data = $paypal->pay($product, $price, $trade_no);
        }

        return $pay_url_data;
    }

    public function getAliPayUrl($trade_no, $name, $price, $call_back, $return_url)
    {
        $paramss = [
            'out_trade_no' => 'ali' . $trade_no,
            'subject' => $name,
            'payment_type' => 1,//支付类型 只取值为1(商品购买) 固定值
            'total_fee' => $price,
            'body' => $name
        ];
        $obj = new AlipayBiz($paramss);

        // 支付宝验证KEY统一调整为扫码支付
        return $obj->pay($call_back . '/api/orders/alipayNotify', $return_url);
    }

    public static function AlipayNotifyService($trade_no)
    {
        $alipay = new AlipayBiz();
        $goods = new Goods();
        $ordergoods = new OrderGoods();
        $order = new Order();
        $userserver = new UserService();
        $lisecosdmode = new LicenseModel();
        $order_data = $alipay->findAlipayByOrderNo($trade_no);
        $orderdata = $order->_find("merchant_no='{$trade_no}'");
        $orderdata = $order->objToArr($orderdata);
        $emaildata = unserialize($orderdata['user_bill']);
        $ordergoods_data = $ordergoods->_where("merchant_no='{$trade_no}'");
        $goods_data = $goods->_where("1=1");

            if (!empty($order_data) && $order_data['trade_status'] == 'TRADE_SUCCESS') {
                Db::table("callback_log")->insert(['info' => 'orderno=' . $trade_no . json_encode($order_data), 'pay_type' => 2]);
                $order_goods = new OrderGoods();
                $OrderController=new OrderController();
                $fapiao=$OrderController->get_pdfurl($orderdata['id']);
                $order->_update(['bill_url'=>$fapiao],"id='{$orderdata['id']}'");
                $userserver->changeType(4,$orderdata['user_id']);
                if($orderdata['renwe_id']){   //续订订单
                    $ids=[];
                    $lisedata=$lisecosdmode->_where("order_id=".$orderdata['renwe_id']); //查出续订的父订单所有序列码
                    foreach ($ordergoods_data as $k => $v) {            //循环当前子订单
                        foreach ($lisedata as $ks => $vs) {             //循环嵌套续订父订单的所有序列码
                            if ($v['renwe_goodsid'] == $vs['ordergoods_id']) {   //判断当前子订单的父级明细订单与序列码绑定的子订单ID一致
                                if(in_array($v['renwe_goodsid'],$ids))continue; //判断当前子订单ID已添加过序列码则跳过循环
                                array_push($ids,$vs['ordergoods_id']);//把当前添加授权码的子订单ID添加到数组内，避免重复添加多条授权码

                                $licensecodedata = LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['products_id'], $vs['platform_id'], $vs['licensetype_id'], explode(",", $v['appid']), $emaildata['email'], $v['order_id'], $v['id'],'year',$vs['created_at']);
                                $lisecosdmode->_insert($licensecodedata);
                            }
                        }
                    }
                }else{
                    foreach ($ordergoods_data as $k=>$v){
                        foreach ($goods_data as $ks=>$vs){
                            if($v['goods_id']==$vs['id']){
                                $licensecodedata=LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['level1'], $vs['level2'], $vs['level3'],  explode(",",$v['appid']), $emaildata['email'],$v['order_id'],$v['id']);
                                $lisecosdmode->_insert($licensecodedata);
                            }
                        }
                    }
                }
                $order->_update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s")], "merchant_no='{$trade_no}'");
                $order_goods->_update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s")], "merchant_no='{$trade_no}'");
            } else {
                Db::table("callback_log")->insert(['info' => 'orderno=' . $trade_no . json_encode($order_data), 'pay_type' => 2]);
            }

    }

    public function get_invoice($order_id){
        if(!$order_id)return false;
        $order = new Order();
        $data=$order->_find("id='{$order_id}'");
        $data=$order->objToArr($data);
        if(!$data)return ['code'=>0,'msg'=>'没有订单数据'];
        $arr['orderdata']=$data;
        $goodsdata=DB::table("orders_goods as o")
            ->leftJoin("goods as g","o.goods_id",'=','g.id')
            ->whereRaw("o.order_id='{$order_id}'")
            ->selectRaw("o.*,g.level1,g.level2,g.level3")
            ->get()
            ->toArray();
        $goodsdata=json_decode(json_encode($goodsdata), true);
        $goodsfeilei = $this->assembly_orderclassification();
        foreach ($goodsdata as $k=>$v){
            $level1 = $goodsfeilei[$v['level1']]['title'];
            $level2 = $goodsfeilei[$v['level2']]['title'];
            $level3 = $goodsfeilei[$v['level3']]['title'];
            $goodsdata[$k]['goodsname'] = $level1 ." for ". $level2 ." (". $level3.")";
        }
        $arr['ordergoodsdata']=$goodsdata;

        return $arr;
    }


   public function headerurl()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type . $_SERVER['HTTP_HOST'];
    }

    /**
     * 支付成功生成序列码
     * @param $order_no
     * @param $third_trade_no
     * @return bool
     */
    public function notifyHandle($order_no, $third_trade_no){
        $order_controller = new OrderController();
        $user_service = new UserService();
        $license_model = new LicenseModel();

        $order = Order::where('order_no', $order_no)->first();
        //已完成订单不进行回调操作
        if($order['status'] == Order::STATUS_1_PAYED){
            return false;
        }

        $order_data = $order->toArray();
        $email_data = unserialize($order_data['user_bill']);
        $order_goods_data = OrderGoods::where('order_no', $order_no)->get()->toArray();
        $goods_data = Goods::all()->keyBy('id')->toArray();

        try {
            $invoice_url = $order_controller->get_pdfurl($order_data['id']);
            $user_service->changeType(User::TYPE_4_SDK, $order_data['user_id']);
            Order::orderComplete($third_trade_no, $order_no, $invoice_url);
            OrderGoods::orderComplete($order_no, $third_trade_no);
            \Log::info($order_no . ":进入回调执行生成授权码");
            if ($order_data['renwe_id']) {   //续订订单
                $ids = [];
                $license_data = LicenseModel::where('order_id', $order_data['renwe_id'])->get()->toArray(); //查出续订的父订单所有序列码
                foreach ($order_goods_data as $k => $v) {            //循环当前子订单
                    foreach ($license_data as $ks => $vs) {             //循环嵌套续订父订单的所有序列码
                        if ($v['renwe_goodsid'] == $vs['ordergoods_id']) {   //判断当前子订单的父级明细订单与序列码绑定的子订单ID一致
                            if (in_array($v['renwe_goodsid'], $ids)) continue; //判断当前子订单ID已添加过序列码则跳过循环
                            array_push($ids, $vs['ordergoods_id']);//把当前添加授权码的子订单ID添加到数组内，避免重复添加多条授权码

                            $license_data_new = LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['products_id'], $vs['platform_id'], $vs['licensetype_id'], explode(",", $v['appid']), $email_data['email'], $v['order_id'], $v['id'], 'year', $vs['created_at']);
                            \Log::info($order_no . ":续订订单进入回调执行生成授权码" . json_encode($license_data_new));
                            $license_model->_insert($license_data_new);
                        }
                    }
                }
            } else {
                //正常购买订单
                foreach ($order_goods_data as $k => $v) {
                    foreach ($goods_data as $ks => $vs) {
                        if ($v['goods_id'] == $vs['id']) {
                            $license_data_new = LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['level1'], $vs['level2'], $vs['level3'], explode(",", $v['appid']), $email_data['email'], $v['order_id'], $v['id']);
                            \Log::info($order_no . ":进入回调执行生成授权码" . json_encode($license_data_new));
                            $license_model->_insert($license_data_new);
                        }
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            \Log::info('支付成功生成序列码失败#', ['order_no'=>$order_no, 'merchant_no'=>$third_trade_no, 'message'=>$e->getMessage()]);
            return false;
        }
    }
}