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
use App\Models\UserSubscriptionProcess;
use App\Models\User;
use Cache;
use Carbon\Carbon;
use DB;
use Exception;
use Log;

class SaaSOrderService
{

    const INVALID_1_NULL = 1;
    const INVALID_2_GOODS = 2;
    const INVALID_3_GOODS_CLASSIFICATION = 3;
    const INVALID_4_SUB = 4;


    /**
     * 创建订单
     * @param User $user
     * @param string $goods_id
     * @return array
     */
    public function createOrder(User $user, $goods_id){
        //新增订单
        $pay_type = OrderGoods::PAY_TYPE_5_PAYPAL;
        $order_no = $this->getOrderGoodsNum();
        $status = OrderGoods::STATUS_0_UNPAID;
        $type = OrderGoods::TYPE_2_BUY;
        $details_type = OrderGoods::DETAILS_TYPE_3_SAAS;

        //套餐
        $goods = Goods::query()->find($goods_id);
        $combo_id = $goods->level1;
        $classify = Goodsclassification::getKeyById();
        $combo = array_get($classify, "$combo_id.title");

        $cycle = $pay_years = null;
        if($combo != Goods::COMBO_PACKAGE){
            $package_type = OrderGoods::PACKAGE_TYPE_1_PLAN;

            if($combo == Goods::COMBO_MONTHLY){
                $cycle = OrderGoods::CYCLE_1_MONTH;
            }else{
                $cycle = OrderGoods::CYCLE_2_YEAR;
                //年订阅资产重置需要用到$pay_years
                $pay_years = 12;
            }
        }else{
            $package_type = OrderGoods::PACKAGE_TYPE_2_PACKAGE;
        }

        try{
            DB::beginTransaction();
            //新增总订单
            $order = Order::add($order_no, $pay_type, $status, $type, $details_type, $goods->price, $user->id, 1);

            //新增子订单
            $order_goods_no = $this->getOrderGoodsNum();
            $order_goods = OrderGoods::add($order->id, $order_no, $order_goods_no, $pay_type, $status, $type, $details_type, $goods->price, $user->id, $goods->id, $package_type, $pay_years);

            DB::commit();
            //订单未支付三小时后关闭
            dispatch(new CloseOrder($order->id))->delay(Carbon::now()->addHours(3));

            //调用支付中心生成支付链接
            $payService = new PayCenterService();

            if($package_type == OrderGoods::PACKAGE_TYPE_2_PACKAGE){
                if($user->email == env('PAY_WHITE')){
                    $price = 1;
                }else{
                    $price = $goods->price;
                }
                $result = $payService->createPackageOrder($order_no, $price);
            }else{
                $result = $payService->createPlanOrder($order_no, $goods->price, $cycle);
            }

            //接口正常返回结果
            if(is_array($result)){
                $code = $result['code'];
            }else{
                $code = $result->code;
            }

            //订单创建成功
            if($code == 200) {
                $data = $result->data;
                $third_trade_no = $data->id;
                $pay_url = $data->payHref;
                $order->third_trade_no = $third_trade_no;
                $order->pay_url = $pay_url;
                $order->save();

                $order_goods->third_trade_no = $third_trade_no;
                $order_goods->save();

                return ['code'=>200, 'data'=>['order_no'=>$order->order_no, 'pay_url'=>$pay_url, 'third_trade_no'=>$third_trade_no]];
            }
        }catch (Exception $e){
            Log::info('创建订单失败', ['user'=>$user->email, 'info'=>$e->getTrace()]);
            DB::rollBack();
        }

        return ['code'=>500, 'message'=>'创建订单失败'];
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

        //实际的单位是月份
        $pay_years = $data['pay_years'] ? $data['pay_years'] : 0;
        if($combo == Goods::COMBO_ANNUALLY && $data['pay_years'] < 12){
            return ['code'=>500, 'message'=>'Annually有效期必须大于12个月'];
        }

        if($combo != Goods::COMBO_PACKAGE && OrderGoods::existsSubscriptionPlan($user->id)){
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
            switch ($combo){
                case Goods::COMBO_PACKAGE:
                    $package_type = OrderGoods::PACKAGE_TYPE_2_PACKAGE;
                    break;
                case Goods::COMBO_MONTHLY:
                    $package_type = OrderGoods::PACKAGE_TYPE_1_PLAN;
                    $cycle = OrderGoods::CYCLE_1_MONTH;
                    break;
                case Goods::COMBO_ANNUALLY:
                    $package_type = OrderGoods::PACKAGE_TYPE_1_PLAN;
                    $cycle = OrderGoods::CYCLE_2_YEAR;
                    break;
                default:
                    break;
            }
            $goods_model = OrderGoods::add($order_id, $order_no, $order_goods_no, $pay_type, $status, $type, $details_type, $price, $user->id, $goods->id, $package_type, $pay_years, $special_assets);

            //更新流水信息
            OrderCashFlow::add($goods_model->id, $pay_type, $package_type, $price, 0, 0, $price, '', '', OrderCashFlow::CURRENCY_1_USD);

            //更新用户类型
            $user_service = new UserService();
            $user_service->changeType(Order::DETAILS_STATUS_3_SAAS, $user->id);

            //更新用户SaaS资产信息
            $remain_service = new UserRemainService();
            $total_files = $special_assets ?: $gear;

            //获取套餐有效期
            if($package_type == OrderGoods::PACKAGE_TYPE_1_PLAN){
                $start_date = Carbon::now();
                $start_date_string = (clone $start_date)->format('Y-m-d H:i:s');
                $end_date = $start_date->addMonthsNoOverflow($pay_years)->format('Y-m-d H:i:s');
            }

            $remain_service->resetRemain($user->id, $user->email, $total_files, $package_type, BackGroundUserRemain::STATUS_1_ACTIVE, BackGroundUserRemain::OPERATE_TYPE_1_ADD, $start_date_string ?? null, $end_date ?? null, $cycle ?? null);

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            return ['code'=>500, 'message'=>'创建失败', 'error'=>$e->getTrace()];
        }

        return ['code'=>200, 'message'=>'创建成功'];
    }

