<?php


namespace App\Services;


use App\Models\OrderCashFlow;

class PayCenterService
{
    /**
     * 调用支付中心接口，生成支付链接(package商品，调用付款的接口)
     * @param $order_no
     * @param $price
     * @return array
     */
    public function createPackageOrder($order_no, $price){
        $serverName = 'https://' . $_SERVER['SERVER_NAME'] . '/api/paypal-callback';
        $url = env('PAY_CENTER') . '/v1/payCenterOrder/createOrder';
        $body = [
            'orderNum' => $order_no,
            'currency' => 'USD',
            'amount' => $price,
            'returnUrl' => $serverName . '?success=true',
            'cancelUrl' => $serverName . '?success=false',
            'orderGoodsList' => [
                [
                    'unitPrice' => $price,
                    'num' => 1
                ]
            ]
        ];

        $encryptionService = new EncryptionService(EncryptionService::PROJECT_2_SAAS);
        $token = $encryptionService->encryption(json_encode(['configId' => 3]));
        $headers = [
            'Content-type' =>  'application/json',
            'Accept' => 'application/json',
            'Authorization' => $token
        ];

        $result = HttpClientService::post($url, $body, $headers);
        \Log::info('创建订单返回结果', ['body'=>$body, 'result'=>$result]);

        return $result;
    }

    /**
     * 调用支付中心接口，生成支付链接(订阅制商品，调用订阅的接口)
     * @param $order_no
     * @param $price
     * @param $cycle
     * @return array|mixed
     */
    public function createPlanOrder($order_no, $price, $cycle){
        $serverName = 'https://' . $_SERVER['SERVER_NAME'] . '/api/paypal-callback';
        $url = env('PAY_CENTER') . '/v1/payCenterOrder/createSubscription';
        $body = [
            'orderNum' => $order_no,
            'currency' => 'USD',
            'price' => 1,
            'cycle' => 9,
            'returnUrl' => $serverName . '?success=true',
            'cancelUrl' => $serverName . '?success=false',
            'orderGoodsList' => [
                [
                    'unitPrice' => 1,
                    'num' => 1
                ]
            ]
        ];

        $encryptionService = new EncryptionService(EncryptionService::PROJECT_2_SAAS);
        $token = $encryptionService->encryption(json_encode(['configId' => 3]));
        $headers = [
            'Content-type' =>  'application/json',
            'Accept' => 'application/json',
            'Authorization' => $token
        ];

        $result = HttpClientService::post($url, $body, $headers);

        \Log::info('创建订单返回结果', ['body'=>$body, 'result'=>$result]);

        return $result;
    }

    /**
     * 查询订单状态接口
     * @param $trade_id
     * @param $package_type
     * @return array
     */
    public function getOrderStatus($trade_id, $package_type){
        $url = env('PAY_CENTER') . '/v1/payCenterOrder/getOrder';

        $query = ['tradeId' => $trade_id, 'tradeType' => $package_type];

        $encryptionService = new EncryptionService(EncryptionService::PROJECT_2_SAAS);
        $token = $encryptionService->encryption(json_encode(['configId' => 3]));
        $headers = [
            'Content-type' =>  'application/json',
            'Accept' => 'application/json',
            'Authorization' => $token
        ];

        $result = HttpClientService::get($url, $query, $headers);
        \Log::info('查询订单状态返回结果', ['query'=>$query, 'result'=>$result]);

        //接口正常返回结果
        if(is_array($result)){
            $code = $result['code'];
        }else{
            $code = $result->code;
        }

        //订单创建成功
        if($code == 200){
            $data = $result->data;
            return ['code'=>200, 'message'=>'查询成功', 'data'=>['status'=>$data->status, 'next_billing_time'=>$data->next_billing_time ?? '']];
        }else{
            return ['code'=>500, 'message'=>'查询失败'];
        }
    }
}