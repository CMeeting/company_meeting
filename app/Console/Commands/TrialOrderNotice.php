<?php

namespace App\Console\Commands;

use App\Models\Mailmagicboard;
use App\Models\Order;
use App\Models\User;
use App\Services\EmailService;
use App\Services\OrdersService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TrialOrderNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:trial:order:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '试用订单到期提醒';

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
        \Log::info('-----试用订单到期提醒邮件发送脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
        $order_id_arr = ['week'=>[], 'three'=>[], 'one'=>[], 'last'=>[]];

        $email_service = new EmailService();

        $order_service = new OrdersService();
        $goods_class = $order_service->assembly_orderclassification();
        //去除一个月以前的，缩小范围
        $end_at = Carbon::now()->subDays(31)->format('Y-m-d H:i:s');
        $orders = Order::where('details_type', Order::DETAILS_STATUS_1_TRIAL)->where('created_at', '>=', $end_at)->get()->toArray();

        //精确到小时，每小时跑一次脚本，防止重复发送
        $now = Carbon::now()->format('Y-m-d H');
        foreach ($orders as $order){
            $send_email = false;
            $order_id = $order['id'];
            $created_at = $order['created_at'];
            $user = User::find($order['user_id']);
            //加一个小时 14:59购买，应该是三点钟提醒
            $week_at = Carbon::parse($created_at)->addDays(23)->addHour()->format('Y-m-d H');
            $three_at = Carbon::parse($created_at)->addDays(27)->addHour()->format('Y-m-d H');
            $one_at = Carbon::parse($created_at)->addDays(29)->addHour()->format('Y-m-d H');
            $last_at = Carbon::parse($created_at)->addDays(30)->addHour()->format('Y-m-d H');

            if($week_at == $now){
                $send_email = true;
                $order_id_arr['week'][] = $order_id;
                $template = Mailmagicboard::find(35);
            }elseif($three_at == $now){
                $send_email = true;
                $order_id_arr['three'][] = $order_id;
                $template = Mailmagicboard::find(36);
            }elseif($one_at == $now){
                $send_email = true;
                $order_id_arr['one'][] = $order_id;
                $template = Mailmagicboard::find(37);
            }elseif($last_at == $now){
                $send_email = true;
                $order_id_arr['last'][] = $order_id;
                $template = Mailmagicboard::find(38);
            }

            if($send_email){
                $goods_data = \DB::table("orders_goods as o")
                    ->leftJoin("goods as g","o.goods_id",'=','g.id')
                    ->whereRaw("o.order_id='{$order_id}'")
                    ->selectRaw("o.*,g.level1,g.level2,g.level3,g.price as goodsprice")
                    ->get()
                    ->toArray();

                foreach ($goods_data as $goods){
                    $goods = collect($goods)->toArray();
                    $product_id = $goods['level1'];
                    $platform_id = $goods['level2'];
                    $license_id = $goods['level3'];
                    $url = env('WEB_HOST') . "/order/product?productsid=$product_id&platformid=$platform_id&licensieid=$license_id";
                    $data['id'] = $template->id;
                    $data['title'] = $template->title;
                    $data['info'] = $template->info;
                    $data['info'] = str_replace('#@url', $url, $data['info']);
                    $email_service->sendDiyContactEmail($data, 0, $user->email);
                }
            }
        }

        print_r($order_id_arr);
        \Log::info('试用提醒订单', $order_id_arr);

        \Log::info('-----试用订单到期提醒邮件发送脚本执行完成-----', ['end_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
