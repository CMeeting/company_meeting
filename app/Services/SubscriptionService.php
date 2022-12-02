<?php

namespace App\Services;

use App\Models\Subscription;
use App\Services\EmailService;
use App\Services\MailmagicboardService;

class SubscriptionService
{
    public function __construct()
    {

    }

    public function update_status($data){
        $Subscription =new Subscription();
        $email = new EmailService();
        $maile = new MailmagicboardService();
        $mailedatas = $maile->getFindcategorical(53);
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
            $email->sendDiyContactEmail([], 10, $data['email'],$mailedatas);
            return ['data'=>$arr,'code'=>200,'msg'=>"ok"];
        }else{
           if($is_find['status']==$status){
               $bool=$Subscription->update(['updated_at'=>date("Y-m-d H:i:s")],"id=".$is_find['id']);
               $email->sendDiyContactEmail([], 10, $data['email'],$mailedatas);
               return ['data'=>'','code'=>200,'msg'=>"该邮箱已是订阅状态，已更新订阅时间"];
           }else{
               $bool=$Subscription->update(['updated_at'=>date("Y-m-d H:i:s"),'status'=>$status],"id=".$is_find['id']);
               $subscribed=$status?"active":"cancel";
               if($status)$email->sendDiyContactEmail([], 10, $data['email'],$mailedatas);
               $arr=[
                   'email'=>$data['email'],
                   'subscribed'=>$subscribed,
               ];
               return ['data'=>$arr,'code'=>200,'msg'=>"已修改订阅状态"];
           }
        }
    }

    /**
     * 新增
     * @param $email
     */
    public function add($email){
        $model = new Subscription();
        $model->email = $email;
        $model->status = 1;
        $model->save();
    }

    /**
     * 取消订阅
     * @param $id
     */
    public function delete($id){
        $model = Subscription::find($id);

        if($model instanceof Subscription){
            $model->deleted = 1;
            $model->save();
        }
    }
}