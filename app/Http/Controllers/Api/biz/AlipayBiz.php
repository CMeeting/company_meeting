<?php


namespace App\Http\Controllers\Api\biz;


use alipay\aop\request\AlipayTradeCloseRequest;
use AlipayTradePrecreateRequest;
use AlipayTradeQueryRequest;
use AopClient;
use App\Http\extend\core\helper\JsonHelper;
use core\helper\LogHelper;
use core\helper\SysHelper;
use QRcode;
use think\Exception;

require_once dirname(dirname(dirname(__DIR__))) . '/extend/alipay/aop/AopClient.php';
require_once dirname(dirname(dirname(__DIR__))) . '/extend/alipay/aop/request/AlipayTradeQueryRequest.php';
require_once dirname(dirname(dirname(__DIR__))) . '/extend/alipay/aop/request/AlipayTradePrecreateRequest.php';
require_once dirname(dirname(dirname(__DIR__))) . '/extend/wechat/example/phpqrcode/phpqrcode.php';

class AlipayBiz
{

    const PAYGA_GEWAY = 'https://mapi.alipay.com/gateway.do';
    const GATE_WAY_URL = 'https://openapi.alipay.com/gateway.do';
    const public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAj6ORX6PqMsu5cnWt+MW43i/odmlDK1KL791jvx/BI8mqXQ5exnTN8o3MXznbudENQSNK9NGGLJuVOQem7c9ycZyQR/MilmQtiGAKWckhJdOFXPRY9iR/uyUI50n8u/usFi0ecdF73ncARxxjKVbOfWvq3FTm3E9TvyEaFJM5VVfxEvD3bii73QLq58dxc8YpxPZ8TawtIHLPQFsu5Bpc1RsZFJ/pDR07wp86PakVQsmUx6fcCWsjBZekmhxC4D7pZmcU0R2okxZoKoVKpEROIB1BT26/1EOGj++bKbG71Rbhz3BVNP6/NVvrrZfH+orcNlHH8DeNk5Qn6WHnCUdAPQIDAQAB';

    private $data;
    protected $config = [];

    public function __construct($data = null)
    {
        $this->config =[
        'pid' => '2088221759705831',
        'app_id' => '2019082266346578',
        'key' => 'ePhFuGFwm2Cf8QelwjxmMA==',
        'rsa' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCBIYAOhXhhWMAbigwMNNDyKQ/bhpiGB7o1RjmYjPkGKkIWVheFWRinSbPPG92X+Pdi/p5/1Zgcx+w8Z5FYA903gQ9C1iLxhhbV8qXdmLa/pDTpQ1/M4CD7IwAcSzzCS89oKuYQYkaiBqlrfJgAoZEOXihgLrEZuDxlaPKxzwsMxoGkdQu2U76V6QuTHTJlI+nyG22Kevs7nPWrvhidW5jeeoNUCIji9lcru+8hamQNeGAdcm49jN2LpfP0c3+izqcYarUFV1cSDm+wGG7TR3esd83lMNGlsc2u3vmJG2N579uPWIQwpzdjtQnob29cAhIWPja0hZiCIsplyzhv0Y+JAgMBAAECggEAZ0uuDW+1SWHeYuk+E/GYB27TcY8sqWK7EOy5HBABEG5zaTy7Gf+YmhF6Fb27uFr/QiBeF/J7+OHwVq2edaK5rjG+TH/RehUCZWjyR95mqcyoSsSLeO79Uwe/ieKhmudb5X8ThJ2o1OmxUvta+H5F9BFcCPywzNs07QZTxerScwehOVn10C58WjJAGxN8cECvG9s+iF38U3k0VrJH7vu+bAKbj5n+mIYEKBw2Z3HR87BySUdA0guoz4qyNKpvSHVMKiWwnS34LpJ6yVmgYFnKdgN8GLRpEb7MgoYz1M2aqVEoH2cA4KHl+/h8I9V93tIYaSqFxJQR1fdJQDILh7pWkQKBgQC4UF7IR/ysc+kmj3sz4soN8DYmmxGbT6bJtVsspdh063gau8akavh3ZaZSeNaV8kgv0PPT8iUK20aFrX5/5e9oi5j9iBVQrbS0c5mrv5miq6BymIke5tJ01FbgYPC1mvVnqh0CeWM9ZUI4n8Uam1yJARc7p6IkOTUPbdF6C7jTvQKBgQCzWrPn5nbtDnWHUbx0G1wLmJphzyVgyydmXFgJ+j0YJJkKrQDVQ8/kEgGPJMFUNIopP7Uw7xPCjxJt6f95PwKu7dky7TYfeCOFiDYmkk249JCyWciO40Mv4x4d/v/e/v0GJL9HD6Hldzs43TBMG5iEDTtqwxXJs3gOA6oXQYeBvQKBgQCEjWhc6UiSTZnznWShYAyoEYUgJo13AMWWctLrPSp6i42IzFqDjFq8o2IGFdldZSz9Fm0ElDSHpkMFiExdduPCcALK8r9BkmtPC4QMvHKlRoDRaVnT23SniL4iCBWUxaiPsQvD58CzOstxJZX/GJRoA1zODjTRkELUocnw19VIkQKBgD8nkDP3VicxMTdeE7L6s1Wt/aa1T18fChekKqgQwpSOxokY1DNEdp7DrGLgOWdSPNg7g6zgcp/Oy1mCzR+/jU1VmWayWp2IK1Ho3dCFMfMPwyfaL6II6m6hVciQMz8toKEaLXRzT7nCW0sxr8EM8o6FLkfwu+pTVNHyfmFEBNgtAoGAcImU4DvhHtrEsucRGl/sJCViq3xIGGhU/TwgeEKZ33Zzhk8Tt/vBiEDKsqe+Glyt5J8wqui7W30nbyHtfGkBRkvSo8VUvzZQX5R9/aIpnFCWssuztIRO87bnIpKcqqlCwOSc7Z7DMM53+Do19eOPTbKjXR3l8JhXtd0bCE5NuY0=',
    ];

        $this->data = $data;
    }

