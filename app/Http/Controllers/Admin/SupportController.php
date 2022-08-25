<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 15:49
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Http\Controllers\Admin;
use App\Services\SupportService;

class SupportController extends BaseController
{
    protected $supportService;

    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    public function list(){
        $param = request()->input();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["platform"] = isset($param['platform']) ? $param['platform'] : "-1";
        $query["start_date"] = isset($param['start_date']) ? $param['start_date'] : "";
        $query["end_date"] = isset($param['end_date']) ? $param['end_date'] : "";
        $data = $this->supportService->getList($param);
        $platform = $this->supportService->getPlatformKv();
        $product = $this->supportService->getProductKv();
        $email = $this->supportService->get_email();
        $type = $this->supportService->getTypeKv();
        $status = $this->supportService->getStatusKv();
        $development_language = $this->supportService->getDevelopmentLanguageKv();
        $admins = $this->supportService->getAdminsKv();
        return $this->view('list',compact('data','query','platform','product','type','status','admins','development_language','email'));
    }

    public function create(){
        $platform = $this->supportService->getPlatformKv();
        $product = $this->supportService->getProductKv();
        $type = $this->supportService->getTypeKv();
        $development_language = $this->supportService->getDevelopmentLanguageKv();
        $admins = $this->supportService->getAdminsKv();
        return $this->view('create',compact('platform','type','admins','development_language','product'));
    }

    public function store(){
        $param = request()->all()['data'];
        $unset = ['handler'];
        $check = $this->check_param_key_null($param,$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->supportService->store($param);
        if ("same_version" == $back){
            $result['code'] = 1000;
            $result['msg'] = '添加失败，存在相同数据，请重试';
        }else if(!empty($back)){
            flash('添加support成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('添加support失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加support失败';
        }
        return $result;
    }

    public function edit($id){
        $row = $this->supportService->getRow($id);
        $platform = $this->supportService->getPlatformKv();
        $product = $this->supportService->getProductKv();
        $type = $this->supportService->getTypeKv();
        $development_language = $this->supportService->getDevelopmentLanguageKv();
        $admins = $this->supportService->getAdminsKv();
        return $this->view('edit',compact('row','platform','type','admins','development_language','product'));
    }

    public function update($id){
        $param = request()->all()['data'];
        $unset = [];
        $check = $this->check_param_key_null($param,$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->supportService->update($param,$id);
        if ("same_version_no" == $back){
            $result['code'] = 1000;
            $result['msg'] = '修改失败，存在相同数据，请重试';
        }else if ("same_slug" == $back){
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('修改support成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('修改support失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '修改support失败';
        }
        return $result;
    }

    public function changeStatus(){
        $param = request()->all();
        if($param['demo']==""||$param['demo']==0){
            return ['code'=>0,'msg'=>"请选择邮件发送模板"];
        }
        $id = $param['id'];
        $res = $this->supportService->update_status($param);
        if($res){
            return ['code'=>1,'msg'=>"状态更新成功",'id'=>$id,'status'=>$param['status']];
        }else{
            return ['code'=>0,'msg'=>"状态更新失败"];
        }

    }

    public function softDel(){
        $param = request()->input();
        $id = $param['id'];
        if(!empty($id)){
            $row = $this->supportService->softDel($id);
            if(1==$row){
                $data['code'] = 0;
                flash('删除成功')->success()->important();
            }else{
                $data['code'] = 1;
                $data['msg'] = '删除失败';
                flash('删除失败')->error()->important();
            }
        }else{
            $data['code'] = 1;
            $data['msg'] = '参数有误，请重试';
        }
        return $data;
    }

    public function check_param_key_null($param,array $unset=[])
    {
        $data['code'] = 200;
        $data['msg'] = 'success';
        if ($param) {
            if (is_array($param)) {
                if(!empty($unset)){
                    foreach ($unset as $v){
                        unset($param[$v]);
                    }
                }
                foreach ($param as $key => $value) {
                    if (null==$value) {
                        $data['code'] = 500;
                        $data['msg'] = '请填写：' . $key;
                        break;
                    }
                }
            } else {
                $data['code'] = 500;
                $data['msg'] = '请传入正确的参数';
            }
        } else {
            $data['code'] = 500;
            $data['msg'] = '参数不能为空！';
        }
        return $data;
    }

}