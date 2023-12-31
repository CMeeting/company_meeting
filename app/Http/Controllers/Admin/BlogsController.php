<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Blog\BlogTagRequest;
use App\Services\BlogService;
use App\Services\OssService;
use Illuminate\Http\Request;

class BlogsController extends BaseController
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function blog()
    {
        $param = request()->input();
        $types = $this->blogService->getBlogTypeskv();
        $tags = $this->blogService->getBlogTagskv();
        $data = $this->blogService->getBlogList($param);
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["type_id"] = isset($param['type_id']) ? $param['type_id'] : "";
        $query["start_date"] = isset($param['start_date']) ? $param['start_date'] : "";
        $query["end_date"] = isset($param['end_date']) ? $param['end_date'] : "";
        return $this->view('blog/blog',compact('data','types','tags','query'));
    }

    public function blogCreate(){
        $types = $this->blogService->getBlogTypeskv();
        $tags = $this->blogService->getBlogTagskv();
        return $this->view('blog/create',compact('tags','types'));
    }

    public function blogStore(){
        $param = request()->all();
        $request = request();
        $unset = [];
        $types = $this->blogService->getBlogTypeskv();
        if(isset($types[$param["data"]['type_id']])){
            if (isset($param["data"]['type_id']) && 'Products' != $types[$param["data"]['type_id']]){
                $unset = ['abstract'];
            }
        }else{
            $result['code'] = 1000;
            $result['msg'] = 'category状态异常：请检查category是否被删除';
            return $result;
        }
        if(!isset($param['tags']) || empty($param['tags'])){
            $result['code'] = 1000;
            $result['msg'] = '请选择：Tags';
            return $result;
        }
        $check = $this->check_param_key_null($param["data"],$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->blogService->blogCreate($request);
        if ("error" == $back){
//            flash('slug已存在')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('添加Blog成功')->success()->important();
            $result['code'] = 200;
            return $result;
        }else{
            flash('添加Blog失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加Blog失败';
        }
        return $result;
//        return redirect()->route('blogs.blog');
    }

    public function blogEdit($id){
        $types = $this->blogService->getBlogTypeskv();
        $tags = $this->blogService->getBlogTagskv();
        $row = $this->blogService->blogRow($id);
        return $this->view('blog/edit', compact('types','tags','row'));
    }

    public function blogUpdate($id){
        $param = request()->all();
        $request = request();
        $unset = [];
        $types = $this->blogService->getBlogTypeskv();
        if(isset($types[$param["data"]['type_id']])){
            if (isset($param["data"]['type_id']) && 'Products' != $types[$param["data"]['type_id']]){
                $unset = ['abstract'];
            }
        }else{
            $result['code'] = 1000;
            $result['msg'] = 'category状态异常：请检查category是否被删除';
            return $result;
        }
        if(!isset($param['tags']) || empty($param['tags'])){
            $result['code'] = 1000;
            $result['msg'] = '请选择：Tags';
            return $result;
        }
        $check = $this->check_param_key_null($param["data"],$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->blogService->blogUpdate($request, $id);
        if ("error" == $back){
//            flash('slug已存在')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('修改Blog成功')->success()->important();
            $result['code'] = 200;
            return $result;
        }else{
            flash('修改Blog失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '修改Blog失败';
        }
        return $result;

//        $param = request();
//        $back = $this->blogService->blogUpdate($param, $id);
//        if ("error" == $back){
//            flash('slug已存在')->error()->important();
//        }else if(!empty($back)){
//            flash('更新Blog成功')->success()->important();
//        }else{
//            flash('更新Blog失败')->error()->important();
//        }
//        return redirect()->route('blogs.blog');
    }

    public function tags()
    {
        $param = request()->input();
        $data = $this->blogService->getBlogTagList($param);
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        return $this->view('tags/tag',compact('data','query'));
    }

    public function tagCreate(){
        return $this->view('tags/create');
    }

    public function tagStore(){
        $param = request();
        $unset = [];
        $check = $this->check_param_key_null($param["data"],$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->blogService->blogTagCreate($param);
        if('error'==$back){
//            flash('添加失败,Tag的title不能重复')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加失败,Tag的title不能重复';
        }else if(!empty($back)){
            flash('添加Tag成功')->success()->important();
            $result['code'] = 200;
            return $result;
        }else{
            flash('添加Tag失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加Tag失败';
        }
//        return redirect()->route('blogs.tags');
        return $result;
    }

    public function tagEdit($id){
        $row = $this->blogService->blogTagRow($id);
        return $this->view('tags/edit', compact('row'));
    }

    public function tagUpdate(Request $request,$id){
        $param = request();
        $unset = [];
        $check = $this->check_param_key_null($param["data"],$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->blogService->blogTagUpdate($request, $id);
        if('error'==$back){
//            flash('更新失败,Tag的title不能重复')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '更新失败,Tag的title不能重复';
        }else if(!empty($back)){
            flash('更新Tag成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('更新Tag失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '更新Tag失败';
        }
//        return redirect()->route('blogs.tags');
        return $result;
    }

    public function types()
    {
        $param = request()->input();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $data = $this->blogService->getBlogTypeList($param);
//        print_r($data);die;
        return $this->view('types/type',compact('data','query'));
    }

    public function typeCreate(){
        return $this->view('types/create');
    }

    public function typeStore(){
        $param = request();
        $unset = [];
        $check = $this->check_param_key_null($param["data"],$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->blogService->blogTypeCreate($param);
        if ("same_title" == $back){
//            flash('添加失败,title不能重复')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加失败,title不能重复';
        }else if ("error" == $back){
//            flash('slug已存在')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('添加Category成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('添加Category失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '添加Category失败';
        }
//        return redirect()->route('blogs.types');
        return $result;
    }

    public function typeEdit($id){
        $row = $this->blogService->blogTypeRow($id);
        return $this->view('types/edit', compact('row'));
    }

    public function typeUpdate($id){
        $param = request();
        $unset = [];
        $check = $this->check_param_key_null($param["data"],$unset);
        if(500==$check['code']){
            $result['code'] = 1000;
            $result['msg'] = $check['msg'];
            return $result;
        }
        $back = $this->blogService->blogTypeUpdate($param, $id);
        if ("same_title" == $back){
//            flash('更新失败,title不能重复')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '更新失败,title不能重复';
        }else if ("error" == $back){
//            flash('slug已存在')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = 'slug已存在';
        }else if(!empty($back)){
            flash('更新Category成功')->success()->important();
            $result['code'] = 200;
        }else{
            flash('更新Category失败')->error()->important();
            $result['code'] = 1000;
            $result['msg'] = '更新Category失败';
        }
//        return redirect()->route('blogs.types');
        return $result;
    }

    public function softDel(){
        $param = request()->input();
        $table = $param['table'];
        $id = $param['id'];
        if(!empty($table)&&!empty($id)){
            $row = $this->blogService->softDel($table,$id);
            if(1==$row){
                $data['code'] = 0;
                flash('删除成功')->success()->important();
            }elseif ('error'==$row){
                $data['code'] = 1;
                $data['msg'] = '删除失败,此分类下存在Blog数据，无法删除';
//                flash('删除失败,此分类下存在Blog数据，无法删除')->error()->important();
            }
            else{
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

    public function editorUpload(){
//        $row['location'] = $this->editor_upload();
        $file = request()->file('file');
        $res = OssService::uploadFile($file);
        if(200 == $res['code']){
            $row['location'] = str_replace('http://', 'https://', $res['data']['url']);
        }
        print_r(json_encode($row));die;
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
