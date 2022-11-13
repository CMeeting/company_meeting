<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2019/8/1
 * Time: 16:48
 */

namespace app\api\biz;


use app\api\helper\EmailTemplate;
use app\api\model\App;
use app\api\model\Coupon;
use app\api\model\Crontab;
use app\api\model\Device;
use app\api\model\LicenseCode;
use app\api\model\Member;
use app\api\model\Order;
use app\api\model\Product;
use app\api\model\Subscription;
use app\api\service\SysConf;
use app\api\view\filmage_email_temp\discountCoupon;
use app\api\view\filmage_email_temp\expireCoupon;
use core\helper\ArrayHelper;
use core\helper\CsvHelper;
use core\helper\JsonHelper;
use core\helper\LogHelper;
use core\helper\StringHepler;
use core\helper\SysHelper;
use core\RedisModel;
use core\Register;
use PHPMailer\PHPMailer\PHPMailer;
use think\Db;
use think\facade\Log;

class EmailBiz
{
    private static $instanse = null;
    private static $mpdf = null;
    const RETRIEVAL_EMAIL = 'retrieval_email:';
    /**
     * @param $trand_no
     * @throws \Mpdf\MpdfException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function genInvoiceFile($subs_id)
    {
        if (empty($subs_id)) {
            return "";
        }
        $logPrefix = "subs ". $subs_id ." genInvoice";
        LogHelper::logEmail($logPrefix.'begin');
        $data = Order::findProductByTrandNo($subs_id);
        if (!isset($data)) {
            $data = Order::findProductByTrandNo(Subscription::value('last_sub_id', ['id' => $subs_id]));
        }
        if (self::$mpdf == null) {
            self::$mpdf = new  \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [210, 297], 'tempDir' => SysHelper::getSysConf('pdf_root').'/'.$subs_id]);
        }
        //加载要生成pdf的文件
        //设置pdf显示方式
        LogHelper::logEmail('CREATED INVOICE START');
        self::$mpdf->SetDisplayMode('fullwidth');
        self::$mpdf->autoScriptToLang = true;
        self::$mpdf->autoLangToFont = true;
        self::$mpdf->WriteHTML(EmailTemplate::invoice($data));
        self::$mpdf->Output(SysHelper::getSysConf('pdf_root') . $subs_id.'/invoice.pdf'); //保存至当前file文件夹下
        LogHelper::logEmail('CREATED INVOICE END');
        $fileUrl = SysHelper::getSysConf('pdf_root') . $subs_id . '/invoice.pdf';
        if (file_exists($fileUrl)) {
            LogHelper::logEmail($logPrefix.'success '.$fileUrl);
            return $fileUrl;
        }else{
            LogHelper::logEmail($logPrefix.'error '.$fileUrl, LogHelper::LEVEL_ERROR);
            return "";
        }
    }

    /**
     * @param $email
     * @param $title
     * @param $target_id
     * @param $target_type
     * @param $body
     * @param string $product_name
     * @param array $paths
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function sendEmail($email, $title, $target_id, $target_type, $body, $product_name = '', $paths = []): bool
    {
        if (strpos(strtolower($email), 'test') === 0) {
            LogHelper::logEmail($email . ' sendEmail Skip '.$target_type.' email, ' . $title, LogHelper::LEVEL_WARN);
            return 'Skip test';
        }
        LogHelper::logEmail($email . ' sendEmail begin, ' . $title . ',id :' . $target_id . ',type:' . $target_type);
        // $sosNoticeEmail = SysConf::getValue('sos_notice_emails');
        if (empty($product_name)){
            $event = array_flip(SysHelper::getSysConf('email_event'))[$title] ?? $title;
        }else{
            $event = $title;
            if (strpos($product_name, 'Filmage Screen Pro') !== false) {
                $product_name = str_replace('Filmage Screen Pro', 'Filmage Screen', $product_name);
            }
            if (strpos($product_name, 'Filmage Editor') !== false) {
                $product_name = '';
            }
            $title = SysHelper::getSysConf('email_event')[$title] . $product_name;
        }
        // if (!SysHelper::isEnv('production') && !empty($target_id)) $title .= ' ' . SysHelper::getEnv('env');
        $params = ['email' => $email, 'title' => $title];
        Crontab::insertCrontab($event, Crontab::$statuses['created'], $target_id, $target_type, $params);

        //抽出发送邮件代码
        $email_config = !empty(Register::get('email_config')) ? Register::get('email_config') : 'email';
        LogHelper::logEmail('config：'. json_encode($email_config));
        $res = self::send(SysHelper::getEnv($email_config), $email, $title, $body, $paths);
        $status = $res['code'] ? Crontab::$statuses['success'] : Crontab::$statuses['failed'];

        Crontab::update(['status' => $status, 'result' => $res['msg']], 'status != 1 AND event =\'' . $event . '\' AND target_id =\'' . $target_id . '\'');
//        if (!$res['code'] && SysHelper::isEnv('production')) {
//            $sos_title = '发送给 ' . $email . ' 的' . $title . '失败,请处理(' . SysHelper::getEnv('env') . ')';
//            self::send(SysHelper::getEnv('sos_email'), $sosNoticeEmail, $sos_title, $body);
//        }
        return $res['code'];
    }


    /**
     * @param $config
     * @param $email
     * @param $title
     * @param $body
     * @param array $paths
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function send($config, $email, $title, $body, $paths = []): array
    {
        LogHelper::logEmail($email . " sendEm begin");
        if (!SysHelper::isEnv('production')){
            $title .= ' ' . SysHelper::getEnv('env') . ' ' .SysHelper::getEnv('server_mode');
        }
        $emails = explode(',', $email);
        if (self::$instanse == null) {
            self::$instanse = new PHPMailer();
            self::$instanse->IsSMTP();
            self::$instanse->CharSet = "UTF-8";
            self::$instanse->Encoding = 'base64';
            self::$instanse->SMTPAuth = true;
            self::$instanse->SMTPDebug = 0; //是否调试
            if (strpos($config['host'], 'zoho') && strpos($config['username'], 'pdfreaderpro.com')) {
                self::$instanse->SMTPSecure = 'ssl';
                self::$instanse->AuthType = 'LOGIN';
            }
            self::$instanse->Host = $config['host']; //host
            self::$instanse->Port = $config['port']; //端口
            self::$instanse->Username = $config['username']; //发件人
            self::$instanse->Password = $config['password']; //发件人专用密码
            self::$instanse->SetFrom($config['username']); //发件人邮箱和名称
        }
        self::$instanse->Subject = $title; //标题
        self::$instanse->Body = $body; //内容

        if (!empty($paths)) {
            if (is_string($paths)) {
                self::$instanse->addAttachment($paths);
            } elseif (is_array($paths)) {
                foreach ($paths as $path) {
                    self::$instanse->addAttachment($path);
                }
            }
        }
        self::$instanse->IsHTML(true); //是否启用html
        foreach ($emails as $val) {
            self::$instanse->AddAddress(trim($val)); //收件用户
        }
        $bool['code'] = self::$instanse->Send();
        $bool['msg'] = self::$instanse->ErrorInfo;
        LogHelper::logEmail('host:'.$config['host'] . ' port ' . $config['port']);
        if ($bool['code']) {
            LogHelper::logEmail($email . ' sendEm end: ' . $title . ', ' . JsonHelper::encode($bool), LogHelper::LEVEL_INFO);
        } else {
            LogHelper::logEmail($email . " sendEm error: " . JsonHelper::encode($bool).', '.$config['username'], LogHelper::LEVEL_ERROR);
        }

        return $bool;
    }

    /**
     * @param $title
     * @param $body
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\exception\DbException
     */
    public static function sendAdminManagerEmail($title, $body)
    {
        EmailBiz::send(SysHelper::getEnv('sos_email'), SysConf::getValue('sos_notice_emails'), $title, $body);
    }
    /**
     * @param $subs_id
     * @throws \Mpdf\MpdfException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function sendLicenseEmail($subs_id)
    {
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncSendLicenseEmail', 'target_id' => $subs_id]);
        $sub_info = Subscription::findById($subs_id);
        $product = Product::findById($sub_info['product_id']);
        $full_name = Member::value('full_name', ['email' => $sub_info['email']]);
        $arr = LicenseCode::selectBySubsId($subs_id);
        if (!empty($arr)) {
            $cdkey = implode(',', array_column($arr, 'cdkey'));
            $file = EmailBiz::getFileByCdkeyNum($cdkey, $subs_id);
            self::setEmailConfig($product['code']);
            if (EmailTemplate::verifyProduct($product['code'])) {
                $title = 'sendRenewLicenseEmail';
            } else {
                $title = 'sendLicenseEmail';
            }
            EmailBiz::sendEmail($sub_info['email'], $title, $subs_id, Subscription::$table, EmailTemplate::sendLicense($product['code'], $full_name, $cdkey), $product['name'], $file);
        } else {
            LogHelper::logEmail('sendLicenseEmail data exception,sub_id:' . $subs_id, LogHelper::LEVEL_ERROR);
            self::sendAdminManagerEmail('sendLicenseEmail data exception','sub_id: ' . $subs_id);
        }
    }

    /**
     * @param $subs_id
     * @throws \Mpdf\MpdfException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function sendBundleLicenseEmail($subs_id)
    {
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncSendLicenseEmail', 'target_id' => $subs_id]);
        $sub_info = Subscription::findById($subs_id);
        $product = Product::findById($sub_info['product_id']);
        $full_name = Member::value('full_name', ['email' => $sub_info['email']]);
        $sub = Subscription::select(['last_sub_id' => $subs_id]);
        $arr = LicenseCode::whereInSelect('subscription_id',array_column($sub,'id'));
        if (!empty($arr)) {
            foreach ($arr as $item) {
                $cdkey[Product::value('name', ['id' => $item['product_id']])] = $item['cdkey'];
            }
            $file = EmailBiz::getFileByCdkeyNum($cdkey, $subs_id);
            self::setEmailConfig($product['code']);
            if (EmailTemplate::verifyBundleProduct($product['code'])) {
                $title = 'Filmage Editor + Filmage Screen';
            } else {
                $title = 'Filmage Editor + Filmage Converter';
            }
            EmailBiz::sendEmail($sub_info['email'], $title, $subs_id, Subscription::$table, EmailTemplate::sendBundleLicense($product['code'], $full_name, $cdkey), $product['name'], $file);
        } else {
            LogHelper::logEmail('sendBundleLicenseEmail data exception,sub_id:' . $subs_id, LogHelper::LEVEL_ERROR);
            self::sendAdminManagerEmail('sendBundleLicenseEmail data exception','sub_id: ' . $subs_id);
        }
    }

    public static function sendRenewLicenseEmail($subs_id) {
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncSendLicenseEmail', 'target_id' => $subs_id]);
        $sub_info = Subscription::findById($subs_id);
        $product = Product::findById($sub_info['product_id']);
        $full_name = Member::value('full_name', ['email' => $sub_info['email']]);
        $arr = LicenseCode::selectBySubsId($subs_id);
        if (!empty($arr)) {
            $cdkey = implode(',', array_column($arr, 'cdkey'));
            $file = EmailBiz::getFileByCdkeyNum($cdkey, $subs_id);
            self::setEmailConfig($product['code']);
            EmailBiz::sendEmail($sub_info['email'], 'sendRenewLicenseEmail', $subs_id, Subscription::$table, EmailTemplate::sendRenewLicense($product['code'], $full_name, $cdkey), $product['name'], $file);
        } else {
            LogHelper::logEmail('sendRenewLicenseEmail data exception,sub_id:' . $subs_id, LogHelper::LEVEL_ERROR);
            self::sendAdminManagerEmail('sendRenewLicenseEmail data exception','sub_id: ' . $subs_id);
        }
    }

    /**
     * @param $param
     * @throws \Mpdf\MpdfException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function findLicenseEmail($param)
    {
        if (empty($param)) return;
        $logPrefix = $param['email']." findLicense ";
        LogHelper::logEmail($logPrefix."begin");
        $subsData = Order::findByThirdOrderNo($param);


        if (empty($subsData) && isset($param['email'])) {
            $subsData = LicenseCode::findByMemberEmail($param['email'],$param);
        }
        $app_code = App::value('code',['id'=>$param['app_id']]);
        $target_id = isset($param['third_order_no']) ? $subsData[0]['order_id'] : $subsData[0]['member_id'];
        $target_type = isset($param['third_order_no']) ? 'orders' : 'members';
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncFindLicenseEmail', 'target_id' => $target_id]);

        if (!empty($subsData)) {
            if (strpos($app_code, 'pdfreaderpro') === false) {
                foreach ($subsData as $key => $val) {
                    if (strpos(Product::value('code', ['id' => $val['pid']]), $app_code) === false) {
                        unset($subsData[$key]);
                    }
                }
             $subsData= array_values($subsData);
            }
            if (strpos($app_code, 'filmage.pro-lite') !== false) {
                foreach ($subsData as &$val) {
                    $time = Device::value('end_up_at', ['subscription_id' => $val['id']]);
                    $end_up_at = strtotime($time);
                    $val['device_status'] = $time === null ? 'Unactivated' : ($end_up_at < time() ? 'Expired' : ($end_up_at < strtotime('2099-12-31') ? 'Will expire on '.date('d/m/Y', $end_up_at) : 'Permenant License'));
                }
                $cdkey = array_column($subsData,'device_status','cdkey');
            } else {
                $cdkey = implode(',', array_column($subsData, 'cdkey'));
            }
            $product = Product::findById($subsData[0]['pid']);

            $file = EmailBiz::getFileByCdkeyNum($cdkey, $subsData[0]['id']);
            self::setEmailConfig($product['code']);
            if (EmailTemplate::validProduct($product['code'])) {
                $title = 'findLicenseEmail';
            } elseif (EmailTemplate::verifyProduct($product['code'])){
                $title = 'sendFindLicenseEmailFilmageEditor';
            } else {
                $title = 'findFilmageLicenseEmail';
            }
            EmailBiz::sendEmail($subsData[0]['email'], $title, $target_id, $target_type, EmailTemplate::findLicense($product['code'], $cdkey, $subsData[0]), $product['name'], $file);
            LogHelper::logEmail($logPrefix."end");
        }else{
            LogHelper::logEmail($logPrefix.'data exception,subsData:' . json_encode($subsData), LogHelper::LEVEL_ERROR);
            self::sendAdminManagerEmail($logPrefix.'data exception','param: ' . $param);
        }
    }

    /**
     * @param $id
     * @throws \Mpdf\MpdfException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function asyncRemindActivateEmail($id)
    {
        $data = LicenseCode::findEmailById($id);
        $subsId = $data['subscription_id'];
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncRemindActivateEmail', 'target_id' => $subsId]);
        $cdkeys = LicenseCode::getCdkeyByEmail($id, $data);
        $cdkey = implode(' , ', array_column($cdkeys, 'cdkey'));
        if (!empty($cdkey)) {
            $product = Product::findById($data['product_id']);
            $file = EmailBiz::getFileByCdkeyNum($cdkey, $subsId);
            self::setEmailConfig($product['code']);
            if (EmailTemplate::validProduct($product['code'])) {
                $title = 'remindActivateEmail';
            } elseif (EmailTemplate::verifyProduct($product['code'])) {
                $title = 'remindActivateEmailFilmageEditor';
            } else {
                $title = EmailTemplate::getTitleByCode($product['code'], 'remindFilmageActivateEmail');
                $product['name'] = '';
            }
            EmailBiz::sendEmail($data['email'], $title, $subsId, Subscription::$table, EmailTemplate::remindActivate($product['code'], $data['full_name'], $cdkey), $product['name'], $file);
            }
    }

    /**
     * @param $subs_id
     * @throws \Mpdf\MpdfException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function upgradeLicenseEmail($subs_id)
    {
        if (empty($subs_id)) return;
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncUpgradeLicenseEmail', 'target_id' => $subs_id]);
        $file = EmailBiz::genInvoiceFile($subs_id);
        $sub_info = Subscription::findById($subs_id);
        $product = Product::findById($sub_info['product_id']);
        $full_name = Member::value('full_name', ['email' => $sub_info['email']]);

        self::setEmailConfig($product['code']);
        EmailBiz::sendEmail($sub_info['email'], 'upgradeLicenseEmail', $subs_id, Subscription::$table, EmailTemplate::upgradeLicense($product['code'], $full_name), $product['name'], $file);
    }

    /**
     * @param $subs_id
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \think\exception\DbException
     */
    public static function getInvoiceUrl($subs_id)
    {
        $order = Order::find(['subscription_id' => $subs_id]);
        if (!$order) {
            $last_sub_id = Subscription::value('last_sub_id',['id' => $subs_id]);
            if ($last_sub_id) {
                $order = Order::find(['subscription_id' => $last_sub_id]);
            }
        }
        $fileUrl = SysHelper::getSysConf('pdf_root') . $subs_id . '/invoice.pdf';
        if ($order && !file_exists($fileUrl)) {
            $fileUrl = EmailBiz::genInvoiceFile($subs_id);
        }
        return $fileUrl;
    }

