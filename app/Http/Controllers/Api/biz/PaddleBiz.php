<?php

namespace App\Http\Controllers\Api\biz;

use app\api\model\Product;
use App\Http\extend\core\helper\LogHelper;
use App\Http\extend\core\helper\SysHelper;

require_once dirname(dirname(dirname(__DIR__))) . '/extend/paypal/rest-api-sdk-php/sample/common.php';
class PaddleBiz
{

    private $sandbox;
    private $data;
    private $CREATE_COUPONS;
    private $UPDATE_COUPONS;
    private $DELETE_COUPONS;
    private $CREATE_PAY_LINK;
    private $GET_PRODUCT;
    private $REFUND;
    protected $products = [];
    protected $public_key_string;
    protected $config = [];
    protected $sand_product_id = ['mac' => '14228', 'windows' => '21566'];
    protected $product_id = ['mac' => '720301', 'windows' => '748380'];

    public function __construct($data = null)
    {
        $this->sandbox = true;
        $pem = $this->sandbox ? 'sendbox_public_key.pem' : 'public_key.pem'; //沙箱pem:sendbox_public_key.pem,正式pem：public_key.pem
        $this->public_key_string = file_get_contents(dirname(dirname(dirname(__DIR__))).'/extend/paddle/'. $pem);
        if($this->sandbox){
            $this->config=[
                'vendor_id'         =>  2760,
                'vendor_auth_code'  =>  'a618d381d6e20acd36bb0f130586779e98641137a9f649b878'
            ];
        }else{
            $this->config=[
                'vendor_id'         =>  134050,
                'vendor_auth_code'  =>  'be91e8545f1c2902f7ebd79c7217b2d7699c9a3f0b67b51d5b'
            ];
        }
        $this->data = $data;
        $sand_box_str = $this->sandbox ? 'sandbox-' : '';
        $this->CREATE_COUPONS = 'https://'.$sand_box_str.'vendors.paddle.com/api/2.1/product/create_coupon';
        $this->UPDATE_COUPONS = 'https://'.$sand_box_str.'vendors.paddle.com/api/2.1/product/update_coupon';
        $this->DELETE_COUPONS = 'https://'.$sand_box_str.'vendors.paddle.com/api/2.0/product/delete_coupon';
        $this->CREATE_PAY_LINK = 'https://'.$sand_box_str.'vendors.paddle.com/api/2.0/product/generate_pay_link';
        $this->GET_PRODUCT = 'https://'.$sand_box_str.'vendors.paddle.com/api/2.0/product/get_products';
        $this->REFUND = 'https://'.$sand_box_str.'vendors.paddle.com/api/2.0/payment/refund';
    }

    public function Verify($param)
    {
        $public_key = openssl_get_publickey($this->public_key_string);

        // Get the p_signature parameter & base64 decode it.
        $signature = base64_decode($param['p_signature']);

        // Get the fields sent in the request, and remove the p_signature parameter
        $fields = $param;
        unset($fields['p_signature']);

        // ksort() and serialize the fields
        ksort($fields);
        foreach($fields as $k => $v) {
            if(!in_array(gettype($v), array('object', 'array'))) {
                $fields[$k] = "$v";
            }
        }
        $data = serialize($fields);

        // Verify the signature
        $verification = openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA1);
        if($verification == 1) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    public function getProduct()
    {
       return self::httpCurl($this->GET_PRODUCT,$this->config,'POST');
    }

    public function createCoupon()
    {
        $couponData = [
            'vendor_id'         =>  $this->config['vendor_id'],
            'vendor_auth_code'  =>  $this->config['vendor_auth_code'],
            'coupon_code'   =>  $this->data['code'] ?? strtoupper($this->data['rcode']), //优惠码
            'description'   =>  $this->data['name'] ?? $this->currencydata['name'] ?? '', //优惠券描述
            'product_ids'   =>  $this->getUsePaddleProductId($this->data['product_id']), //绑定产品
            'coupon_type'   =>  'product', //优惠券类型(product、checkout)
            'discount_type' =>  $this->data['discount'] ? 'percentage' : 'flat', //折扣类型(指定金额flat、百分比percentage)
            'discount_amount'   =>  $this->data['discount'] ? (1-$this->data['discount'])*100 : $this->data['price'], //折扣金额
            'currency'      =>  'USD', //货币(如果折扣类型是flat，这里必须填写)
            'allowed_uses'  =>  $this->data['total_amount'] ?? 1, //优惠券使用次数
            'expires'       => date('Y-m-d',strtotime('+1 day',array_key_exists('expire_date',$this->data) ? strtotime($this->data['expire_date']) : strtotime($this->data['end_date']))), //有效期时间
            'recurring'     =>  '0', //如果优惠券用于订阅产品，这表明折扣是否应适用于首次购买后的定期付款
        ];

        LogHelper::logPaddle('paddle create coupon data：'. json_encode($couponData));
        $request = self::httpCurl($this->CREATE_COUPONS,$couponData,'POST');
        if ($request['success'] != true)
        {
            LogHelper::logPaddle('paddle create coupon request：'. json_encode($request));
        }
    }

