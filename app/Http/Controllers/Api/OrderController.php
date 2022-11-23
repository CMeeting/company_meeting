<?php


namespace App\Http\Controllers\Api;

use App\Services\OrdersService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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


    public function createorder(Request $request){
        $order = new OrdersService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $order->createorder($param);
        return \Response::json($data);
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

    public function paddlecallback(Request $request){
        $param = $request->all();
        Db::table("callback_log")->insert(['info' => 'paddle='. json_encode($param), 'pay_type' => 1]);
        if(isset($param['alert_name'])&&$param['alert_name']=="payment_succeeded"){
            $order = new OrdersService();
            $order->updateorderstatus($param['passthrough']);
        }else{
            return \Response::json(['code'=>0,'mgs'=>"缺少参数"]);
        }

    }

}