    /**
     * @param $cdkey
     * @param $subs_id
     * @param $product_code
     * @return array
     * @throws \Mpdf\MpdfException
     * @throws \think\exception\DbException
     */
    public static function getFileByCdkeyNum($cdkey, $subs_id): array
    {
        $files[] = self::getInvoiceUrl($subs_id);
        if (is_array($cdkey) == true) {
            $count = 5;
        } else {
            $cdkey = explode(',', $cdkey);
            $count = 10;
        }
        if (count($cdkey) > $count) {
            foreach ($cdkey as $k => $item) {
                $data[$k][] = $item;
            }
            $url = CsvHelper::write_csv($data, SysHelper::getSysConf('pdf_root') . $subs_id);
            array_push($files, $url);
        }
        return $files;
    }

    /**
     * @param $coupon
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function receiveCoupon($coupon)
    {
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncReceiveCoupon', 'target_id' => $coupon['id']]);
        $email = Member::findById($coupon['member_id']);
        $emailTemp = discountCoupon::html($email['full_name'], $coupon, $email['email']);
        $product = Product::findById($coupon['product_id']);
        EmailBiz::sendEmail($email['email'], 'receiveCoupon', $coupon['id'], Coupon::$table, $emailTemp, $product['name']);
    }

    /**
     * @param $coupon
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     */
    public static function almostExpiredCoupon($coupon,$code)
    {
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncAlmostExpiredCoupon', 'target_id' => $coupon['id']]);
        $email = Member::findById($coupon['member_id']);
        $product = Product::findById($coupon['product_id']);
        $title = EmailTemplate::getTitleByCode($product['code'], 'almostExpireCoupon');
        $emailTemp = expireCoupon::html($email['full_name'],$coupon,$email['email'],$code);
        EmailBiz::sendEmail($email['email'], $title, $coupon['id'], Coupon::$table, $emailTemp);
    }

