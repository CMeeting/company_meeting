<?php

namespace App\Console\Commands;

use App\Models\LicenseModel;
use App\Models\Mailmagicboard;
use App\Services\EmailService;
use App\Services\LicenseService;
use App\Services\OrdersService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PharIo\Manifest\License;

class ExpireLicenseCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:expire:license:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '序列码状态变更';

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
        \Log::info('-----序列码状态监听脚本开始执行-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);

        //正式序列码到期前45天发送内部提醒邮件并更新状态为即将到期
        $expire45 = Carbon::now()->addDays(45)->format('Y-m-d');
        $query = LicenseModel::whereDate('expire_time', $expire45)
            ->where('status', LicenseModel::LICENSE_STATUS_1_NORMAL)
            ->where('type', LicenseModel::LICENSE_TYPE_2_PAID);

        $expire45_data = $query->get()->toArray();
        (clone $query)->update(['status'=>LicenseModel::LICENSE_STATUS_4_EXPIRE_SOON]);
        \Log::info('到期前45天序列码', ['ids'=>array_column($expire45_data, 'id')]);
        //发送提醒邮件
        $this->sendEmail($expire45_data, 45);



        //正式序列码到期前30天发送内部提醒邮件
        $expire30 = Carbon::now()->addDays(30)->format('Y-m-d');
        $expire30_data = LicenseModel::whereDate('expire_time', $expire30)
            ->where('type', LicenseModel::LICENSE_TYPE_2_PAID)
            ->get()->toArray();
        //发送提醒邮件
        \Log::info('到期前30天序列码', ['ids'=>array_column($expire30_data, 'id')]);
        $this->sendEmail($expire30_data, 30);



        //试用序列码到期前15天发送内部提醒邮件并更新状态为即将到期
        $expire15 = Carbon::now()->addDays(15)->format('Y-m-d');
        $query = LicenseModel::whereDate('expire_time', $expire15)
            ->where('status', LicenseModel::LICENSE_STATUS_1_NORMAL)
            ->where('type', LicenseModel::LICENSE_TYPE_1_ON_TRIAL);

        $expire15_data = $query->get()->toArray();
        (clone $query)->update(['status'=>LicenseModel::LICENSE_STATUS_4_EXPIRE_SOON]);
        //发送提醒邮件
        \Log::info('到期前15天序列码', ['ids'=>array_column($expire15_data, 'id')]);
        $this->sendEmail($expire15_data,15);



        //试用序列码到期前7天发送内部提醒邮件
        $expire7 = Carbon::now()->addDays(7)->format('Y-m-d');
        $expire7_data = LicenseModel::whereDate('expire_time', $expire7)
            ->where('type', LicenseModel::LICENSE_TYPE_1_ON_TRIAL)
            ->get()->toArray();
        //发送提醒邮件
        \Log::info('到期前7天序列码', ['ids'=>array_column($expire7_data, 'id')]);
        $this->sendEmail($expire7_data,7);



        //序列码过期
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $query = LicenseModel::where('expire_time', '<=', $now)
            ->where('status', '!=', LicenseModel::LICENSE_STATUS_3_EXPIRE);

        $ids = $query->pluck('id');

        $query->update(['status'=>LicenseModel::LICENSE_STATUS_3_EXPIRE]);
        \Log::info('到期序列码', ['ids'=>$ids]);
        \Log::info('-----序列码过期状态改变脚本执行完成-----', ['start_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }

    public function sendEmail($license_data, $day){
        if(empty($license_data)){
            return;
        }

        $html='<table style="margin-top:0;">';
        $license_type = $license_type = config("constants.license_type");

        $license_service = new LicenseService();
        $goodsClassifications = $license_service->getGoodsClassifications();
        $i = 1;
        foreach ($license_data as $row){
            $name = $goodsClassifications[$row['products_id']] . " for " . $goodsClassifications[$row['platform_id']] . " ( " . $goodsClassifications[$row['licensetype_id']] . " ) ";
            $html.='<tr><td>序列码'.$i.':</td></tr>';
            $html.='<tr><td>客户公司名称：'.$row['company_name'].'</td></tr>';
            $html.='<tr><td>序列码类别：'.array_get($license_type, $row['type']).'</td></tr>';
            $html.='<tr><td>商品名称：'.$name.'</td></tr>';
            $html.='<tr><td>用户邮箱：'.$row['user_email'].'</td></tr>';
            $html.='<tr><td>创建时间：'.$row['created_at'].'</td></tr>';
            $html.='<tr><td>到期时间：'.$row['expire_time'].'</td></tr>';
            $html.='<tr><td>license_key：'.$row['license_key'].'</td></tr>';
            $i++;
        }
        $html.='</table>';

        $template = Mailmagicboard::getByName('序列码即将到期提醒');
        $data['title'] = $template->title;
        $data['info'] = $template->info;
        $data['title'] = str_replace("#@day", $day, $data['title']);
        $data['info'] = str_replace("#@day", $day, $data['info']);
        $data['info'] = str_replace("#@html", $html, $data['info']);

        $email = config('constants.license_notice_email');
        $emailService = new EmailService();
        $data['id'] = 66;
        $emailService->sendDiyContactEmail($data, 0, $email);
    }
}
