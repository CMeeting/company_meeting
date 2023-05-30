<?php


namespace App\Services;


use App\Models\OrderCashFlow;

class PayCenterService
{
    /**
     * 调用支付中心接口，生成支付链接
     * @param $order_no
     * @param $price
     * @return array
     */
    public function createOrder($order_no, $price){
        $serverName = 'https://' . $_SERVER['SERVER_NAME'] . '/api/paypal-callback';
        $url = '192.168.10.66:8971' . '/v1/payCenterOrder/createOrder';
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

        return HttpClientService::post($url, $body, $headers);
    }
}