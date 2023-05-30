<?php


namespace App\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpClientService
{

    /**
     * post请求
     * @param $url
     * @param $body
     * @param $headers
     * @return array
     */
    public static function post($url, $body, $headers)
    {
        try{
            $client = new Client();
            $res = $client->request('POST', $url, [
                'json' => $body,
                'headers' => $headers
            ]);
        }catch (GuzzleException $e){
            return ['code'=>500, 'message'=>'接口请求失败'];
        }

        // TODO 状态码处理
        $data = json_decode($res->getBody()->getContents());


        return ['code'=>200, 'message'=>'success', 'data'=>$data];
    }

    /**
     * get请求
     * @param $url
     * @param $headers
     * @return array
     */
    public static function get($url, $headers)
    {
        try{
            $client = new Client();
            $res = $client->request('GET', $url, [
                'headers' => $headers
            ]);
        }catch (GuzzleException $e){
            return ['code'=>500, 'message'=>'接口请求失败'];
        }

        return ['code'=>200, 'message'=>'success', 'data'=>$res->getBody()->getContents()];
    }
}