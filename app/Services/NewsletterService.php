<?php

namespace App\Services;

use Auth;
use App\Models\NewsletterModel AS mail;
use App\Models\Subscriptionadmin;
use App\Models\NewsletterlogModel;
class NewsletterService
{
    public function __construct()
    {

    }

    public function data_list($param){
        $where = "1=1";
        $email = new mail();
        $data = $email->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        return $data;
    }


    public function getFindcategorical($id,$type=1)
    {
        if($type==1){
            $email = new mail();
        }else{
            $email = new NewsletterlogModel();
        }
        $where = "id='$id'";
        $data = $email->_find($where);
        $data = $email->objToArr($data);
        return $data;
    }

    public function addEditcaregorical($param){
        $admin = Auth::guard('admin')->user();
        $email = new mail();
        if (isset($param['data'])) {
            $data = $param['data'];
        }

        if (isset($data['id'])) {
            $where = "id='{$data['id']}'";
            $is_find = $email->_find($where);
            $is_find = $email->objToArr($is_find);
            if (($is_find['name'] != $data['name'])) {
                $names = $email->_find("name='" . $data['name']."'");
                $names = $email->objToArr($names);
            }
            if ((isset($names) && $names)) {
                return "repeat";
            }
            $bool = $email->_update($data, $where);
        } elseif (isset($param['delid'])) {
            $where = "id='{$param['delid']}'";
            $is_find = $email->_find($where);
            $is_find = $email->objToArr($is_find);
            $status=$is_find['deleted']?0:1;
            $bool = $email->_update(['deleted' => $status], "id=" . $param['delid']);
        } else {

            $names = $email->_find("name='" . $data['name']."'");
            $names= $email->objToArr($names);
            if (isset($names) && $names) {
                return "repeat";
            }
            $data['admin_id']=$admin->id;
            $data['admin_name']=$admin->name;
            $bool = $email->insertGetId($data);
        }
        return $bool;

    }


    public function add_newsletterlog($id){
        $useremail = new Subscriptionadmin();
        $newsletterlog = new NewsletterlogModel();
        $maildata=$this->getFindcategorical($id);
        $user_mail=$useremail->_where("status=1 and deleted=0","id DESC","email");
        if(!$user_mail)return "setnull";
        $data=[];
        foreach ($user_mail as $k=>$v){
            $data[]=['association_id'=>$id, 'mail'=>$v['email'], 'info'=>$maildata['info'],'created_at'=>date("Y-m-d H:i:s")];
        }
        $res=$newsletterlog->insert($data);
        if($res){
            $url=$this->headerurl().'/admin/ajaxsend/'.$id;
            return ['code'=>1,'data'=>$url];
        }else{
            return ['code'=>0,'data'=>'','msg'=>"发送失败"];
        }
    }

    public function get_useremail(){
        $useremail = new Subscriptionadmin();
        $user_mail=$useremail->_where("status=1 and deleted=0","id DESC","email");
        $arr=array();
        foreach ($user_mail as $k=>$v){
            $arr[]=$v['email'];
        }
        $data=implode(",",$arr);
        return $data;
    }

    function headerurl(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return  $http_type . $_SERVER['HTTP_HOST'];
    }


    public function newsletterlog_list(){
        $newlog = new NewsletterlogModel();
        $where="deleted=0";
        $data = $newlog->whereRaw($where)->orderByRaw('updated_at desc')->paginate(10);
        return $data;
    }


}