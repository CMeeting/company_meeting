<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\biz\PaypalBiz;
use App\Models\Goods;
use App\Models\LicenseModel;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\EmailService;
use App\Services\LicenseService;
use App\Services\MailmagicboardService;
use App\Services\OrdersService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function rewinfo(Request $request){
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        if (!isset($param['id'])) {
            return \Response::json(['code' => 403, 'mgs' => "缺少必要参数"]);
        }
        $param['user_id'] = $user_id;
        $order = new OrdersService();
        $rest = $order->gitinfo($param);
        return \Response::json($rest);
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
                $userserver->changeType(4, $orderdata['user_id']);
                DB::table("orders")->whereRaw("order_no='{$param['passthrough']}'")->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'bill_url' => $fapiao_url, 'paddle_no' => $param['order_id'],'tax'=>$param['payment_tax']]);
                DB::table("orders_goods")->whereRaw("order_no='{$param['passthrough']}'")->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'paddle_no' => $param['order_id']]);
                \Log::info($param['passthrough'] . ":进入回调执行生成授权码");
                if($orderdata['renwe_id']){   //续订订单
                    $ids=[];
                    $lisedata=$lisecosdmode->_where("order_id=".$orderdata['renwe_id']); //查出续订的父订单所有序列码
                    foreach ($ordergoods_data as $k => $v) {            //循环当前子订单
                        foreach ($lisedata as $ks => $vs) {             //循环嵌套续订父订单的所有序列码
                            if ($v['renwe_goodsid'] == $vs['ordergoods_id']) {   //判断当前子订单的父级明细订单与序列码绑定的子订单ID一致
                                if(in_array($v['renwe_goodsid'],$ids))continue; //判断当前子订单ID已添加过序列码则跳过循环
                                array_push($ids,$vs['ordergoods_id']);//把当前添加授权码的子订单ID添加到数组内，避免重复添加多条授权码

                                $licensecodedata = LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['products_id'], $vs['platform_id'], $vs['licensetype_id'], explode(",", $v['appid']), $emaildata['email'], $v['order_id'], $v['id'],'year',$vs['created_at']);
                                \Log::info($param['passthrough'] . ":续订订单进入回调执行生成授权码" . json_encode($licensecodedata));
                                $lisecosdmode->_insert($licensecodedata);
                            }
                        }
                    }
                }else{                        //正常购买订单
                    foreach ($ordergoods_data as $k => $v) {
                        foreach ($goods_data as $ks => $vs) {
                            if ($v['goods_id'] == $vs['id']) {
                                $licensecodedata = LicenseService::buildLicenseCodeData($v['goods_no'], $v['pay_years'], $v['user_id'], $vs['level1'], $vs['level2'], $vs['level3'], explode(",", $v['appid']), $emaildata['email'], $v['order_id'], $v['id']);
                                \Log::info($param['passthrough'] . ":进入回调执行生成授权码" . json_encode($licensecodedata));
                                $lisecosdmode->_insert($licensecodedata);
                            }
                        }
                    }
                }
                return \Response::json(['code'=>200,'msg'=>"接收成功"]);
            } catch (\Exception $e) {
                return \Response::json(['code' => 0, 'mgs' => $e->getMessage()]);
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

    /**
     * 关闭paddle支付弹窗，发送支付失败邮件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPaymentFailedEmail(Request $request){
        //防止Paddle支付成功，还没有回调我们的接口导致我们查询订单状态为未支付
        sleep(3);

        $order_id = $request->input('order_id');
        $user = UserService::getCurrentUser($request);
        $mail_template = new MailmagicboardService();
        $order_service = new OrdersService();
        $mail_service = new EmailService();

        $goods_class = $order_service->assembly_orderclassification();
        $email_info = $mail_template->getFindcategorical(40);

        $order = Order::find($order_id);
        if(!$order || $order->status != 0){
            return \Response::json(['code'=>200, 'message'=>'订单不存在或订单已支付']);
        }

        $bill_info = unserialize($order->user_bill);
        $email = $bill_info['email'];

        $goods_data = DB::table("orders_goods as o")
            ->leftJoin("goods as g","o.goods_id",'=','g.id')
            ->whereRaw("o.order_id='{$order_id}'")
            ->selectRaw("o.*,g.level1,g.level2,g.level3,g.price as goodsprice")
            ->get()
            ->toArray();
        $html='<table style="margin-top:0px;">';
        $email_arr['username'] = $user->full_name;
        $email_arr['orderno'] = $order->order_no;
        $email_arr['order_id'] = $order->order_no;
        $email_arr['goodsprice'] = "$" . $order->price;
        $email_arr['taxes'] = "$0.00";
        $email_arr['price']="$" . $order->price;
        $email_arr['yesprice']="$" . $order->price;
        $email_arr['payprice'] = "$0.00";
        $email_arr['pay_time'] = $order->pay_time;
        $email_arr['url']= env('WEB_HOST') . '/personal/orders/checkout?order_id=' . $order_id . '&type=1';//跳转到购买页面替换地址
        $i=1;
        foreach ($goods_data as $value){
            $value = collect($value)->toArray();
            $prrducts=$goods_class[$value['level1']]['title'] ." for ". $goods_class[$value['level2']]['title'] ." (". $goods_class[$value['level3']]['title'].")";
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
        $email_arr['products'] = $html;
        $mail_service->sendDiyContactEmail($email_arr,6, $email, $email_info);
        return \Response::json(['code'=>200, 'message'=>'发送成功']);
    }

    public function invoicemice(Request $request){
        $param = $request->all();
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $mailedatas = $maile->getFindcategorical(64);
        $Ordermodel = new Order();
        if(!isset($param['id'])){
            return \Response::json(['code'=>403,'msg'=>"缺少订单ID"]);
        }
        $order_data = $Ordermodel->_find("id='{$param['id']}'");
        $order_data = $Ordermodel->objToArr($order_data);
        $user_eemaildata = unserialize($order_data['user_bill']);
        $email->sendDiyContactEmail($order_data, 12, $user_eemaildata['email'],$mailedatas);
        return \Response::json(['code'=>200,'msg'=>"success"]);
    }
    public function testemail(Request $request){
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $param = $request->all();
        $mailedatas = $maile->getFindcategorical($param['id']);
        $email->sendDiyContactEmail([], 16, "wangyuting@kdanmobile.com",$mailedatas);
        return \Response::json(['code'=>200,'msg'=>"success"]);
    }

    /**
     * paypal支付成功回调事件
     * @param Request $request
     */
    public function payPalNotify(Request $request){
        Log::info('paypal异步回调参数', [$request->all()]);
        $payment_status = $request->input('event_type');
        $resource = $request->input('resource');
        $invoice = $resource['invoice_number'] ?? '';
        $trade_no = $resource['parent_payment'] ?? '';
        if(!$invoice || $trade_no){
            Log::info('paypal异步回调错误，缺少invoice_number或者parent_payment', [$request->all()]);
            die;
        }

        //支付完成事件
        if($payment_status == 'PAYMENT.SALE.COMPLETED'){
            $order = new OrdersService();
            $order->notifyHandle($invoice, $trade_no);
        }
    }

    /**
     * paypal支付重定向
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function payPalCallBack(Request $request){
        $param = $request->all();
        Log::info('paypal支付重定向', [$param]);

        //支付成功跳转前端地址
        if(isset($param['success'])){
            if($param['success'] == 'true'){
                $payment_id = $request->input('paymentId');
                $payer_id = $request->input('PayerID');
                $paypal = new PaypalBiz();
                $paypal->callBack($payment_id, $payer_id);
                $webHost = env('WEB_HOST');
                return redirect()->away($webHost);
            }else{
                return \Response::json(['code'=>500, 'message'=>'用户取消支付']);
            }
        }

        return \Response::json();
    }
}