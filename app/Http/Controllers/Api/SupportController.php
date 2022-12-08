<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;
use App\Services\EmailService;
use App\Services\MailmagicboardService;
use App\Services\SupportapiService;
use App\Services\SupportService;
use Illuminate\Http\Request;

class SupportController
{

    public function getsupport(Request $request){
        $SubscriptionService= new SupportapiService();
        $param = $request->all();
        if(!isset($param['order_no'])){
            return json_encode(['data'=>'','code'=>403,'msg'=>"缺少参数"]);
        }
        $data=$SubscriptionService->get_data($param);
        return json_encode(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }

    public function thefeedback(Request $request){
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $mailedatas = $maile->getFindcategorical(49);
        $param = $request->all();
        if(!isset($param['email'])){
            return \Response::json(['data'=>'','code'=>403,'msg'=>"缺少参数"]);
        }
        $email->sendDiyContactEmail([], 11, $param['email'],$mailedatas);
        return \Response::json(['code'=>200,'msg'=>"success"]);
    }

}