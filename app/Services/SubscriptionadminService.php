<?php

namespace App\Services;

use Auth;
use App\Models\Subscriptionadmin;

class SubscriptionadminService
{
    public function __construct()
    {

    }

    public function data_list(){
        $where = "deleted=0";
        $Subscript = new Subscriptionadmin();
        $data = $Subscript->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        return $data;
    }


    public function addEditcaregorical($data){
        $data=$data['data'];
        $admin = Auth::guard('admin')->user();
        $Subscription =new Subscriptionadmin();
        $where = "email='{$data['email']}'";
        $is_find = $Subscription->_find($where);
        $is_find = $Subscription->objToArr($is_find);
        if($is_find)return "repeat";
            $arr=[
                'email'=>$data['email'],
                'status'=>$data['status'],
                'admin_id'=>$admin->id,
                'admin_name'=>$admin->name
            ];
        $bool=$Subscription->insertGetId($arr);
        return $bool;
    }


    public function toggle($data){
        $Subscription =new Subscriptionadmin();
        $where = "id='{$data['delid']}'";
        $is_find = $Subscription->_find($where);
        $is_find = $Subscription->objToArr($is_find);
        if(!$is_find)return "repeat";
        $status=$is_find['status']?0:1;
        $bool=$Subscription->_update(['status'=>$status,'updated_at'=>date("Y-m-d H:i:s")],"id='{$data['delid']}'");
        return $bool;
    }

    public function getFindcategorical($id){
        $Subscription =new Subscriptionadmin();
        $where = "id='{$id}'";
        $is_find = $Subscription->_find($where);
        $is_find = $Subscription->objToArr($is_find);
        return $is_find;
    }

    public function Editcaregorical($data){
        $Subscription =new Subscriptionadmin();
        $is_find=$this->getFindcategorical($data['id']);
        if($is_find['email']!=$data['email']){
            $where = "email='{$data['email']}'";
            $find = $Subscription->_find($where);
            $find = $Subscription->objToArr($find);
            if($find)return "repeat";
        }
        $bool=$Subscription->_update(['email'=>$data['email'],'status'=>$data['status'],'updated_at'=>date("Y-m-d H:i:s")],"id='{$data['id']}'");
        return $bool;
    }



}