<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\SaaSOrderService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $order_id;

    /**
     * Create a new job instance.
     *
     * @param $order_id
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('订单关闭队列开始执行', ['order_id'=>$this->order_id]);
        $order = Order::find($this->order_id);
        $order_goods = OrderGoods::getByOrderId($order->id);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        //删除订单缓存
        $service = new SaaSOrderService();
        $service->delOrderCache($order_goods->user_id, $order_goods->goods_id);

        if($order->status == OrderGoods::STATUS_0_UNPAID){
            \Log::info('订单未支付-订单开始关闭', ['order_id'=>$this->order_id]);
            try{
                \DB::beginTransaction();
                $order->status = OrderGoods::STATUS_4_CLOSE;
                $order->closetime = $now;
                $order->save();

                if($order_goods instanceof OrderGoods){
                    $order_goods->closetime = $now;
                    $order_goods->status = OrderGoods::STATUS_4_CLOSE;
                    $order_goods->save();
                }
                \DB::commit();

                \Log::info('订单未支付-订单关闭成功', ['order_id'=>$this->order_id]);
            }catch (\Exception $e){
                \DB::rollBack();

                \Log::info('订单未支付-订单关闭失败', ['order_id'=>$this->order_id, 'message'=>$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()]);
            }
        }
    }
}
