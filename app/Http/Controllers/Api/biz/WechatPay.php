<?php

/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

namespace App\Http\Controllers\Api\biz;


use App\Http\extend\core\helper\SysHelper;
use QRcode;
use App\Http\extend\wechat\example\WxPayConfig;
use App\Http\extend\wechat\lib\WxPayApi;
use App\Http\extend\wechat\lib\WxPayBizPayUrl;
use App\Http\extend\wechat\lib\WxPayCloseOrder;
use App\Http\extend\wechat\lib\WxPayOrderQuery;
use App\Http\extend\wechat\lib\WxPayUnifiedOrder;

require_once dirname(dirname(dirname(__DIR__))) . '/extend/wechat/example/phpqrcode/phpqrcode.php';
require_once dirname(dirname(dirname(__DIR__))) . '/extend/wechat/lib/WxPay.Data.php';
class WechatPay
{
    /**
     * @param $trade_no
     * @return bool|\wechat\lib\成功时返回，其他抛异常
     * @throws \WxPayException
     */
    public static function findOrder($trade_no)
    {
        $config = new WxPayConfig();
        $wx = new WxPayOrderQuery();
        $wx->SetAppid($config->GetAppId());
        $wx->SetMch_id($config->GetMerchantId());
        $wx->SetOut_trade_no($trade_no);
        $wxData = WxPayApi::orderQuery($config, $wx);
        if ($wxData['result_code'] == 'SUCCESS' && $wxData['return_code'] == 'SUCCESS') {
            return $wxData;
        } else {
            return false;
        }
    }

    /**
     * @param $trade_no
     * @return bool
     * @throws \WxPayException
     */
    public static function close($trade_no)
    {
        $config = new WxPayConfig();
        $wx = new WxPayCloseOrder();
        $wx->SetAppid($config->GetAppId());
        $wx->SetMch_id($config->GetMerchantId());
        $wx->SetOut_trade_no($trade_no);
        $wxData = WxPayApi::closeOrder($config, $wx);
        if ($wxData['result_code'] == 'SUCCESS' && $wxData['return_code'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param $trade_no
     * @param $product
     * @param $price
     * @param $call_back
     * @return string
     */
    public static function wechatPay($trade_no, $product, $price, $call_back)
    {
        $input = new WxPayUnifiedOrder();
        $input->SetBody($product);
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($price * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetGoods_tag($product);
        $input->SetNotify_url($call_back . '/api/wechatNotify');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("01");

        $result = WechatPay::GetPayUrl($input);
        $url = urldecode($result['code_url']);
        $pay_url_data['id'] = $result['prepay_id'];
        if (substr($url, 0, 6) == "weixin") {
            if (!file_exists(public_path()."/wxpay_qrcode_folder/")) mkdir(public_path()."/wxpay_qrcode_folder/", 0777);
            $img_url = public_path()."/wxpay_qrcode_folder/" . $trade_no . '.png';
            QRcode::png($url, $img_url);
            if (!file_exists($img_url)) error('wechat_pay', 'wechat_pay_error');
            $pay_url_data['url'] = self::headerurl() . '/wxpay_qrcode_folder/' . $trade_no . '.png';
            return $pay_url_data;
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
    static function headerurl(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return  $http_type . $_SERVER['HTTP_HOST'];
    }

    /**
     * @param $productId
     * @return string
     * @throws \WxPayException
     */
    public function GetPrePayUrl($productId)
    {
        $biz = new WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        try{
            $config = new WxPayConfig();
            $values = WxpayApi::bizpayurl($config, $biz);
        } catch(Exception $e) {
            Log::ERROR(json_encode($e));
        }
        $url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
        return $url;
    }

    /**
     *
     * 参数数组转换为url参数
     * @param array $urlObj
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&"); //移除字符串两侧的&字符
        return $buff;
    }

    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param UnifiedOrderInput $input
     */
    public static function GetPayUrl($input)
    {

        if ($input->GetTrade_type() == "NATIVE") {
            try{
                $config = new WxPayConfig();
                $result = WxPayApi::unifiedOrder($config, $input);
                return $result;
            } catch(Exception $e) {

                Log::ERROR(json_encode($e));
            }
        }
        return false;
    }
}