    /**
     * 根据产品选择发送邮箱
     * @param $product_code
     */
    public static function setEmailConfig($product_code)
    {
        if (EmailTemplate::validProduct($product_code)) {
            Register::set('email_config', 'email');
        } elseif (EmailTemplate::vaildFilmage($product_code)) {
            Register::set('email_config', 'filmage_email');
        }
    }

    public static function vaildretrievalEmail($email)
    {
        $bool = RedisModel::model()->get(self::RETRIEVAL_EMAIL . $email);
        if (!$bool) {
            RedisModel::model()->set(self::RETRIEVAL_EMAIL . $email, 1,60);
        }
        return $bool;
    }

    public static function SendRemindRenewEmail($subs_id) {
        Crontab::update(['status' => Crontab::$statuses['success']], ['event' => 'asyncSendRemindRenewEmail', 'target_id' => $subs_id]);
        $sub_info = Subscription::findById($subs_id);
        $product = Product::findById($sub_info['product_id']);
        $uuid = Device::value('unique_sn',['subscription_id' => $subs_id]);
        $full_name = Member::value('full_name', ['email' => $sub_info['email']]);
        $arr = LicenseCode::selectBySubsId($subs_id);
        if (!empty($arr)) {
            $cdkey = implode(',', array_column($arr, 'cdkey'));
            $file = EmailBiz::getFileByCdkeyNum($cdkey, $subs_id);
            self::setEmailConfig($product['code']);
            EmailBiz::sendEmail($sub_info['email'], 'sendRemindRenewEmail', $subs_id, Subscription::$table, EmailTemplate::sendRemindRenewEmail($product['code'], $full_name, $cdkey, $uuid), $product['name'], $file);
        } else {
            LogHelper::logEmail('SendRemindRenewEmail data exception,sub_id:' . $subs_id, LogHelper::LEVEL_ERROR);
            self::sendAdminManagerEmail('SendRemindRenewEmail data exception','sub_id: ' . $subs_id);
        }
    }
}