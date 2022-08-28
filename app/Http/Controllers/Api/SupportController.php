<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;
use App\Services\SupportService;
use Illuminate\Http\Request;

class SupportController
{

    public function getsupport(Request $request){
        $SubscriptionService= new SupportService();
        $param = $request->all();
        if(!isset($param['order_no'])){
            return json_encode(['data'=>'','code'=>403,'msg'=>"缺少参数"]);
        }
        $data=$SubscriptionService->get_data($param);
        return json_encode(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }

}