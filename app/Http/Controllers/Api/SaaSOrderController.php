<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\User;
use App\Services\PayCenterService;
use App\Services\SaaSOrderService;
use App\Services\UserService;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Response;

class SaaSOrderController extends Controller
{
    /**
     * 创建订单
     * @param Request $request
     * @return array|JsonResponse
     */
    public function createOrder(Request $request){
        $current_user = UserService::getCurrentUser($request);
        if(!$current_user instanceof User){
            return Response::json(['code'=>401, 'message'=>'Unauthorized']);
        }

        Log::info('创建订单参数', ['email' => $current_user->email, 'params' => $request->all()]);

        $goods_id = $request->input('goods_id');
        if(!$goods_id){
            return Response::json(['code'=>502, 'message'=>'Parameter error']);
        }

        //已有未支付订单直接返回
        $orderService = new SaaSOrderService();
        $cache_order = $orderService->getOrderCache($current_user->id, $goods_id);
        if($cache_order){
            Log::info('创建订单存在未支付的订单缓存', ['email' => $current_user->email, 'params' => $request->all()]);
            return Response::json(['code'=>200, 'message'=>'success', 'data'=>$cache_order]);
        }

        //验证商品以及用户是否存在订阅
        $result = $orderService->verifyGoodsOrSub($goods_id, $current_user->id, true);
        if ($result == SaaSOrderService::INVALID_2_GOODS){
            return Response::json(['code'=>503, 'message'=>'The product you are trying to purchase has been updated. Please reload the page and try again.']);
        }elseif($result == SaaSOrderService::INVALID_3_GOODS_CLASSIFICATION){
            return Response::json(['code'=>504, 'message'=>'The product package or tier does not exist.']);
        }elseif($result == SaaSOrderService::INVALID_4_SUB){
            return Response::json(['code'=>505, 'message'=>'You cannot purchase the same subscription plan while your current subscription is active. If you need to process more files, you can choose a package plan instead.']);
        }

        $result = $orderService->createOrder($current_user, $goods_id);

        Log::info('创建订单结果', ['email' => $current_user->email, 'result' => $result]);

        if($result['code'] == 200){
            //新增订单缓存
            $orderService->addOrderCache($current_user->id, $goods_id, $result['data']);

            return Response::json(['code'=>200, 'message'=>'success', 'data'=>$result['data']]);
        }else{
            return Response::json(['code'=>506, 'message'=>'System error.', 'data'=>[]]);
        }
    }

