<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\User;
use App\Services\GoodsService;
use App\Services\OrdersService;
use App\Services\PayCenterService;
use App\Services\SaaSOrderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Mpdf\Http\Response;

class SaaSOrderController extends Controller
{
    /**
     * 创建订单
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function createOrder(Request $request){
        $current_user = UserService::getCurrentUser($request);
        if(!$current_user instanceof User){
            return \Response::json(['code'=>401, 'message'=>'未登录，不能购买']);
        }

        $goods_id = $request->input('goods_id');
        if(!$goods_id){
            return \Response::json(['code'=>502, 'message'=>'缺少商品ID']);
        }

        $goodsService = new GoodsService();
        $goods = $goodsService->findById($goods_id);

        if(!$goods instanceof Goods || $goods->status == Goods::STATUS_0_INACTIVE || $goods->deleted == Goods::DELETE_1_YES){
            return \Response::json(['code'=>503, 'message'=>'商品已下架或者删除']);
        }

        $combo_id = $goods->level1;
        $gear_id = $goods->level2;
        $classify = Goodsclassification::getKeyById();
        $combo = array_get($classify, "$combo_id.title");
        $gear = array_get($classify, "$gear_id.title");

        if(!$combo || !$gear){
            return \Response::json(['code'=>504, 'message'=>'商品套餐或者档位不存在']);
        }

        $cycle = '';
        $orderService = new SaaSOrderService();
        if(strstr($combo, '订阅')){
            if($orderService->existsSubscriptionPlan($current_user->id)){
                return ['code'=>505, 'message'=>'该账号已存在订阅中订单，不能重复购买'];
            }
            $package_type = OrderGoods::PACKAGE_TYPE_1_PLAN;

            if(strstr($combo, '月')){
                $cycle = OrderGoods::CYCLE_1_MONTH;
            }else{
                $cycle = OrderGoods::CYCLE_2_YEAR;
            }

        }else{
            $package_type = OrderGoods::PACKAGE_TYPE_2_PACKAGE;
        }

        $result = $orderService->createOrder($current_user, $goods, $package_type, $cycle);

        if($result['code'] == 200){
            return \Response::json(['code'=>200, 'message'=>'success', 'data'=>$result['data']]);
        }else{
            return \Response::json(['code'=>506, 'message'=>'系统错误', 'data'=>[]]);
        }
    }

    public function getOrderStatus(Request $request){
        $order_no = $request->input('order_no');

        $current_user = UserService::getCurrentUser($request);
        if(!$current_user instanceof User){
            return \Response::json(['code'=>401, 'message'=>'未登录，不能购买']);
        }

        $orderService = new SaaSOrderService();
        $order = $orderService->getByOrderNo($order_no);

        if(!$order instanceof Order){
            return \Response::json(['code'=>501, 'message'=>'订单不存在', 'data'=>[]]);
        }

        if($order->status == OrderGoods::STATUS_1_PAID){
            return \Response::json(['code'=>200, 'message'=>'success', 'data'=>['order_no'=>$order_no, 'status'=>$order->status]]);
        }

        //调用支付中心订单状态查询接口
        if($order->status == OrderGoods::STATUS_0_UNPAID){
            $payService = new PayCenterService();
            $result = $payService->getOrderStatus($order->third_trade_no);

            if($result['code'] == 200){
                //支付成功
                if($result['data']['status'] == 'APPROVED'){
                    $orderService->completeOrder($order, $current_user);
                }
            }
        }

        return \Response::json(['code'=>200, 'message'=>'success', 'data'=>['order_no'=>$order_no, 'status'=>$order->status]]);
    }
}