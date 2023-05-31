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

        //接口正常返回结果
        if($result['code'] == 200){
            $data = $result['data'];
            //订单创建成功
            if($data->code == 200){
                $content = $data->data;
                return ['code'=>200, 'message'=>'创建订单成功', 'data'=>['id'=>$content->id, 'pay_url'=>$content->payHref]];
            }else{
                return ['code'=>500, 'message'=>'创建订单失败'];
            }
        }else{
            return ['code'=>500, 'message'=>'创建订单失败'];
        }
    }

    /**
     * 查询订单状态接口
     * @param $trade_id
     * @return array
     */
    public function getOrderStatus($trade_id){
        $url = env('PAY_CENTER') . '/v1/payCenterOrder/getOrder';

        $query = ['tradeId' => $trade_id];

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
        if($result['code'] == 200){
            $data = $result['data'];
            //查询成功
            if($data->code == 200){
                $content = $data->data;
                return ['code'=>200, 'message'=>'查询成功', 'data'=>['status'=>$content->status]];
            }else{
                return ['code'=>500, 'message'=>'查询失败'];
            }
        }else{
            return ['code'=>500, 'message'=>'查询失败'];
        }
    }
}