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
        $this->blogService->blogCreate($param);
        flash('添加Blog成功')->success()->important();
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
        $this->blogService->blogUpdate($param, $id);
        flash('更新成功')->success()->important();
        return redirect()->route('blogs.blog');
    }

    public function tags()
    {
        $data = $this->blogService->getBlogTagList();
//        print_r($data);die;
        return $this->view('tags/tag',compact('data'));
    }

    public function tagCreate(){
        return $this->view('tags/create');
    }

    public function tagStore(){
        $param = request();
        $this->blogService->blogTagCreate($param);
        flash('添加Tag成功')->success()->important();
        return redirect()->route('blogs.tags');
    }

    public function tagEdit($id){
        $row = $this->blogService->blogTagRow($id);
        return $this->view('tags/edit', compact('row'));
    }

    public function tagUpdate(Request $request,$id){
//        $param = request();
        $this->blogService->blogTagUpdate($request, $id);
        flash('更新成功')->success()->important();
        return redirect()->route('blogs.tags');
    }

    public function types()
    {
        $data = $this->blogService->getBlogTypeList();
//        print_r($data);die;
        return $this->view('types/type',compact('data'));
    }

    public function typeCreate(){
        return $this->view('types/create');
    }

    public function typeStore(){
        $param = request();
        $this->blogService->blogTypeCreate($param);
        flash('添加Category成功')->success()->important();
        return redirect()->route('blogs.types');
    }

    public function typeEdit($id){
        $row = $this->blogService->blogTypeRow($id);
        return $this->view('types/edit', compact('row'));
    }

    public function typeUpdate($id){
        $param = request();
        $this->blogService->blogTypeUpdate($param, $id);
        flash('更新成功')->success()->important();
        return redirect()->route('blogs.types');
    }

    public function softDel($table,$id){
        $row = $this->blogService->softDel($table,$id);
        if(1==$row){
            flash('删除成功')->success()->important();
        }elseif ('error'==$row){
            flash('删除失败,此分类下存在Blog数据，无法删除')->error()->important();
        }
        else{
            flash('删除失败')->error()->important();
        }
        return redirect()->back();
    }

    public function editorUpload(){
        $param = request();
        print_r($param);die;
        $this->editor_upload($param);
    }

}
