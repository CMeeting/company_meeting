<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController
{

    public function subscription_status(Request $request){
        $SubscriptionService= new SubscriptionService();
        $param = $request->all();
        if(!isset($param['email'])){
            return \Response::json(['data'=>'','code'=>403,'msg'=>"缺少邮件参数"]);
        }
        $data=$SubscriptionService->update_status($param);
        return \Response::json(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }

}