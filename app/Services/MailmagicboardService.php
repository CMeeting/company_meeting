<?php

namespace App\Services;
use Auth;
use App\Models\Mailmagicboard as mail;

class MailmagicboardService
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


    public function getFindcategorical($id)
    {
        $email = new mail();
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



}