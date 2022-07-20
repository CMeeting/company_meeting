<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Blog\BlogTagRequest;
use App\Services\BlogService;
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
        return $this->view('blog/blog',compact('data','types','tags','query'));
    }

    public function blogCreate(){
        $types = $this->blogService->getBlogTypeskv();
        $tags = $this->blogService->getBlogTagskv();
        return $this->view('blog/create',compact('tags','types'));
    }

    public function blogStore(){
        $param = request();
        $back = $this->blogService->blogCreate($param);
        if ("error" == $back){
            flash('slug已存在')->error()->important();
        }else if(!empty($back)){
            flash('添加Blog成功')->success()->important();
        }else{
            flash('添加Blog失败')->error()->important();
        }
        return redirect()->route('blogs.blog');
    }

    public function blogEdit($id){
        $types = $this->blogService->getBlogTypeskv();
        $tags = $this->blogService->getBlogTagskv();
        $row = $this->blogService->blogRow($id);
        return $this->view('blog/edit', compact('types','tags','row'));
    }

    public function blogUpdate($id){
        $param = request();
        $back = $this->blogService->blogUpdate($param, $id);
        if ("error" == $back){
            flash('slug已存在')->error()->important();
        }else if(!empty($back)){
            flash('更新Blog成功')->success()->important();
        }else{
            flash('更新Blog失败')->error()->important();
        }
        return redirect()->route('blogs.blog');
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
        $back = $this->blogService->blogTagCreate($param);
        if(!empty($back)){
            flash('添加Tag成功')->success()->important();
        }else{
            flash('添加Tag失败')->error()->important();
        }
        return redirect()->route('blogs.tags');
    }

    public function tagEdit($id){
        $row = $this->blogService->blogTagRow($id);
        return $this->view('tags/edit', compact('row'));
    }

    public function tagUpdate(Request $request,$id){
//        $param = request();
        $back = $this->blogService->blogTagUpdate($request, $id);
        if(!empty($back)){
            flash('更新Tag成功')->success()->important();
        }else{
            flash('更新Tag失败')->error()->important();
        }
        return redirect()->route('blogs.tags');
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
        $back = $this->blogService->blogTypeCreate($param);
        if ("error" == $back){
            flash('slug已存在')->error()->important();
        }else if(!empty($back)){
            flash('添加Category成功')->success()->important();
        }else{
            flash('添加Category失败')->error()->important();
        }
        return redirect()->route('blogs.types');
    }

    public function typeEdit($id){
        $row = $this->blogService->blogTypeRow($id);
        return $this->view('types/edit', compact('row'));
    }

    public function typeUpdate($id){
        $param = request();
        $back = $this->blogService->blogTypeUpdate($param, $id);
        if ("error" == $back){
            flash('slug已存在')->error()->important();
        }else if(!empty($back)){
            flash('更新Category成功')->success()->important();
        }else{
            flash('更新Category失败')->error()->important();
        }
        return redirect()->route('blogs.types');
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
        $row['location'] = $this->editor_upload();
        print_r(json_encode($row));die;
    }

}
