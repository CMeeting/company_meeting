<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\OrderGoods;
use App\Models\User;
use App\Services\GoodsService;
use App\Services\SaaSOrderService;
use App\Services\UserService;
use Illuminate\Http\Request;

class SaaSOrderController extends Controller
{
    public function createOrder(Request $request){
        $current_user = UserService::getCurrentUser($request);
        if(!$current_user instanceof User){
            return \Response::json(['code'=>501, 'message'=>'未登录，不能购买']);
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

        $orderService = new SaaSOrderService();
        if(strstr($combo, '订阅')){
            if($orderService->existsSubscriptionPlan($current_user->id)){
                return ['code'=>500, 'message'=>'该账号已存在订阅中订单，不能重复购买'];
            }
            $package_type = OrderGoods::PACKAGE_TYPE_1_PLAN;
        }else{
            $package_type = OrderGoods::PACKAGE_TYPE_2_PACKAGE;
        }

        $result = $orderService->createOrder($current_user, $goods, $package_type);
        dd($result);
    }
}