    /**
     * 查询订单状态
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderStatus(Request $request){
        $order_no = $request->input('order_no');

        $current_user = UserService::getCurrentUser($request);
        if(!$current_user instanceof User){
            return Response::json(['code'=>401, 'message'=>'Unauthorized']);
        }

        $order = Order::getByOrderNo($order_no);

        if(!$order instanceof Order){
            return Response::json(['code'=>501, 'message'=>'订单不存在', 'data'=>[]]);
        }

        $start_date = $end_date = null;
        if($order->status == OrderGoods::STATUS_1_PAID){
            $order_goods = OrderGoods::getByOrderNo($order_no);
            if($order_goods->package_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
                $start_date = Carbon::parse($order_goods->pay_time)->format('Y/m/d');
                $end_date = Carbon::parse($order_goods->next_billing_time)->format('Y/m/d');
            }
            return Response::json(['code'=>200, 'message'=>'success', 'data'=>['order_no'=>$order_no, 'status'=>$order->status, 'start_date'=>$start_date, 'end_date'=>$end_date]]);
        }

        $orderGoods = OrderGoods::getByOrderId($order->id);

        //调用支付中心订单状态查询接口
        $status = $order->status;
        if($status == OrderGoods::STATUS_0_UNPAID){
            $payService = new PayCenterService();
            $result = $payService->getOrderStatus($order->third_trade_no, $orderGoods->package_type);

            if($result['code'] == 200){
                //支付成功
                if($result['data']['status'] == 'APPROVED' || $result['data']['status'] == 'ACTIVE'){
                    $orderService = new SaaSOrderService();
                    $order_result = $orderService->completeOrder($order->third_trade_no, $result['data']['next_billing_time']);
                    if(!empty($order_result)){
                        $status = OrderGoods::STATUS_1_PAID;
                        $start_date = $order_result['start_date'] ? Carbon::parse($order_result['start_date'])->format('Y-m-d') : '';
                        $end_date = $order_result['end_date'] ? Carbon::parse($order_result['end_date'])->format('Y-m-d') : '';
                    }
                }
            }
        }

        return Response::json(['code'=>200, 'message'=>'success', 'data'=>['order_no'=>$order_no, 'status'=>$status, 'start_date'=>$start_date, 'end_date'=>$end_date]]);
    }

    /**
     * 发送支付失败邮件
     * @param Request $request
     * @return JsonResponse
     */
    public function sendFailedEmail(Request $request){
        $user = UserService::getCurrentUser($request);

        $order_no = $request->input('order_no');

        $order = Order::getByOrderNo($order_no);
        $order_goods = OrderGoods::getByOrderNo($order_no);

        $service = new SaaSOrderService();
        $goods = Goods::query()->find($order_goods->goods_id);
        $combo = Goodsclassification::getComboById($goods->level1);

        $service->sendPayEmail('API购买失败', $order_goods->goods_no, $order->created_at, $order->price, $combo, $user);

        return Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 回调事件处理
     * @param Request $request
     * @return JsonResponse
     */
    public function webHook(Request $request){
        Log::info('支付服务回调', ['params'=>$request->all()]);

        $event_type = $request->input('event_type');
        $third_trade_no = $request->input('third_trade_id');
        $next_billing_time = $request->input('next_billing_time');

        $order = Order::getByTradeNo($third_trade_no);
        if(!$order instanceof Order){
            return Response::json(['code'=>501, 'message'=>'订单不存在']);
        }

        $lock = 'webhook:' . $third_trade_no;
        try {
            $orderService = new SaaSOrderService();
            switch ($event_type){
                case OrderGoods::EVENT_1_PAYMENT_SUCCESS:
                    //在方法里面上锁，还有其他地方调用了这个方法
                    $orderService->completeOrder($third_trade_no, $next_billing_time);
                    break;
                case OrderGoods::EVENT_3_DEDUCTION_SUCCESS:
                    Cache::lock($lock)->get(function () use($orderService, $third_trade_no, $next_billing_time){
                        $orderService->deductionSuccess($third_trade_no, $next_billing_time);
                    });
                    break;
                case  OrderGoods::EVENT_4_DEDUCTION_FAILED:
                    Cache::lock($lock)->get(function () use($orderService, $third_trade_no){
                        $orderService->deductionFailed($third_trade_no);
                    });
                    break;
                case OrderGoods::EVENT_5_PLAN_CANCEL:
                    Cache::lock($lock)->get(function () use($orderService, $third_trade_no){
                        $orderService->cancelPlan($third_trade_no);
                    });
                    break;
                default:
                    break;
            }

            return Response::json(['code'=>200, 'message'=>'success']);
        }catch (\Exception $e){
            Log::info('支付回调处理失败', ['event_type'=>$event_type, 'third_trade_id'=>$third_trade_no, 'error'=>$e->getTrace()]);
            //释放锁
            Cache::forget($lock);

            return Response::json(['code'=>500, 'message'=>'system error']);
        }
    }

    /**
     * 验证商品或者是否已存在订阅
     * @param Request $request
     * @return JsonResponse
     */
    public function verifySubOrGoods(Request $request){
        $user = UserService::getCurrentUser($request);
        if(!$user instanceof User){
            return Response::json(['code'=>401, 'message'=>'Unauthorized']);
        }

        $goods_id = $request->input('goods_id');
        $verify_sub = $request->input('verify_sub', false);

        $service = new SaaSOrderService();
        $result = $service->verifyGoodsOrSub($goods_id, $user->id, $verify_sub);

        if($result == SaaSOrderService::INVALID_1_NULL){
            return Response::json(['code'=>200, 'message'=>'success']);
        }elseif ($result == SaaSOrderService::INVALID_2_GOODS){
            return Response::json(['code'=>503, 'message'=>'The product you are trying to purchase has been updated. Please reload the page and try again.']);
        }elseif($result == SaaSOrderService::INVALID_3_GOODS_CLASSIFICATION){
            return Response::json(['code'=>504, 'message'=>'The product package or tier does not exist.']);
        }else{
            return Response::json(['code'=>505, 'message'=>'You cannot purchase the same subscription plan while your current subscription is active. If you need to process more files, you can choose a package plan instead.']);
        }
    }
}