<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;

class BlogsController extends BaseController
{
    public function blog(Blog $blog)
    {
//        dd($blog);die;
        return $this->view('blog',compact('blog'));
    }

    public function create(){
        echo 'Add';
    }

    public function tags()
    {
        echo 'Tags';
//        dd($blog);die;
//        return $this->view('blog',compact('blog'));
    }

    public function types()
    {
        echo 'Types';
//        dd($blog);die;
//        return $this->view('blog',compact('blog'));
    }
}
