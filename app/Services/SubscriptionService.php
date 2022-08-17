<?php

namespace App\Services;

use App\Models\Subscription;

class SubscriptionService
{
    public function __construct()
    {

    }

    public function update_status($data){
        $Subscription =new Subscription();
        $status=(isset($data['subscribed'])&&$data['subscribed'])?1:0;
        $where = "email='{$data['email']}'";
        $is_find = $Subscription->find($where);
        $is_find = $Subscription->objToArr($is_find);
        if(!$is_find){
              $data=['status'=>$status, 'email'=>$data['email']];
              $bool=$Subscription->insertGetId($data);
              $arr=[
                  'email'=>$data['email'],
                  'subscribed'=>'active',
              ];
            return ['data'=>$arr,'code'=>200,'msg'=>"ok"];
        }else{
           if($is_find==$status){
               $bool=$Subscription->update(['updated_at'=>date("Y-m-d H:i:s")],"id=".$is_find['id']);
               return ['data'=>'','code'=>200,'msg'=>"该邮箱已是订阅状态，已更新订阅时间"];
           }else{
               $bool=$Subscription->update(['updated_at'=>date("Y-m-d H:i:s"),'status'=>$status],"id=".$is_find['id']);
               $subscribed=$status?"active":"cancel";
               $arr=[
                   'email'=>$data['email'],
                   'subscribed'=>$subscribed,
               ];
               return ['data'=>$arr,'code'=>200,'msg'=>"已修改订阅状态"];
           }
        }
    }
}