    /**
     * 生成子订单编号
     * @return string
     */
    public function getOrderGoodsNum(){
        return chr(rand(65, 90)) .chr(rand(65, 90)) .chr(rand(65, 90)). uniqid();
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
     * @param $third_trade_no
     * @param $next_billing_time
     * @param $pay_id
     * @return array
     */
    public function completeOrder($third_trade_no, $next_billing_time = null, $pay_id = null){
        $lock = 'webhook:' . $third_trade_no;
        $order = Order::getByTradeNo($third_trade_no);
        $result = [];

        $lock = Cache::lock($lock, 60*60);
        if ($lock->get()) {
            if($order->status == OrderGoods::STATUS_0_UNPAID){
                $order_goods = OrderGoods::getByOrderId($order->id);

                try{
                    DB::beginTransaction();
                    //删除订单缓存
                    $this->delOrderCache($order->user_id, $order_goods->goods_id);

                    //修改订单和子订单为已支付状态
                    $pay_time = Carbon::now();
                    $pay_time_string = $pay_time->toDateTimeString();
                    $order->status = OrderGoods::STATUS_1_PAID;
                    $order->pay_time = $pay_time_string;
                    $order->save();

                    $order_goods->status = OrderGoods::STATUS_1_PAID;
                    $order_goods->next_billing_time = $next_billing_time;
                    $order_goods->pay_time = $pay_time_string;
                    $order_goods->save();

                    //更新流水信息
                    Log::info('支付成功更新流水信息', ['third_trade_no'=>$third_trade_no]);
                    OrderCashFlow::add($order_goods->id, $order->pay_type, $order_goods->package_type, $order->price, 0, 0, $order->price, $order->third_trade_no, $pay_id, OrderCashFlow::CURRENCY_1_USD);

                    //更新用户类型
                    $user = User::find($order->user_id);
                    $user_service = new UserService();
                    $user_service->changeType(Order::DETAILS_STATUS_3_SAAS, $user->id);

                    //更新用户SaaS资产信息
                    $remain_service = new UserRemainService();
                    $total_files = Goods::getTotalFilesByGoods($order_goods->goods_id);
                    Log::info('支付成功更新资产信息', ['order_id'=>$order->id, 'user_id'=>$user->id, 'total_files'=>$total_files, 'package_type'=>$order_goods->package_type]);

                    //获取套餐有效期，周期
                    $start_date = $end_date = $cycle = null;
                    $goods = Goods::query()->find($order_goods->goods_id);
                    $combo = Goodsclassification::getComboById($goods->level1);
                    if($combo == Goods::COMBO_MONTHLY){
                        $cycle = OrderGoods::CYCLE_1_MONTH;
                        $start_date = $pay_time_string;
                        $end_date = $remain_service->getSubEndDate($order_goods->id, $start_date, $cycle);
                    }elseif ($combo == Goods::COMBO_ANNUALLY){
                        $cycle = OrderGoods::CYCLE_2_YEAR;
                        $start_date = $pay_time_string;
                        $end_date = $remain_service->getSubEndDate($order_goods->id, $start_date, $cycle);
                    }

                    $remain_service->resetRemain($user->id, $user->email, $total_files, $order_goods->package_type, BackGroundUserRemain::STATUS_1_ACTIVE, BackGroundUserRemain::OPERATE_TYPE_1_ADD, $start_date, $end_date, $cycle);

                    DB::commit();
                    Log::info('订单支付成功回调处理成功', ['third_trade_id'=>$order->third_trade_no]);

                    //发送支付成功邮件
                    $this->sendPayEmail('API购买成功', $order_goods->goods_no, $order->pay_time, $order->price, $combo, $user);

                    $result = ['start_date'=>$start_date, 'end_date'=>$end_date];
                }catch (Exception $e){
                    DB::rollBack();
                    Log::error('订单支付成功回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);
                }
            }

            //释放锁
            $lock->release();
        }

        return $result;
    }

    /**
     * 订阅周期扣款成功
     * @param $third_trade_no
     * @param $next_billing_time
     * @param $pay_id
     * @return bool
     */
    public function deductionSuccess($third_trade_no, $next_billing_time, $pay_id){
        $order = Order::getByTradeNo($third_trade_no);
        $user = User::find($order->user_id);
        $order_goods = OrderGoods::getByOrderId($order->id);

        if($order_goods->status != OrderGoods::STATUS_1_PAID){
            Log::info('订阅扣款成功回调订单状态错误', ['pay_id'=>$pay_id, 'third_trade_no'=>$third_trade_no]);
            return false;
        }

        //回调id已存在
        if($pay_id && OrderCashFlow::existsPayId($pay_id)){
            Log::info('订阅扣款成功回调重复', ['pay_id'=>$pay_id, 'third_trade_no'=>$third_trade_no]);
            return false;
        }

        //更新下次扣款时间
        try{
            DB::beginTransaction();

            $combo = Goodsclassification::getComboById($order_goods->level1);
            //年订阅更新有效期
            if($combo == Goods::COMBO_ANNUALLY){
                $order_goods->pay_years += 12;
            }
            $order_goods->next_billing_time = $next_billing_time;
            $order_goods->save();

            //扣款成功第二天重置
            $reset_date = Carbon::now()->addDay()->toDateString();

            //更新流水信息
            Log::info('订阅扣款成功更新流水信息', ['third_trade_no'=>$third_trade_no]);
            OrderCashFlow::add($order_goods->id, $order->pay_type, $order_goods->package_type, $order->price, 0, 0, $order->price, $order->third_trade_no, $pay_id, OrderCashFlow::CURRENCY_1_USD);

            //增加用户扣款成功操作
            UserSubscriptionProcess::add($order_goods->id, $user->id, UserSubscriptionProcess::TYPE_1_DEDUCTED_SUCCESS, $reset_date, $next_billing_time);

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            Log::error('订阅扣款成功回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

            return false;
        }

        return true;
    }

    /**
     * 订阅周期扣款失败
     * @param  $third_trade_no
     * @return bool
     */
    public function deductionFailed($third_trade_no){
        $order = Order::getByTradeNo($third_trade_no);
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

                Log::info('订阅周期扣款失败', ['third_trade_no'=>$third_trade_no]);
                //扣款失败第二天重置资产
                $reset_date = Carbon::now()->addDay()->toDateString();

                UserSubscriptionProcess::add($order_goods->id, $user->id, UserSubscriptionProcess::TYPE_2_DEDUCTED_FAILED, $reset_date);

                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                Log::error('订阅周期扣款失败回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

                return false;
            }
        }

        return true;
    }

    /**
     * 取消订阅
     * @param  $third_trade_no
     * @return bool
     */
    public function cancelPlan($third_trade_no){
        $order = Order::getByTradeNo($third_trade_no);
        if($order->status == OrderGoods::STATUS_1_PAID){
            try{
                DB::beginTransaction();
                //修改状态为取消订阅
                $order->status = OrderGoods::STATUS_5_UNSUBSCRIBE;
                $order->save();

                $order_goods = OrderGoods::getByOrderId($order->id);
                $order_goods->status = OrderGoods::STATUS_5_UNSUBSCRIBE;
                $order_goods->save();

                Log::info('取消订阅回调增加待处理的取消订阅记录', ['third_trade_no'=>$third_trade_no]);
                $remain_service = new UserRemainService();
                $reset_date = $remain_service->getResetDate($order_goods);

                UserSubscriptionProcess::add($order_goods->id, $order_goods->user_id,UserSubscriptionProcess::TYPE_3_CANCEL_SUBSCRIPTION, $reset_date);

                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                Log::error('取消订阅回调处理失败', ['third_trade_id'=>$order->third_trade_no, 'message'=>$e->getMessage(), 'line'=>$e->getLine(), 'file'=>$e->getFile()]);

                return false;
            }
        }
        return true;
    }

    /**
     * 发送支付成功或者失败邮件
     * @param $email_name
     * @param $goods_no
     * @param $pay_time
     * @param $price
     * @param $combo
     * @param $user
     */
    public function sendPayEmail($email_name, $goods_no, $pay_time, $price, $combo, User $user){
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

        $data['info'] = str_replace("#@order_goods_no", $goods_no, $data['info']);

        $pay_time = Carbon::parse($pay_time)->format('Y/m/d H:i:s');
        $data['info'] = str_replace("#@pay_date", $pay_time, $data['info']);

        $data['info'] = str_replace("#@plan", $combo, $data['info']);

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

    /**
     * 新增订单缓存
     * @param $user_id
     * @param $goods_id
     * @param $data
     */
    public function addOrderCache($user_id, $goods_id, $data){
        $user = User::find($user_id);
        $key = md5($user->email . '-' . $user_id . '-' . $goods_id);
        Cache::put($key, $data, 3 * 60);
    }

    /**
     * 获取订单缓存
     * @param $user_id
     * @param $goods_id
     * @return mixed
     */
    public function getOrderCache($user_id, $goods_id){
        $user = User::find($user_id);
        $key = md5($user->email . '-' . $user_id . '-' . $goods_id);
        return Cache::get($key);
    }

    /**
     * 删除订单缓存
     * @param $user_id
     * @param $goods_id
     * @return bool
     */
    public function delOrderCache($user_id, $goods_id){
        $user = User::find($user_id);
        $key = md5($user->email . '-' . $user_id . '-' . $goods_id);
        return Cache::forget($key);
    }

    /**
     * 验证商品或者订阅
     * @param $goods_id
     * @param $user_id
     * @param $verify_sub
     * @return bool
     */
    public function verifyGoodsOrSub($goods_id, $user_id, $verify_sub){
        $goodsService = new GoodsService();
        $goods = $goodsService->findById($goods_id);

        //商品是否下架或者删除
        if(!$goods instanceof Goods || $goods->status == Goods::STATUS_0_INACTIVE || $goods->deleted == Goods::DELETE_1_YES){
            return self::INVALID_2_GOODS;
        }

        //商品分类是否存在
        $combo_id = $goods->level1;
        $gear_id = $goods->level2;
        $classify = Goodsclassification::getKeyById();
        $combo = array_get($classify, "$combo_id.title");
        $gear = array_get($classify, "$gear_id.title");

        if(!$combo || !$gear){
            return self::INVALID_3_GOODS_CLASSIFICATION;
        }

        //需要验证用户是否存在订阅
        if($verify_sub && $combo != Goods::COMBO_PACKAGE && OrderGoods::existsSubscriptionPlan($user_id)){
            return self::INVALID_4_SUB;
        }

        return self::INVALID_1_NULL;
    }
}