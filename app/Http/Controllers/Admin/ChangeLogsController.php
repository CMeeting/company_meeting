<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/3
 * @Time: 15:34
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Http\Controllers\Admin;

use App\Services\ChangeLogsService;

class ChangeLogsController extends BaseController
{
    protected $changeLogsService;

    public function __construct(ChangeLogsService $changeLogsService)
    {
        $this->changeLogsService = $changeLogsService;
    }

    public function list(){
        $param = request()->input();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["platform"] = isset($param['platform']) ? $param['platform'] : "-1";
        $query["start_date"] = isset($param['start_date']) ? $param['start_date'] : "";
        $query["end_date"] = isset($param['end_date']) ? $param['end_date'] : "";
        $platform = array_flip($this->changeLogsService->getPlatformKv());
        $data = $this->changeLogsService->getList($param);
        return $this->view('list',compact('data','platform','query'));
    }

    public function create(){
        $platform = $this->changeLogsService->getPlatformKv();
        return $this->view('create',compact('platform'));
    }

    public function store(){
        $param = request()->all()['data'];
        $unset = [];
        $check = $this->check_param_key_null($param,$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->changeLogsService->store($param);
        if ("same_version_no" == $back){
            $result['code'] = 1000;
            $result['msg'] = '修改失败，同一平台下的 Version_No 已重复，请重试';
        }else if ("same_slug" == $back){
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('添加Changelogs成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('添加Changelogs失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加Changelogs失败';
        }
        return $result;
    }

    public function edit($id){
        $row = $this->changeLogsService->getRow($id);
        $platform = $this->changeLogsService->getPlatformKv();
        return $this->view('edit',compact('row','platform'));
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
        $back = $this->changeLogsService->update($param,$id);
        if ("same_version_no" == $back){
            $result['code'] = 1000;
            $result['msg'] = '修改失败,同一平台下的 Version_No 已重复,请重试';
        }else if ("same_slug" == $back){
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('修改Changelogs成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('修改Changelogs失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '修改Changelogs失败';
        }
        return $result;
    }

    public function softDel(){
        $param = request()->input();
        $id = $param['id'];
        if(!empty($id)){
            $row = $this->changeLogsService->softDel($id);
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