    /**
     * 二维码模式
     * @param $url
     * @param $return_url
     * @return mixed
     * @throws \Exception
     */
    public function pay($url,$return_url)
    {
        $aop = new AopClient ();
        $aop->gatewayUrl = self::GATE_WAY_URL;
        $aop->appId = $this->config['app_id'];
        $aop->rsaPrivateKey = $this->config['rsa'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'utf-8';
        $aop->format = 'json';
        $request = new AlipayTradePrecreateRequest ();
        $request->setNotifyUrl($url);
        $request->setReturnUrl($return_url);

        $request->setBizContent("{" .
            "\"out_trade_no\":\"" . $this->data['out_trade_no'] . "\"," .
            "\"total_amount\":\"" . $this->data['total_fee']. "\"," .
            "\"timeout_express\":\"90m\"," .
            "\"body\":\"" . $this->data['body']. "\"," .
            "\"subject\":\"" . $this->data['subject']. "\"" .
            "  }");
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $data = JsonHelper::decode(JsonHelper::encode($result->$responseNode));
        if(!empty($resultCode)&&$resultCode == 10000){
            if (!file_exists(public_path()."/aliPayImg/")) mkdir(public_path()."/aliPayImg/", 0777);
            $img_url = public_path()."/aliPayImg/" . $this->data['out_trade_no'] . '.png';
            QRcode::png($data['qr_code'], $img_url);
            if (!file_exists($img_url)) error('ali_pay', 'ali_pay_error');
            $pay_url_data['url'] = $this->headerurl() . '/aliPayImg/' . $this->data['out_trade_no'] . '.png';
            return $pay_url_data;
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
    function headerurl(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return  $http_type . $_SERVER['HTTP_HOST'];
    }
    /**
     * @param $third_trade_no
     * @param $order_no
     * @return bool|mixed
     * @throws Exception
     */
    public function findAlipayByOrderNo($order_no = '',$third_trade_no = '')
    {
        if (empty($third_trade_no) && empty($order_no)) {
            throw new Exception("param_exit");
        }
        $aop = new AopClient();
        $aop->gatewayUrl = self::GATE_WAY_URL;
        $aop->appId = $this->config['app_id'];
        $aop->rsaPrivateKey = $this->config['rsa'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'utf-8';
        $aop->format = 'json';
        $request = new AlipayTradeQueryRequest();

        $request->setBizContent("{" .
            "\"out_trade_no\":\"$order_no\"," .
            "\"trade_no\":\"$third_trade_no\"," .
            "\"org_pid\":\"" . $this->config['pid'] . "\"," .
            "      \"query_options\":[" .
            "        \"TRADE_SETTE_INFO\"" .
            "      ]" .
            "  }");
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        LogHelper::logSubs('alipay find order:'.$order_no.',status:'.JsonHelper::encode($result->$responseNode));
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return JsonHelper::decode(JsonHelper::encode($result->$responseNode));
        } else {
            return false;
        }
    }

    public function close($out_trade_no){
        $aop = new AopClient();
        $aop->gatewayUrl = self::GATE_WAY_URL;
        $aop->appId = $this->config['app_id'];
        $aop->rsaPrivateKey = $this->config['rsa'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'utf-8';
        $aop->format = 'json';
        $request = new AlipayTradeCloseRequest ();
        $request->setBizContent("{" .
            "\"out_trade_no\":\"$out_trade_no\"," .
            "\"operator_id\":\"YX01231\"" .
            "  }");
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        LogHelper::logSubs('alipay close order:'.$out_trade_no.',status:'.JsonHelper::encode($result->$responseNode));
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param $url
     * @return array
     */
    public function alipay($url,$return_url)
    {
        $data = [
            'service' => 'create_direct_pay_by_user', //接口名称 固定值
            'partner' => $this->config['pid'], //合作伙伴身份ID
            '_input_charset' => 'UTF-8', //商城网站编码格式
            'sign_type' => 'MD5',// 签名方式
            'sign' => '',// 需要根据其他参数生成
            'notify_url' => $url,//异步通知地址 可空
            'return_url' => $return_url,//同步通知地址 可空
            'seller_id' => $this->config['pid'],//支付宝用户号 seller_id、seller_email、seller_account_name至少传一个

        ];
        $params = array_merge_recursive($this->data, $data);

        $params = $this->setSign($params);
        $url = self::PAYGA_GEWAY . '?' . $this->getUrl($params);
        return ['url' => $url];
    }

    public function checkSignRsa($param){
        $aop = new \AopClient;

        $aop->alipayrsaPublicKey = self::public_key;
        //此处验签方式必须与下单时的签名方式一致
        return $aop->rsaCheckV1($param, NULL, "RSA2");
    }
    //获取签名MD5
    public function getSign($arr)
    {
        return md5($this->getStr($arr) . $this->config['key']);
    }

    //获取含有签名的数组MD5
    public function setSign($arr)
    {
        $arr['sign'] = $this->getSign($arr);
        return $arr;
    }

    public function getStr($arr, $type = 'RSA')
    {
        //筛选
        if (isset($arr['sign'])) {
            unset($arr['sign']);
        }
        if (isset($arr['sign_type']) && $type == 'RSA') {
            unset($arr['sign_type']);
        }
        //排序
        ksort($arr);
        //拼接
        return $this->getUrl($arr, false);
    }

    //将数组转换为url格式的字符串
    public function getUrl($arr, $encode = true)
    {
        if ($encode) {
            return http_build_query($arr); //转译

        } else {
            return urldecode(http_build_query($arr)); //不转译
        }
    }

    //2.验证签名
    public function checkSign($arr)
    {
        $sign = $this->getSign($arr);
        if ($sign == $arr['sign']) {
            return true;
        } else {
            return false;
        }
    }

    //验证是否来之支付宝的通知
    public function isAlipay($arr)
    {
        $url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . $this->config['pid'] . '&notify_id=';
        $str = file_get_contents($url . $arr['notify_id']);
        if ($str == 'true') {
            return true;
        } else {
            return false;
        }
    }

    // 4.验证交易状态
    public function checkOrderStatus($arr)
    {
        if ($arr['trade_status'] == 'TRADE_SUCCESS' || $arr['trade_status'] == 'TRADE_FINISHED') {
            return true;
        } else {
            return false;
        }
    }
}

