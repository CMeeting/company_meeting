<?php


namespace App\Http\Controllers\Api;

use App\Services\OrdersService;
use App\Services\UserService;
use Illuminate\Http\Request;


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
           if (isset($param['out_trade_no'])){
                // 已支持支付宝NotifyHandle，其他付款方式目前是查询Order数据
                $order_data = $order->findThirdOrderNotifyHandle($param['out_trade_no']);
            }
            if (empty($order_data)) {
                return \Response::json(['code'=>403,'mgs'=>"invalid_order_no"]);
            }
        return \Response::json(['code'=>200,'mgs'=>"ok",'data'=>$order_data]);
       // }
//        //第三方调用返回
//        if (isset($_GET['success'], $_GET['paymentId'], $_GET['PayerID']) && $_GET['success'] == 'true') {
//            $paymentId = $_GET['paymentId'];
//            $apiContext = PaypalBiz::paypal();
//
//            $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
//            $execution = new PaymentExecution();
//            $execution->setPayerId($_GET['PayerID']);
//            try { // Execute the payment
//                $result = $payment->execute($execution, $apiContext);
//                $str = serialize($result->toArray());
//                Order::update(['result' => $str], ['third_order_no' => $_GET['paymentId']]);
//                try {
//                    \PayPal\Api\Payment::get($paymentId, $apiContext);
//                } catch (\Exception $ex) {
//                    LogHelper::logSubs($paymentId.' Failed getPaypal '.LogHelper::getErrMessage($ex), LogHelper::LEVEL_WARN);
//                    $this->failure('Pay',$ex->getMessage(),200);
//                }
//            } catch (\Exception $ex) {
//                LogHelper::logSubs($paymentId.' Error createPaypal '.LogHelper::getErrMessage($ex), LogHelper::LEVEL_ERROR);
//                LogHelper::logSubs($paymentId.' createPaypal result: '.$result, LogHelper::LEVEL_ERROR);
//                $this->failure('Pay',$ex->getMessage(),500);
//            }
//            //支付完成重定向到前端页面
//            sleep(5);
//            $order = Order::findByOrdersWhere('o.status = '.Order::$statuses['completed'].' and o.third_order_no = \'' . $_GET['paymentId'] . '\'');
//            if(empty($order)){
//                sleep(5);
//            }
//            $app_id = Order::value('app_id', ['third_order_no' => $_GET['paymentId']]);
//            $str = strpos(App::value('code', ['id' => $app_id]), 'converter') !== false ? '/filmageconverter' : '';
//            $lang = '';
//            if (isset($_GET['lang'])) $lang = '/' . $_GET['lang'];
//            $this->redirect(SysHelper::getEnv('web_front_host') . $lang . $str . SysHelper::getEnv('pay_redirect_path') . '?paymentId=' . $_GET['paymentId']);
//        }
//        elseif (isset($_GET['success']) && $_GET['success'] === false) {
//            $this->failure('Pay',"用户取消支付",200);
//        } else {
//            $this->redirect(SysHelper::getEnv('web_front_host'));
//        }

    }
}