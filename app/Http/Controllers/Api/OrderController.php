<?php


namespace App\Http\Controllers\Api;

use App\Models\Goods;
use App\Models\LicenseModel;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\LicenseService;
use App\Services\OrdersService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class OrderController
{

    public function getorderinfo(Request $request){
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $order->get_orderinfo($param);
        return \Response::json($data);
    }

    public function getorderlist(Request $request){
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $order->get_orderlist($param);
        return \Response::json($data);
    }


    public function getlicense(Request $request){
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $order->get_license($param);
        return \Response::json($data);
    }

    public function getordertryoutlist(Request $request){
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $order->get_ordertryoutlist($param);
        return \Response::json($data);
    }


    /**
     * 创建订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createorder(Request $request)
    {
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        Log::info("用户ID：[" . $user_id . "]创建订单请求参数：" . json_encode($param,JSON_UNESCAPED_UNICODE));
        $data = $order->createorder($param);
        return \Response::json($data);
    }

    /**
     * renew直接生成支付链接（弃用）
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newOrder(Request $request)
    {
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $login_user_email = $request->input("login_user_email", "");
        $order_no = $request->input("order_no", '');//父级订单id
        Log::info("用户ID：[" . $user_id . "]重新创建订单,原订单号[" . $order_no . "]");
        $result = $order->checkAndCreate($user_id, $order_no, $login_user_email);//判断用户是否存在此订单，并判断订单对应的商品是否下架
        if ($result['code'] != 200) {
            Log::info("用户ID：[" . $user_id . "]重新创建订单失败,原订单号[" . $order_no . "]失败原因：" . json_encode($result, JSON_UNESCAPED_UNICODE));
            return \Response::json($result);
        }
        return \Response::json($result);
    }

    public function getgoodsprice(Request $request){
        $order = new OrdersService();
        $param = $request->all();
        $data = $order->getgoodsprice($param);
        return \Response::json($data);
    }

    public function noorderpay(Request $request){
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $order->noorderpay($param);
        return \Response::json($data);
    }

    public function notify(Request $request)
    {
        $param = $request->all();
        $order = new OrdersService();
        // 前端调用返回
        if(!isset($_GET['success'])){
           if (isset($param['out_trade_no'])){
                // 已支持支付宝NotifyHandle，其他付款方式目前是查询Order数据
                $order_data = $order->findThirdOrderNotifyHandle($param['out_trade_no']);
            }
            if (empty($order_data)) {
                return \Response::json(['code'=>403,'mgs'=>"invalid_order_no"]);
            }
        return \Response::json(['code'=>200,'mgs'=>"ok",'data'=>$order_data]);
        }
    }

    public function wechatNotify(Request $request)
    {
        $order = new OrdersService();
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        //Db::table("callback_log")->insert(['info' => 'wxtext='. json_encode($xml), 'pay_type' => 3]);
        $order->wechatnot($xml);

    }


    public function repurchase(Request $request)
    {
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        if (!isset($param['id']) || !isset($param['pay_type']) || !isset($param['info'])) {
            return \Response::json(['code' => 403, 'mgs' => "缺少必要参数"]);
        }
        $param['user_id'] = $user_id;
        $order = new OrdersService();
        $rest = $order->runrepurchase($param);
        return \Response::json($rest);
    }

    public function paddlecallback(Request $request)
    {
        $goods = new Goods();
        $userserver = new UserService();
        $ordergoods = new OrderGoods();
        $order = new Order();
        $lisecosdmode = new LicenseModel();
        $param = $request->all();
        Db::table("callback_log")->insert(['info' => 'paddle=' . json_encode($param), 'pay_type' => 1]);
        if (isset($param['alert_name']) && $param['alert_name'] == "payment_succeeded" && isset($param['passthrough'])) {
            $merchant_no = 'paddle' . $param['passthrough'];
            $orderdata = $order->_find("merchant_no='{$merchant_no}'");
            $orderdata = $order->objToArr($orderdata);
            $emaildata = unserialize($orderdata['user_bill']);
            $ordergoods_data = $ordergoods->_where("merchant_no='{$merchant_no}'");
            $goods_data = $goods->_where("1=1");
            try {
                $fapiao_url = $this->get_pdfurl($orderdata['id']);
                $orders_service = new OrdersService();
                $bill_no = $orders_service->getBillNo();//发票编号,需要移到服务层
                $userserver->changeType(4, $orderdata['user_id']);
                DB::table("orders")->whereRaw("order_no='{$param['passthrough']}'")->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'bill_no' => $bill_no, 'bill_url' => $fapiao_url, 'paddle_no' => $param['order_id']]);
                DB::table("orders_goods")->whereRaw("order_no='{$param['passthrough']}'")->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'paddle_no' => $param['order_id']]);
                \Log::info($param['passthrough'] . ":进入回调执行生成授权码");
                foreach ($ordergoods_data as $k => $v) {
                    foreach ($goods_data as $ks => $vs) {
                        if ($v['goods_id'] == $vs['id']) {
                            $licensecodedata = LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['level1'], $vs['level2'], $vs['level3'], explode(",", $v['appid']), $emaildata['email'], $v['order_id'], $v['id']);
                            \Log::info($param['passthrough'] . ":进入回调执行生成授权码" . json_encode($licensecodedata));
                            $lisecosdmode->_insert($licensecodedata);
                        }
                    }
                }
                return \Response::json(['code'=>200,'msg'=>"接收成功"]);
            } catch (\Exception $e) {
                error('paddle', $e->getMessage(), 200);
            }
        } else {
            return \Response::json(['code' => 0, 'mgs' => "缺少参数"]);
        }
    }

    public function get_pdfurl($order_id){
        if(!$order_id)return '';
        $GoodsService=new OrdersService;
        $arr = $GoodsService->get_invoice($order_id);
        $times=time();
        if (!file_exists(public_path().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR)) mkdir(public_path().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR, 0777);
        $save=public_path().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR.$times.'.pdf';
        $host=$GoodsService->headerurl();
        $url=$host . '/pdf/' . $times.'.pdf';
        PDF::loadView('pdf.document', ['data'=>$arr], [], [
            'format' => 'A5-L'
        ])->save($save);
        return $url;
    }

}