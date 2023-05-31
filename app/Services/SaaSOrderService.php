<?php


namespace App\Services;


use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\Order;
use App\Models\OrderCashFlow;
use App\Models\OrderGoods;
use App\Models\User;

class SaaSOrderService
{
    /**
     * 创建订单
     * @param User $user
     * @param Goods $goods
     * @param $package_type
     * @return array
     */
    public function createOrder(User $user, Goods $goods, $package_type){
        //新增订单
        $pay_type = OrderGoods::PAY_TYPE_5_PAYPAL;
        $order_no = $this->getOrderGoodsNum();
        $status = OrderGoods::STATUS_0_UNPAID;
        $type = OrderGoods::TYPE_2_BUY;
        $details_type = OrderGoods::DETAILS_TYPE_3_SAAS;
        $price = $goods->price;
        $order = Order::add($order_no, $pay_type, $status, $type, $details_type, $price, $user->id, 1);

        //新增子订单
        $order_goods_no = $this->getOrderGoodsNum();
        $order_goods = OrderGoods::add($order->id, $order_no, $order_goods_no, $pay_type, $status, $type, $details_type, $price, $user->id, $goods->id, $package_type, null);

        //调用支付中心生成支付链接
        $payService = new PayCenterService();
        $result = $payService->createOrder($order_no, $price);
        if($result['code'] == 200){
            $third_trade_no = array_get($result, 'data.id');
            $url = array_get($result, 'data.pay_url');
            $order->third_trade_no = $third_trade_no;
            $order->pay_url = $url;
            $order->save();

            $order_goods->third_trade_no = $third_trade_no;
            $order_goods->save();

            return ['code'=>200, 'data'=>['order_no'=>$order->order_no, 'pay_url'=>$url]];
        }else{
            return ['code'=>500, 'message'=>'创建订单失败'];
        }
    }

    /**
     * 后台新增SaaS订单
     * @param $param
     * @return array
     */
    public function saasRunData($param)
    {
        $data = $param['data'];
        $user = UserService::getByEmail($data['email']);
        if (!$user instanceof User) {
            return ['code'=>500, 'message'=>'该邮箱未注册，不能创建订单'];
        }

        if($this->existsSubscriptionPlan($user->id)){
            return ['code'=>500, 'message'=>'该邮箱已存在订阅中订单，不能重复创建'];
        }

        $classification = Goodsclassification::getKeyById();
        $combo = array_get($classification, "{$data['level1']}.title");
        $gear = array_get($classification, "{$data['level2']}.title");
        if(!$combo || !$gear){
            return ['code'=>500, 'message'=>'套餐或档位不存在'];
        }

        $goodService = new GoodsService();
        $goods = $goodService->getGoodsByGear($data['level1'], $data['level2']);
        if(!$goods instanceof Goods){
            return ['code'=>500, 'message'=>'该套餐档位下没有商品，请先新增商品'];
        }

        $pay_years = $data['pay_years'] ? $data['pay_years'] : 0;
        if(strstr($combo, '年订阅') && $data['pay_years'] < 12){
            return ['code'=>500, 'message'=>'年订阅有效期必须大于12个月'];
        }

        if($gear == '手动配置'){
            $price = $data['price'];
            $special_assets = $data['special_assets'];
        }else {
            $price = $goods['price'];
            $special_assets = '';
        }

        //新增订单
        $pay_type = OrderGoods::PAY_TYPE_4_OTHER;
        $order_no = $this->getOrderGoodsNum();
        $status = OrderGoods::STATUS_1_PAID;
        $type = OrderGoods::TYPE_1_BACKGROUND;
        $details_type = OrderGoods::DETAILS_TYPE_3_SAAS;
        $order_model = Order::add($order_no, $pay_type, $status, $type, $details_type, $price, $user->id, 1);
        $order_id = $order_model->id;

        //新增子订单
        $order_goods_no = $this->getOrderGoodsNum();
        if(strtolower($combo) == 'package'){
            $package_type = OrderGoods::PACKAGE_TYPE_2_PACKAGE;
        }else{
            $package_type = OrderGoods::PACKAGE_TYPE_1_PLAN;
        }
        OrderGoods::add($order_id, $order_no, $order_goods_no, $pay_type, $status, $type, $details_type, $price, $user->id, $goods->id, $package_type, $pay_years, $special_assets);

        //更新流水信息
        OrderCashFlow::add($order_id, $pay_type, $package_type, $price, 0, 0, $price, '', '', OrderCashFlow::CURRENCY_1_USD);

        //更新用户类型
        $user_service = new UserService();
        $user_service->changeType(Order::DETAILS_STATUS_3_SAAS, $user->id);

        //更新用户SaaS资产信息
        $remain_service = new UserRemainService();
        $total_files = $special_assets ?: $gear;
        $remain_service->resetRemain($user->id, $user->email, $total_files, $package_type);

        return ['code'=>200, 'message'=>'创建成功'];
    }

    /**
     * SaaS 判断用户是否存在订阅中的订单
     * @param $user_id
     * @return bool
     */
    public function existsSubscriptionPlan($user_id){
        return OrderGoods::query()
            ->where('details_type', OrderGoods::DETAILS_TYPE_3_SAAS)
            ->where('user_id', $user_id)
            ->where('package_type', OrderGoods::PACKAGE_TYPE_1_PLAN)
            ->where('status', OrderGoods::STATUS_1_PAID)
            ->exists();
    }

    /**
     * 生成子订单编号
     * @return string
     */
    public function getOrderGoodsNum(){
        return chr(rand(65, 90)) .chr(rand(65, 90)) .chr(rand(65, 90)). time();
    }

    /**
     * 根据订单编号获取订单
     * @param $order_no
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByOrderNo($order_no){
        return Order::query()
            ->where('order_no', $order_no)
            ->first();
    }

    public function completeOrder(Order $order, User $user){
        //修改订单为已支付状态
        $order->status = OrderGoods::STATUS_1_PAID;
        $order->save();

        //修改子订单为已支付状态
        $order_goods = OrderGoods::getByOrderId($order->id);
        $order_goods->status = OrderGoods::STATUS_1_PAID;
        $order_goods->save();

        //更新流水信息
        OrderCashFlow::add($order->id, $order->pay_type, $order_goods->package_type, $order->price, 0, 0, $order->price, $order->third_trade_no, '', OrderCashFlow::CURRENCY_1_USD);

        //更新用户类型
        $user_service = new UserService();
        $user_service->changeType(Order::DETAILS_STATUS_3_SAAS, $user->id);

        //更新用户SaaS资产信息
        $remain_service = new UserRemainService();
        $total_files = Goods::getTotalFilesByGoods($order_goods->goods_id);
        $remain_service->resetRemain($user->id, $user->email, $total_files, $order_goods->package_type);
    }
}