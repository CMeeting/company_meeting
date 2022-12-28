<?php

namespace App\Console\Commands;

use App\Models\Mailmagicboard;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\User;
use App\Services\EmailService;
use App\Services\OrdersService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnpaidOrderNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:unpaid:order:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单提醒邮件';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //订单待支付提醒邮件
        \Log::info('-----待付款邮件发送脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
        $three_arr = $seven_arr = [];

        $email_service = new EmailService();
        $unpaid_tree = Mailmagicboard::find(61);
        $unpaid_seven = Mailmagicboard::find(62);

        $order_service = new OrdersService();
        $goods_class = $order_service->assembly_orderclassification();

        //精确到小时，每小时跑一次脚本，防止重复发送
        $now = Carbon::now()->format('Y-m-d H');
        //去除八天以前的，减少数量
        $end_date = Carbon::now()->subDays(8)->format('Y-m-d H:i:s');
        $orders = Order::where('status', Order::STATUS_0_UNPAID)->where('created_at', '>=', $end_date)->get()->toArray();
        foreach ($orders as $order){
            $order_id = $order['id'];
            $created_at = $order['created_at'];
            $three_at = Carbon::parse($created_at)->addDays(3)->addHour()->format('Y-m-d H');
            $seven_at = Carbon::parse($created_at)->addDays(7)->addHour()->format('Y-m-d H');

            //发送订单待付款第三天邮件
            if($three_at == $now || $seven_at == $now){
                if($three_at == $now){
                    $three_arr[] = $order_id;
                    $email_template = $unpaid_tree;
                }else{
                    $seven_arr[] = $order_id;
                    $email_template = $unpaid_seven;
                }

                $user = User::find($order['user_id']);
                $goods_data = \DB::table("orders_goods as o")
                    ->leftJoin("goods as g","o.goods_id",'=','g.id')
                    ->whereRaw("o.order_id='{$order_id}'")
                    ->selectRaw("o.*,g.level1,g.level2,g.level3,g.price as goodsprice")
                    ->get()
                    ->toArray();
                foreach ($goods_data as $value){
                    $value = collect($value)->toArray();
                    $data['id'] = $email_template->id;
                    $data['title'] = $email_template->title;
                    $data['title'] = str_replace('#@order_no', $order['order_no'], $data['title']);

                    $data['info'] = $email_template->info;
                    $data['info'] = str_replace('#@full_name', $user->full_name, $data['info']);

                    $url = env('WEB_HOST') . '/personal/orders/checkout?order_id=' . $order['id'] . '&type=1';
                    $data['info'] = str_replace('#@url', $url, $data['info']);

                    $data['info'] = str_replace('#@order_no', $order['order_no'], $data['info']);

                    $product = $goods_class[$value['level1']]['title'] ." for ". $goods_class[$value['level2']]['title'] ." (". $goods_class[$value['level3']]['title'].")";
                    $data['info'] = str_replace('#@product', $product, $data['info']);

                    if($value['pay_years'] > 1){
                        $unity = 'Years';
                    }else{
                        $unity = 'Year';
                    }
                    $pay_years = $value['pay_years'] . $unity;
                    $data['info'] = str_replace('#@pay_years', $pay_years, $data['info']);

                    $subtotal = '$' . $value['price'];
                    $data['info'] = str_replace('#@subtotal', $subtotal, $data['info']);

                    $tax = '$0.00';
                    $data['info'] = str_replace('#@tax', $tax, $data['info']);

                    $total_amount = '$' . $value['price'];
                    $data['info'] = str_replace('#@total_amount', $total_amount, $data['info']);

                    $paid_price = '$0.00';
                    $data['info'] = str_replace('#@paid_price', $paid_price, $data['info']);

                    $balance_due = '$' . $value['price'];
                    $data['info'] = str_replace('#@balance_due', $balance_due, $data['info']);
                    $email_service->sendDiyContactEmail($data, 0, $user->email);
                }
            }
        }

        \Log::info('待付款发送邮件订单', ['three'=>$three_arr, 'seven'=>$seven_arr]);

        \Log::info('-----待付款邮件发送脚本执行完成-----', ['end_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
