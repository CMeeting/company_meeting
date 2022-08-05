<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogTagRequest;
use Illuminate\Http\Request;
use App\Services\MailmagicboardService;
use App\Services\EmailService;

class MailmagicboardController extends BaseController {

    /*
     * 邮件模板列表
     * */
    public function mailmagic_list(Request $request){
        $param = $request->input();
        $maile = new MailmagicboardService();
        $data = $maile->data_list($param);
        return $this->view('mailmagiclist',['data'=>$data]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 添加邮件模板页面
     */
    public function createmailmagiclist(){
        return $this->view('createmailmagiclist');
    }

    public function createrunmailmagic(Request $request){
        $param = $request->input();
        $maile = new MailmagicboardService();
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
                    flash('添加成功')->success()->important();
//                    return redirect()->route('mailmagicboard.mailmagic_list');
                    return ['code'=>1,'msg'=>'添加成功'];
                } else {
                    return ['code'=>0,'msg'=>'添加失败'];
                }

            }
        }

    }

    public function updatemailmagiclist($id){
        $maile = new MailmagicboardService();
        if (isset($id) && $id) {
            $data = $maile->getFindcategorical($id);
        }else{
            flash('缺少参数')->error()->important();
            return redirect()->route('mailmagicboard.mailmagic_list');
        }
        return $this->view('updatemailmagiclist',['data'=>$data]);
    }

    public function updaterunmailmagiclist(Request $request){
        $param = $request->input();
        $maile = new MailmagicboardService();
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

    public function mailmagiclist_info($id){
        $maile = new MailmagicboardService();
        if (isset($id) && $id) {
            $data = $maile->getFindcategorical($id);
        }else{
            flash('缺少参数')->error()->important();
            return redirect()->route('mailmagicboard.mailmagic_list');
        }
        return $this->view('mailmagiclistinfo',['data'=>$data]);
    }

    public function delmailmagic(Request $request){
        $param = $request->input();
        $maile = new MailmagicboardService();
        $bool = $maile->addEditcaregorical($param);
        if ($bool) {
            $data = $maile->getFindcategorical($param['delid']);
            return ['code'=>0,'status'=>$data['deleted']];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }

    public function send_email(Request $request){
        $param = $request->input();
        $maile = new MailmagicboardService();
        $email = new EmailService();
        if(!isset($param['id']))return ['code'=>0,'msg'=>"缺少参数"];
        $data = $maile->getFindcategorical($param['id']);
        $res = $email->sendDiyContactEmail($data,1,$param['email']);
        return ['code'=>1,'msg'=>"OK",'data'=>$res];
    }

}
