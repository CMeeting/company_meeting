<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/7/19
 * @Time: 18:00
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Http\Controllers\Api;

use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogsController
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function blogList()
    {
        $category = Request()->get('category');
        if (!empty($category)) {
            $list_data = $this->blogService->getBlogListForHtml($category);
        } else {
            $list_data = $this->blogService->getBlogListForHtml();
        }
        $data = $list_data;
        return $this->rendjson($data);

    }

    public function blogDetail()
    {
        $slug = Request()->get('slug');
        if (!empty($slug)) {
            $data = $this->blogService->getBlogDetailForHtml($slug);
            if (!empty($data)) {
                return $this->rendjson($data);
            } else {
                return $this->rendjson([], 403, 'No Result');
            }
        } else {
            return $this->rendjson([], 403, '参数错误，请检查！');
        }
    }

    public function getBlogForTags(){
        $category = Request()->get('category');
        $tag = Request()->get('tag');
        if(!empty($category)&&!empty($tag)){
            $data = $this->blogService->getBlogForTags($category,$tag);
            if ($data){
                return $this->rendjson($data);
            }else{
                return $this->rendjson([], 403, 'NO Result!');
            }

        }else{
            return $this->rendjson([], 403, '参数错误，请检查！');
        }

    }

    public function rendjson(array $data=[],$code=200,$msg='success'){
        return json_encode(['data'=>$data,'code'=>$code,'msg'=>$msg]);
    }
}