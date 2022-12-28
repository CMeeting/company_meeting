<?php

namespace App\Console\Commands;

use App\Models\Mailmagicboard;
use App\Models\OrderGoods;
use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OrderRenewNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:order:renew:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单到期前续订提醒';

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
        \Log::info('-----订单到期续订提醒邮件发送脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
        $email_service = new EmailService();
        $alert_id = ['month'=>[], 'ten'=>[], 'one'=>[]];
        $order_goods = OrderGoods::with('order')->where('status', 1)->where('details_type', '!=', 1)->get()->toArray();
        foreach ($order_goods as $order_good){
            $send_mail = false;
            $order_id = $order_good['id'];
            $order_no = $order_good['order']['order_no'];
            $created_at = $order_good['created_at'];
            $year = $order_good['pay_years'];
            $end_at = Carbon::parse($created_at)->addYears($year);

            $month_at = (clone $end_at)->subMonth()->format('Y-m-d H');
            $ten_at = (clone $end_at)->subDays(10)->format('Y-m-d H');
            $one_at = (clone $end_at)->subDay()->format('Y-m-d H');

            $now = Carbon::now()->format('Y-m-d H');

            $user = User::find($order_good['user_id']);
            if($month_at == $now){
                $send_mail = true;
                $alert_id['month'][] = $order_id;
                $template = Mailmagicboard::find(42);
            }elseif($ten_at == $now){
                $send_mail = true;
                $alert_id['ten'][] = $order_id;
                $template = Mailmagicboard::find(43);
            }elseif($one_at == $now){
                $send_mail = true;
                $alert_id['ten'][] = $order_id;
                $template = Mailmagicboard::find(44);
            }

            if($send_mail){
                $data['title'] = $template->title;
                $data['title'] = str_replace('#@order_no', $order_no, $data['title']);

                $data['info'] = $template->info;
                $data['info'] = str_replace('#@order_no', $order_no, $data['info']);
                $data['info'] = str_replace('#@end_at', $end_at->format('Y-m-d'), $data['info']);
                $data['info'] = str_replace('#@email', $user->email, $data['info']);
                $url = env('WEB_HOST') . '/login';
                $data['info'] = str_replace('#@url', $url, $data['info']);

                $email_service->sendDiyContactEmail($data, 0, $user->email);
            }
        }

        \Log::info('提醒订单#子订单号', $alert_id);
        \Log::info('-----订单到期续订提醒邮件发送脚本执行完成-----', ['end_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
