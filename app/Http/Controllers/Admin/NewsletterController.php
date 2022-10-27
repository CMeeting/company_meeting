<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\SubscriptionadminService;
use App\Services\NewsletterService;
use App\Services\EmailService;
use App\Repositories\RolesRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsletterController extends BaseController {

    public function subscription_list(){
        $SubscriptionService= new SubscriptionadminService();
        $data = $SubscriptionService->data_list();
        return $this->view('subscription',['data'=>$data]);
    }

    public function createsubscription(){
        return $this->view('createsubscription');
    }

    public function createrunsubscription(Request $request){
        $param = $request->input();
        $SubscriptionService = new SubscriptionadminService();
        if (!empty($param)) {
            if($param['data']['email']==""){
                return ['code'=>0,'msg'=>'订阅人邮箱不能为空'];
            }
            $bool = $SubscriptionService->addEditcaregorical($param);
            if ($bool == "repeat") {
                return ['code'=>0,'msg'=>'该邮件地址已存在订阅信息，请编辑'];
            }
            if ($bool) {
                    flash('添加成功')->success()->important();
                    return ['code'=>1,'msg'=>'添加成功'];
            } else {
                    return ['code'=>0,'msg'=>'添加失败'];
            }
        }

    }

    public function toggle_status(Request $request){
        $param = $request->input();
        $SubscriptionService = new SubscriptionadminService();
        $bool = $SubscriptionService->toggle($param);
        if ($bool) {
            $data = $SubscriptionService->getFindcategorical($param['delid']);
            return ['code'=>0,'status'=>$data['status']];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }

    public function updatesubscription($id){
       $SubscriptionService = new SubscriptionadminService();
       if(!$id){
           flash('缺少参数')->error()->important();
           return redirect()->route('newsletter.subscription_list');
       }
       $data = $SubscriptionService->getFindcategorical($id);
       return $this->view('updatesubscription',['data'=>$data]);
    }

    public function updaterunsubscription(Request $request){
        $param = $request->input();
        $SubscriptionService = new SubscriptionadminService();
        if (!empty($param)) {
            if($param['data']['email']==""){
                return ['code'=>0,'msg'=>'订阅人邮箱不能为空'];
            }
            $bool = $SubscriptionService->Editcaregorical($param['data']);
            if ($bool == "repeat") {
                return ['code'=>0,'msg'=>'该邮件地址已存在订阅信息，请编辑'];
            }
            if ($bool) {
                flash('更新成功')->success()->important();
                return ['code'=>1,'msg'=>'更新成功'];
            } else {
                return ['code'=>0,'msg'=>'更新失败'];
            }
        }
    }


    public function newsletter_list(Request $request){
        $param = $request->input();
        $maile = new NewsletterService();
        $data = $maile->data_list($param);
        return $this->view('newsletter',['data'=>$data]);
    }

    public function createnewsletter(){
        return $this->view('createnewsletter');
    }

    public function createrunnewsletter(Request $request){
        $param = $request->input();
        $maile = new NewsletterService();
        if (!empty($param)) {
            if($param['data']['name']==""){
                return ['code'=>0,'msg'=>'模板名称不能为空'];
            }
            if($param['data']['title']==""){
                return ['code'=>0,'msg'=>'电子报标题不能为空'];
            }
            if($param['data']['info']==""){
                return ['code'=>0,'msg'=>'电子报内容不能为空'];
            }
            $bool = $maile->addEditcaregorical($param);
            if ($bool == "repeat") {
                return ['code'=>0,'msg'=>'电子报模板名称重复'];
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    return ['code'=>1,'msg'=>'添加成功'];
                } else {
                    return ['code'=>0,'msg'=>'添加失败'];
                }

            }
        }

    }


    public function updatenewsletter($id){
        $maile = new NewsletterService();
        if (isset($id) && $id) {
            $data = $maile->getFindcategorical($id);
        }else{
            flash('缺少参数')->error()->important();
//            return redirect()->route('newsletter.newsletter_list');
        }
        return $this->view('updatenewsletter',['data'=>$data]);
    }

    public function updaterunnewsletter(Request $request){
        $param = $request->input();
        $maile = new NewsletterService();
        if (!empty($param)) {
            if($param['data']['name']==""){
                return ['code'=>0,'msg'=>'模板名称不能为空'];
            }
            if($param['data']['title']==""){
                return ['code'=>0,'msg'=>'邮件标题不能为空'];
            }
            if($param['data']['info']==""){
                return ['code'=>0,'msg'=>'邮件内容不能为空'];
            }
            $bool = $maile->addEditcaregorical($param);
            if ($bool == "repeat") {
                return ['code'=>0,'msg'=>'模板名称重复'];
            } else {
                if ($bool) {
                    flash('编辑成功')->success()->important();
                    return ['code'=>1,'msg'=>'编辑成功'];
                } else {
                    flash('编辑失败')->error()->important();
                    return ['code'=>0,'msg'=>'编辑失败'];
                }

            }
        }

    }

    public function newsletter_info($id){
        $maile = new NewsletterService();
        if (isset($id) && $id) {
            $data = $maile->getFindcategorical($id);
        }else{
            flash('缺少参数')->error()->important();
//            return redirect()->route('newsletter.newsletter_list');
        }
        return $this->view('newsletterinfo',['data'=>$data]);
    }

    public function delnewsletter(Request $request){
        $param = $request->input();
        $maile = new NewsletterService();
        $bool = $maile->addEditcaregorical($param);
        if ($bool) {
            $data = $maile->getFindcategorical($param['delid']);
            return ['code'=>0,'status'=>$data['deleted']];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }


    public function newsletterlog(Request $request){
        $param = $request->input();
        $maile = new NewsletterService();
        if(!isset($param['id']))return ['code'=>1,'msg'=>"缺少参数"];
        $res = $maile->add_newsletterlog($param['id']);
        if($res=="setnull"){
            return ['code'=>1,'message'=>"当前没有可发送的订阅用户"];
        }elseif ($res['code']==0){
            return ['code'=>1,'message'=>$res['msg']];
        }
        return ['code'=>0,'message'=>"OK",'data'=>$res['data']];
    }

    public function ajaxsend($id){
        $email = new EmailService();
        $Newsletter = new NewsletterService();
        $data=$Newsletter->getFindcategorical($id);
        $user_email=$Newsletter->get_useremail();
        $res = $email->sendDiyContactEmail($data,2,$user_email);
        return ['code'=>1,'msg'=>"OK",'data'=>$res];
    }


    public function newsletterloglist(){
        $Newsletter = new NewsletterService();
        $data=$Newsletter->newsletterlog_list();
        return $this->view('newsletterloglist',['data'=>$data]);
    }

    public function newsletterloginfo($id){
        $Newsletter = new NewsletterService();
        $data=$Newsletter->getFindcategorical($id,2);
        $association=$Newsletter->getFindcategorical($data['association_id']);
        return $this->view('newsletterloginfo',['data'=>$data,'association'=>$association]);
    }

    public function again_sendfind(Request $request){
        $param = $request->input();
        $Newsletter = new NewsletterService();
        $email = new EmailService();
        $data=$Newsletter->getFindcategorical($param['id'],2);
        $association=$Newsletter->getFindcategorical($data['association_id']);
        $arr=['info'=>$data['info'],'title'=>$association['title'],'id'=>$data['association_id']];
        $res = $email->sendDiyContactEmail($arr,2,$data['mail']);
        return ['code'=>1,'msg'=>"OK",'data'=>$res];
    }


}
