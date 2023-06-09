<?php


namespace App\Services;


use App\Jobs\CloseOrder;
use App\Jobs\SendEmailAttachment;
use App\Models\BackGroundUserRemain;
use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Models\Mailmagicboard;
use App\Models\Order;
use App\Models\OrderCashFlow;
use App\Models\OrderGoods;
use App\Models\OrderGoodsCancel;
use App\Models\User;
use Carbon\Carbon;
use DB;

class SaaSOrderService
{
    /**
     * 创建订单
     * @param User $user
     * @param Goods $goods
     * @param $package_type
     * @param $cycle
     * @return array
     */
    public function createOrder(User $user, Goods $goods, $package_type, $cycle = ''){
        //新增订单
        $pay_type = OrderGoods::PAY_TYPE_5_PAYPAL;
        $order_no = $this->getOrderGoodsNum();
        $status = OrderGoods::STATUS_0_UNPAID;
        $type = OrderGoods::TYPE_2_BUY;
        $details_type = OrderGoods::DETAILS_TYPE_3_SAAS;
        $price = $goods->price;
        $order = Order::add($order_no, $pay_type, $status, $type, $details_type, $price, $user->id, 1);

        //新增子订单
        $pay_years = null;
        $order_goods_no = $this->getOrderGoodsNum();
        if($cycle == OrderGoods::CYCLE_2_YEAR){
            $pay_years = 12;
        }
        $order_goods = OrderGoods::add($order->id, $order_no, $order_goods_no, $pay_type, $status, $type, $details_type, $price, $user->id, $goods->id, $package_type, $pay_years);

        //订单未支付三小时后关闭
        dispatch(new CloseOrder($order->id))->delay(Carbon::now()->addHours(3));

        //调用支付中心生成支付链接
        $payService = new PayCenterService();

        if($package_type == OrderGoods::PACKAGE_TYPE_2_PACKAGE){
            $result = $payService->createPackageOrder($order_no, $price);
        }else{
            $result = $payService->createPlanOrder($order_no, $price, $cycle);
        }

        //接口正常返回结果
        if(is_array($result)){
            $code = $result['code'];
        }else{
            $code = $result->code;
        }

        //订单创建成功
        if($code == 200){
            $data = $result->data;
            $third_trade_no = $data->id;
            $pay_url = $data->payHref;
            $order->third_trade_no = $third_trade_no;
            $order->pay_url = $pay_url;
            $order->save();

            $order_goods->third_trade_no = $third_trade_no;
            $order_goods->save();

            return ['code'=>200, 'data'=>['order_no'=>$order->order_no, 'pay_url'=>$pay_url]];
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

        if(strtolower($combo) != 'package' && $this->existsSubscriptionPlan($user->id)){
            return ['code'=>500, 'message'=>'该邮箱已存在订阅中订单，不能重复创建'];
        }

        if($gear == '手动配置'){
            $price = $data['price'];
            $special_assets = $data['special_assets'];
        }else {
            $price = $goods['price'];
            $special_assets = '';
        }

        //新增订单
        try{
            DB::beginTransaction();
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
            $remain_service->resetRemain($user->id, $user->email, $total_files, $package_type, BackGroundUserRemain::STATUS_1_ACTIVE, 'add');

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return ['code'=>500, 'message'=>'创建失败', 'error'=>$e->getMessage()];
        }


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

    /**
     * 订单支付成功
     * @param Order $order
     * @param $next_billing_time
     * @return bool
     */
    public function completeOrder(Order $order, $next_billing_time = null){
        if($order->status == OrderGoods::STATUS_0_UNPAID){
            try{
                DB::beginTransaction();
                $order->status = OrderGoods::STATUS_1_PAID;
                $order->pay_time = date('Y-m-d H:i:s');
                $order->save();

                $user = User::find($order->user_id);

                //修改子订单为已支付状态
                $order_goods = OrderGoods::getByOrderId($order->id);
                $order_goods->status = OrderGoods::STATUS_1_PAID;
                $order_goods->next_billing_time = $next_billing_time;
                $order_goods->pay_time = date('Y-m-d H:i:s');
                $order_goods->save();

                //更新流水信息
                \Log::info('支付成功更新流水信息', ['order_id'=>$order->id]);
                OrderCashFlow::add($order->id, $order->pay_type, $order_goods->package_type, $order->price, 0, 0, $order->price, $order->third_trade_no, '', OrderCashFlow::CURRENCY_1_USD);

                //更新用户类型
                $user_service = new UserService();
                $user_service->changeType(Order::DETAILS_STATUS_3_SAAS, $user->id);

                //更新用户SaaS资产信息
                $remain_service = new UserRemainService();
                $total_files = Goods::getTotalFilesByGoods($order_goods->goods_id);
                \Log::info('支付成功更新资产信息', ['order_id'=>$order->id, 'user_id'=>$user->id, 'total_files'=>$total_files, 'package_type'=>$order_goods->package_type]);
                $remain_service->resetRemain($user->id, $user->email, $total_files, $order_goods->package_type, BackGroundUserRemain::STATUS_1_ACTIVE, 'add');

                DB::commit();
                \Log::info('订单支付成功回调处理成功', ['third_trade_id'=>$order->third_trade_no]);
            }catch (\Exception $e){
                DB::rollBack();
                \Log::error('订单支付成功回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

                return false;
            }

            //发送支付成功邮件
            $goods = OrderGoods::find($order_goods->goods_id);
            $combo = Goodsclassification::getComboById($goods->level1);
            $this->sendPayEmail('API购买成功', $order->order_no, $order->pay_time, $order->price, $combo, $user);
        }

        return true;
    }

    /**
     * 订阅周期扣款成功
     * @param Order $order
     * @param $next_billing_time
     * @return bool
     */
    public function deductionSuccess(Order $order, $next_billing_time){
        $user = User::find($order->user_id);

        //更新下次扣款时间
        try{
            DB::beginTransaction();
            $order_goods = OrderGoods::getByOrderId($order->id);
            $combo = Goodsclassification::getComboById($order_goods->level1);
            //年订阅更新有效期
            if(strstr($combo, '年')){
                $order_goods->pay_years += 12;
            }
            $order_goods->next_billing_time = $next_billing_time;
            $order_goods->save();

            //更新流水信息
            \Log::info('订阅扣款成功更新流水信息', ['order_id'=>$order->id]);
            OrderCashFlow::add($order->id, $order->pay_type, $order_goods->package_type, $order->price, 0, 0, $order->price, $order->third_trade_no, '', OrderCashFlow::CURRENCY_1_USD);

            //更新用户SaaS资产信息
            $remain_service = new UserRemainService();
            $total_files = Goods::getTotalFilesByGoods($order_goods->goods_id);
            \Log::info('订阅扣款成功更新资产信息', ['order_id'=>$order->id, 'user_id'=>$user->id, 'total_files'=>$total_files, 'package_type'=>$order_goods->package_type]);
            $remain_service->resetRemain($user->id, $user->email, $total_files, $order_goods->package_type, BackGroundUserRemain::STATUS_1_ACTIVE, 'reset');
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            \Log::error('订阅扣款成功回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

            return false;
        }

        return true;
    }

    /**
     * 订阅周期扣款失败
     * @param Order $order
     * @return bool
     */
    public function deductionFailed(Order $order){
        $user = User::find($order->user_id);

        if($order->status == OrderGoods::STATUS_1_PAID){
            try{
                DB::beginTransaction();
                //扣款失败修改状态为取消订阅
                $order->status = OrderGoods::STATUS_5_UNSUBSCRIBE;
                $order->save();

                $order_goods = OrderGoods::getByOrderId($order->id);
                $order_goods->status = OrderGoods::STATUS_5_UNSUBSCRIBE;
                $order_goods->save();

                //更新用户SaaS资产信息
                \Log::info('订阅周期扣款失败更新资产信息', ['order_id'=>$order->id]);
                $remain_service = new UserRemainService();
                $remain_service->resetRemain($user->id, $user->email, 0, $order_goods->package_type, BackGroundUserRemain::STATUS_2_INACTIVE, 'cancel');

                //新增订阅取消记录
                \Log::info('订阅周期扣款失败增加已处理取消订阅记录', ['order_id'=>$order->id]);
                $reset_date = date('Y-m-d');
                $remark = '扣款失败回调事件';
                OrderGoodsCancel::add($order_goods->id, OrderGoodsCancel::STATUS_2_PROCESSED, $reset_date, $remark);

                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                \Log::error('订阅周期扣款失败回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

                return false;
            }
        }

        return true;
    }

    /**
     * 取消订阅
     * @param Order $order
     * @return bool
     */
    public function cancelPlan(Order $order){
        if($order->status == OrderGoods::STATUS_1_PAID){
            try{
                DB::beginTransaction();
                //修改状态为取消订阅
                $order->status = OrderGoods::STATUS_5_UNSUBSCRIBE;
                $order->save();

                $order_goods = OrderGoods::getByOrderId($order->id);
                $order_goods->status = OrderGoods::STATUS_5_UNSUBSCRIBE;
                $order_goods->save();

                //新增订阅取消记录
                //获取处理时间
                \Log::info('取消订阅回调增加待处理的取消订阅记录', ['order_id'=>$order->id]);
                $next_billing_time = $order_goods->next_billing_time;
                $reset_date = Carbon::parse($next_billing_time)->addMonthsNoOverflow(1)->addDay()->format('Y-m-d');
                $remark = '取消订阅回调事件';
                OrderGoodsCancel::add($order_goods->id, OrderGoodsCancel::STATUS_1_UNPROCESSED, $reset_date, $remark);
            }catch (\Exception $e){
                DB::rollBack();
                \Log::error('取消订阅回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

                return false;
            }
        }
        return true;
    }

    /**
     * 发送支付成功或者失败邮件
     * @param $email_name
     * @param $order_no
     * @param $pay_time
     * @param $price
     * @param $combo
     * @param $user
     */
    public function sendPayEmail($email_name, $order_no, $pay_time, $price, $combo, User $user){
        //主站官网地址
        $website = env('WEB_HOST');
        //SAAS官网地址
        $website_saas = env('WEB_HOST_SAAS');

        //发送邮件
        $email_model = Mailmagicboard::getByName($email_name);

        $data['title'] = $email_model->title;
        $data['info'] = $email_model->info;
        $data['id'] = $email_model->id;
        $data['info'] = str_replace("#@website", $website, $data['info']);
        $data['info'] = str_replace("#@saas_site", $website_saas, $data['info']);

        $data['info'] = str_replace("#@username", $user->full_name, $data['info']);

        $data['info'] = str_replace("#@order_no", $order_no, $data['info']);

        $pay_time = Carbon::parse($pay_time)->format('Y/m/d H:i:s');
        $data['info'] = str_replace("#@pay_date", $pay_time, $data['info']);

        if(strstr($combo, '年')){
            $plan = 'Annually';
        }elseif(strstr($combo, '月')){
            $plan = 'Monthly';
        }else{
            $plan = 'Package';
        }
        $data['info'] = str_replace("#@plan", $plan, $data['info']);

        $taxes = $price * 0.05;
        $taxes = round($taxes);
        $data['info'] = str_replace("#@taxes", '$' . $taxes, $data['info']);

        $product_price = $price - $taxes;
        $data['info'] = str_replace("#@product_price", '$' . $product_price, $data['info']);

        $data['info'] = str_replace("#@price", '$' . $price, $data['info']);

        $dashboard_url = env('WEB_BACKGROUND_USER_SAAS') . '/dashboard';
        $data['info'] = str_replace("#@dashboard_url", $dashboard_url, $data['info']);

        $doc_url = env('WEB_HOST_SAAS') . '/api-reference/overview';
        $data['info'] = str_replace("#@doc_url", $doc_url, $data['info']);

        $buy_url = env('WEB_HOST_SAAS') . '/api/pricing';
        $data['info'] = str_replace("#@buy_url", $buy_url, $data['info']);

        dispatch(new SendEmailAttachment($data['info'], $data['title'], $user->email));
    }
}