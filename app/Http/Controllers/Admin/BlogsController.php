<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Blog\BlogTagRequest;
use App\Services\BlogService;

class BlogsController extends BaseController
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function blog()
    {
//        dd($blog);die;
        $data = $this->blogService->getBlogList();
//        print_r($data);die;
        return $this->view('blog/blog',compact('data'));
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

    public function tagUpdate($id){
        $param = request();
        $this->blogService->blogTagUpdate($param, $id);
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
        $this->blogService->softDel($table,$id);
        flash('删除成功')->success()->important();
        return redirect()->back();
    }
}