    public function updateCoupon()
    {
        $couponData = [
            'vendor_id'         =>  $this->config['vendor_id'],
            'vendor_auth_code'  =>  $this->config['vendor_auth_code'],
            'coupon_code'   =>  $this->data['old_code'] ?? $this->data['code'], //优惠码
            'new_coupon_code'   =>  $this->data['code'] ?? strtoupper($this->data['rcode']), //优惠码
            'description'   =>  $this->currencydata['name'] ?? $this->data['name'] ?? '', //优惠券描述
            'product_ids'   =>  $this->getUsePaddleProductId($this->data['product_id']), //绑定产品
            'coupon_type'   =>  'product', //优惠券类型(product、checkout)
            'discount_type' =>  $this->data['discount'] ? 'percentage' : 'flat', //折扣类型(指定金额flat、百分比percentage)
            'discount_amount'   =>  $this->data['discount'] ? (1-$this->data['discount'])*100 : $this->data['price'], //折扣金额
            'allowed_uses'  =>  $this->data['total_amount'] ?? 1, //优惠券使用次数
            'expires'       =>  array_key_exists('expire_date',$this->data) ? date('Y-m-d',strtotime($this->data['expire_date'])) : date('Y-m-d',strtotime($this->data['end_date'])), //有效期时间
            'recurring'     =>  '0', //如果优惠券用于订阅产品，这表明折扣是否应适用于首次购买后的定期付款
        ];
        if (!$this->data['discount'])
        {
            $couponData['currency'] = 'USD'; //货币(如果折扣类型是flat，这里必须填写)
        }
        LogHelper::logPaddle('paddle update coupon data：'. json_encode($couponData));
        $request = self::httpCurl($this->UPDATE_COUPONS,$couponData,'POST');
        if ($request['success'] != true)
        {
            LogHelper::logPaddle('paddle update coupon request：'. json_encode($request));
        }
    }

    public function delCoupon()
    {
        $couponData = [
            'vendor_id'         =>  $this->config['vendor_id'],
            'vendor_auth_code'  =>  $this->config['vendor_auth_code'],
            'coupon_code'   =>  $this->data['code'] ?? strtoupper($this->data['rcode']), //优惠码
            'product_id'   =>  $this->getUsePaddleProductId($this->data['product_id']), //绑定产品
        ];
        LogHelper::logPaddle('paddle delete coupon data：'. json_encode($couponData));
        $request = self::httpCurl(self::DELETE_COUPONS,$couponData,'POST');
        if ($request['success'] != true)
        {
            LogHelper::logPaddle('paddle delete coupon request：'. json_encode($request));
        }
    }

    public function createPayLink($order,$product,$price,$amount=1,$param=array())
    {
        $price = $price;
        $orderData = [
            'vendor_id'         =>  $this->config['vendor_id'],
            'vendor_auth_code'  =>  $this->config['vendor_auth_code'],
            'product_id'   =>  14228, //产品ID
            'title'   =>  "PDF Reader Pro Mac", //产品名称
            'prices' =>  ['USD:' . $price],
            'quantity_variable'   =>  '0', //用户修改更改购买数量配置
            'return_url'=> 'http://test-pdf-pro.kdan.cn:3026/order/checkout',
            'discountable' => '0',
            'quantity'   =>  $amount ?? '1', //购买数量默认为1个
//            'expires'   =>  '2021-08-29', //支付链接过期时间
            'customer_email'   =>  "1322061784@qq.com", //客户邮箱
            'customer_country'   =>  $param['customer_country'] ?? '', //客户所在国家
            'customer_postcode'   =>  $param['customer_postcode'] ?? '', //客户所在地邮编
            'passthrough'       =>  $order  //原数据可以返回到webhook
        ];

        $request =  self::httpCurl($this->CREATE_PAY_LINK,$orderData,'POST');
        return $request['response'];
    }

    public function refund()
    {
        $refundData = [
            'vendor_id'         =>  $this->config['vendor_id'],
            'vendor_auth_code'  =>  $this->config['vendor_auth_code'],
            'order_id'  =>  $this->data,
            'reason'  =>  '(退款)',
        ];

        LogHelper::logOrder('getpayLinkData:'.json_encode($refundData));
        $request =  self::httpCurl($this->REFUND,$refundData,'POST');
        LogHelper::logOrder('getpayLinkRequest:'.json_encode($refundData));
        return $request;
    }

    public function getUsePaddleProductId($product_code) {
        $product = $this->sandbox ? $this->sand_product_id : $this->product_id;
        if (is_numeric($product_code)) {
            $product_code = Product::value('code',['id' => $product_code]);
        }
        if (strpos($product_code,'pdfreaderpro.mac') !== false) {
            $id = $product['mac'];
        } else {
            $id = $product['windows'];
        }
        return $id;
    }

    /**
     * http请求
     *
     * @param $url     //请求地址
     * @param $params  //链接后拼接的参数数组
     * @param $method  //get/post
     * @param $header  //请求头数组
     */
    public function httpCurl($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(                                     //请求参数
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,       //将curl_exec()获取的信息以文件流的形式返回
            CURLOPT_SSL_VERIFYPEER => false,   //不对认证证书来源的检查
            CURLOPT_SSL_VERIFYHOST => false,   //不从证书中检查SSL加密算法是否存在
            CURLOPT_HTTPHEADER => $header,     //Content-Type类型
            CURLOPT_COOKIESESSION => true,
            CURLOPT_FOLLOWLOCATION => 1,      // 使用自动跳转
            CURLOPT_COOKIE => session_name() . '=' . session_id(),
        );

        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);//将数组变成key=value&key2=value
                break;
            case 'POST':                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params); //同上
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts); // 数组形式设置URL和其他参数
        $data = curl_exec($ch);        // URL抓取并把它传递给浏览器
        curl_close($ch);
        $data = json_decode($data, true);
        return $data;
    }

}

