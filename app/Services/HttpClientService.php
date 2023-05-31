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
    public static function post($url, $body = [], $headers = [])
    {
        try{
            $options = [];
            if(!empty($body)){
                $options['json'] = $body;
            }

            if(!empty($headers)){
                $options['headers'] = $headers;
            }

            $client = new Client();
            $res = $client->request('POST', $url, $options);

            \Log::info('接口请求结果', ['url'=>$url, 'body'=>$body, 'result'=>$res]);

            return ['code'=>200, 'message'=>'success', 'data'=>json_decode($res->getBody()->getContents())];
        }catch (GuzzleException $e){
            \Log::info('接口请求失败', ['url'=>$url, 'body'=>$body, 'message'=>$e->getMessage()]);
            return ['code'=>500, 'message'=>'接口请求失败'];
        }
    }

    /**
     * get请求
     * @param $url
     * @param $query
     * @param $headers
     * @return array
     */
    public static function get($url, $query = [], $headers = [])
    {
        try{
            $options = [];
            if(!empty($query)){
                $options['query'] = $query;
            }

            if(!empty($headers)){
                $options['headers'] = $headers;
            }

            $client = new Client();
            $res = $client->request('GET', $url, $options);
            \Log::info('接口请求结果', ['url'=>$url, 'query'=>$query, 'result'=>$res]);

            $data = json_decode($res->getBody()->getContents());

            return ['code'=>200, 'message'=>'success', 'data'=>$data];
        }catch (GuzzleException $e){
            \Log::info('接口请求失败', ['url'=>$url, 'query'=>$query, 'message'=>$e->getMessage()]);
            return ['code'=>500, 'message'=>'接口请求失败'];
        }
    }
}