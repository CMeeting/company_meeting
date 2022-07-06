<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/4/20
 * @Time: 18:09
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

declare (strict_types=1);

namespace App\Services;



use App\Handlers\ImageUploadHandler;
use App\Models\Blog;
use App\Models\BlogTags;
use App\Models\BlogTypes;
use Illuminate\Support\Facades\Input;

class BlogService
{
    /**
     * for admin--------------
     */
    protected $blogModel;
    protected $blogTypesModel;
    protected $blogTagsModel;
    protected $uploader;

    public function __construct(Blog $blogModel,BlogTypes $blogTypesModel,BlogTags $blogTagsModel,ImageUploadHandler $imageUploadHandler)
    {
        $this->blogModel = $blogModel;
        $this->blogTypesModel = $blogTypesModel;
        $this->blogTagsModel = $blogTagsModel;
        $this->uploader = $imageUploadHandler;
    }
    public function getBlogList()
    {
//        $data = array();
//        $where[] = ['is_delete','=','0'];
////        $p = CommonService::buildWhere($param);
////        if ($p) {
////            $where .= 'AND ' . $p;
////        } else {
////            $where .= $p;
////        }
//        $order = 'sort_id,id DESC';
//        $data = $this->blogModel->select($where,'*',$order);
//        $data = $this->blogModel->objToArr($data);
//        foreach ($data as $k=>$v){
//            $v['tag_id'] = self::getBlogTagTitle($v['tag_id']);
//            $datas[$k] = $v;
//        }
////        $datas = Blogs::paginate($where, $sort);
////        $data['data'] = $datas->toArray();
////        $data['page'] = $datas->render();
////        $data['assign_where'] = CommonService::buildAssignWhere($param);
//        return $datas ?? [];
        $data = blog::whereRaw('is_delete = 0')->orderByRaw('sort_id,id desc')->paginate(10);
        foreach ($data as $k=>$v){
            $v->tag_id = $this->getBlogTagTitle($v->tag_id);
//            echo $v->tag_id."<br>";
        }
//        print_r($data);die;
        return $data??[];
    }

    public function getBlogTagskv()
    {
        $arr = json_decode(json_encode($this->blogTagsModel->getTagskv()), true);
//        $arr = $this->blogTagsModel->objToArr(BlogTags::getTagskv());
        foreach ($arr as $v) {
            $data[$v['id']] = $v['title'];
        }
        return $data ?? [];
    }

    public function getBlogTypeskv()
    {
        $arr = $this->blogTypesModel->objToArr(BlogTypes::getTypeskv());
        foreach ($arr as $v) {
            $data[$v['id']] = $v['title'];
        }
        return $data ?? [];
    }

    public function getBlogTypeAndSlugkv()
    {
        $arr = BlogTypes::getTypeAndSlugkv();
        foreach ($arr as $v) {
            $data[$v['id']] = ['title' => $v['title'], 'slug' => $v['slug']];
        }
        return $data ?? [];
    }

    public function blogRow($id)
    {
        $data = Blog::find('id =' . $id);
        return $data ?? [];
    }

    public function getBlogTagTitle($tags)
    {
        $tag_info = '';
        $arr = json_decode(json_encode($this->blogTagsModel->getTagskv()), true);
        foreach ($arr as $v) {
            $tags_kv[$v['id']] = $v['title'];
        }
        if (!empty($tags)) {
            $tags = explode(',', $tags);
            foreach ($tags as $v) {
                $t = $tags_kv[$v] ?? '';
                $tag_info .= $t . ' | ';
            }
            $tag_info = rtrim($tag_info, " | ");
        }
        return $tag_info;
    }

    public function blogCreate($param)
    {
//        dd($param);die;
        $data = $param->request->all();
        $arr = $data['data'];
        //上传图片
        $file = Input::file("cover");
        if ($file) {
            $result = $this->uploader->save($file, 'cover');
            if ($result) {
                $arr['cover'] = $result['path'];
            }
        }
//        if (!empty($file)) {
//            $path = OssHelper::BLOG_PATH . '/' . rand(0, 99999) . time();
//            $url = OssHelper::upload('cover', $path)[0];
//            $url = str_replace('http://', 'https://', $url);
//            $arr['cover'] = $url;
//        }
        if ($data['tags']) {
            $tag = '';
            foreach ($data['tags'] as $v) {
                $tag .= $v . ',';
            }
            $arr['tag_id'] = rtrim($tag, ",");
        }
//        print_r($arr);die;
        $row = Blog::insertGetId($arr);
        return $row ??'';
    }

    public function blogUpdate($param,$id){
        $data = $param->request->all();
        $arr = $data['data'];
        //上传图片
        $file = Input::file("cover");
        if ($file) {
            $result = $this->uploader->save($file, 'cover');
            if ($result) {
                $arr['cover'] = $result['path'];
            }
        }
        if ($data['tags']) {
            $tag = '';
            foreach ($data['tags'] as $v) {
                $tag .= $v . ',';
            }
            $arr['tag_id'] = rtrim($tag, ",");
        }
//        print_r($arr);die;
        $row = Blog::update($arr,'id = '.$id);
        return $row ??'';
    }

    public function getBlogTypeList()
    {
        $where[] = ['is_delete','=','0'];
        $order = 'sort_id,id DESC';
        $data = $this->blogTypesModel->select($where,'*',$order);
        return $data ?? [];
    }

    public function blogTypeRow($id)
    {
        $data = BlogTypes::find('id = '.$id);
        return $data ?? [];
    }

    public function blogTypeCreate($param)
    {
        $arr = $param->request->all()['data'];
        $row = BlogTypes::insertGetId($arr);
        return $row ?? '';
    }

    public function blogTypeUpdate($param,$id){
        $arr = $param->request->all()['data'];
        $row = BlogTypes::update($arr,'id = '.$id);
        return $row ?? '';
    }

    public function getBlogTagList()
    {
//        $where[] = ['is_delete','=','0'];
//        $order = 'sort_id,id DESC';
//        $data = $this->blogTagsModel->select($where,'*',$order);

//        return $data ?? [];
        return blogTags::whereRaw('is_delete = 0')->orderByRaw('sort_id,id desc')->paginate(10);
    }

    public function blogTagRow($id)
    {
        $data = BlogTags::find($id);
        return $data ?? [];
    }

    public function blogTagCreate($request)
    {
//        $data = $param->request->all()['data'];
//        $data['created_at'] = date('Y-m-d H:i:s',time());
//        $data['updated_at'] = date('Y-m-d H:i:s',time());
//        $row = $this->blogTagsModel->insertGetId($data);
        $data = $request->all();
        $tag = BlogTags::insert($data['data']);
        return $tag??[];
    }

    public function blogTagUpdate($request,$id)
    {
        $data = $request->all();
//        dd($data['data']);die;
        $tag = BlogTags::find($id);
        $tag->update($data['data']);
//        $row = blogTags::update($data,'id = ' . $id);
        return $tag ??'';
    }

    public function softDel($table,$id)
    {
        switch ($table) {
            case 'blog':
                $row = Blog::update(['is_delete' => 1], 'id = ' . $id);
                break;
            case 'type':
                $data = $this->blogModel->objToArr(Blog::select(['type_id' =>$id],'count(*)'))[0];
                if (isset($data)&&$data['count(*)']>0) {
                    $row = 'error';
                } else {
                    $row = BlogTypes::update(['is_delete' => 1], 'id = ' . $id);
                }
                break;
            case 'tag':
//                $row = BlogTags::update(['is_delete' => 1], 'id = ' . $id);
                $row = BlogTags::find($id)->update(['is_delete' => 1]);
                break;
            default:
                $row = '';
        }
        return $row;

    }

    public function slugVerify($param)
    {
        if (isset($param['id'])) {
            $row = Blogs::find([['slug', '=', $param['slug']], ['id', '<>', $param['id']]]);
        } else {
            $row = Blogs::find(['slug' => $param['slug']]);
        }
        return $row ?? '';
    }

    public function typeSlugVerify($param)
    {
        if (isset($param['id'])) {
            $row = BlogTypes::find([['slug', '=', $param['slug']], ['id', '<>', $param['id']]]);
        } else {
            $row = BlogTypes::find(['slug' => $param['slug']]);
        }
        return $row ?? '';
    }


    /**
     * for api------------
     */

    public function getBlogListForHtml($category = '')
    {
        $field = 'id,title_h1,slug,type_id as category,tag_id as tags,cover,sort_id,created_at';
        $types = $this->getBlogTypeAndSlugkv();
        foreach ($types as $arr) {
            $cate[] = $arr;
        }
        $data['categories'] = $cate ?? [];
        if ($types) {
            foreach ($types as $k => $v) {
                $type[$v['slug']] = $k;
            }
        }
        if ($category) {
            $all_data = Blogs::where(['type_id' => $type[$category]], ' is_delete = 0 ', 'sort_id,id DESC', $field);
            $data['data'] = $this->getSendList($all_data, $types);
            $data['currentCategory'] = BlogTypes::where(['slug' => $category], ' is_delete = 0 ', '', 'title,slug,seo_title,keywords,description')->toArray()[0];
        } else {
            $all_data = Blogs::where('', ' is_delete = 0 ', 'sort_id,id DESC', $field);
            $data['data'] = $this->getSendList($all_data, $types);
        }
        return $data;
    }

    public function getSendList($all_data, $types)
    {
        if ($all_data) {
            foreach ($all_data as $key => $value) {
//                print_r($all_data);die;
                $value['category'] = $types[$value['category']]['title'];
                $tag_id = explode(',', $value['tags']);
                $tags = $this->getBlogTagskv();
                $tag = '';
                foreach ($tag_id as $v) {
                    if (isset($tags[$v])) {
                        $tag .= $tags[$v] . ',';
                    }
                }
                $tag = explode(',', rtrim($tag, ','));
                $value['tags'] = $tag ?? [];
                $value['year'] = date('Y', strtotime($value['created_at']));
                $data[$key] = $value;
            }
        }
        return $data ?? [];
    }

    public function getBlogDetailForHtml($slug)
    {
        $field = 'id,title_h1,slug,type_id as category,tag_id as tags,cover,sort_id,created_at';
        $types = $this->getBlogTypeAndSlugkv();
        $row = Blogs::find(['slug' => $slug]);
        $row['category'] = $types[$row['type_id']];
        $row['tags'] = $this->tagToArray($row['tag_id']);
        unset($row['tag_id']);
        $list_data = Blogs::selectLimit([['type_id', '=', $row['type_id']], ['slug', '<>', $slug], ['is_delete', '=', '0']], $field, 'sort_id,id DESC', 3);
        $count = count($list_data);
        if (3 <= $count) {
            foreach ($list_data as $v) {
                $v['category'] = $types[$v['category']]['title'];
                $v['tags'] = $this->tagToArray($v['tags']);
                $list[] = $v;
            }
        } else if ($count > 0 && $count < 3) {
            $limit = 3 - $count;
            $blist = Blogs::selectLimit([['type_id', '<>', $row['type_id']], ['slug', '<>', $slug], ['is_delete', '=', '0']], $field, 'id DESC', $limit);
            foreach ($list_data as $v) {
                $v['category'] = $types[$v['category']]['title'];
                $v['tags'] = $this->tagToArray($v['tags']);
                $list[] = $v;
            }
            foreach ($blist as $item) {
                $item['category'] = $types[$item['category']]['title'];
                $item['tags'] = $this->tagToArray($item['tags']);
                $list[] = $item;
            }
        } else {
            $blist = Blogs::selectLimit([['slug', '<>', $slug], ['is_delete', '=', '0']], $field, 'id DESC', 3);
            foreach ($blist as $item) {
                $item['category'] = $types[$item['category']]['title'];
                $item['tags'] = $this->tagToArray($item['tags']);
                $list[] = $item;
            }
        }
        unset($row['type_id'], $row['is_delete']);
        $data['data'] = $row;
        $data['list'] = $list ?? [];
        return $data ?? [];
    }

    public function tagToArray($tag)
    {
        $tag_id = explode(',', $tag);
        $tags = $this->getBlogTagskv();
        $arr = '';
        foreach ($tag_id as $v) {
            if (isset($tags[$v])) {
                $arr .= $tags[$v] . ',';
            }
        }
        $arr = explode(',', rtrim($arr, ','));
        return $arr ?? [];
    }

    public function getBlogForTags($category, $tag)
    {
        $field = 'id,title_h1,slug,type_id as category,tag_id as tags,cover,abstract,created_at';
        $types = $this->getBlogTypeAndSlugkv();
        if ($types) {
            foreach ($types as $k => $v) {
                $type[$v['slug']] = $k;
            }
        }
        $tags = $this->getBlogTagskv();
        if ($tags) {
            foreach ($tags as $key => $value) {
                $tag_kv[$value] = $key;
            }
        }
        $tag_like = '%' . $tag_kv[$tag] . '%';
        $rows = Blogs::selectLimit([['type_id', '=', $type[$category]], ['tag_id', 'like', $tag_like], ['is_delete', '=', '0']], $field, 'id DESC', 3);
        if (3 <= count($rows)) {
            foreach ($rows as $v) {
                $v['category'] = $types[$v['category']]['title'];
                $v['tags'] = $this->tagToArray($v['tags']);
                $list[] = $v;
            }
        } else {
            $limit = 3 - count($rows);
            $ids = '';
            foreach ($rows as $r) {
                $ids .= $r['id'] . ',';
            }
            $ids = explode(',', rtrim($ids, ','));
            $blist = Blogs::selectLimit([['type_id', '=', $type[$category]], ['id', 'not in', $ids], ['is_delete', '=', '0']], $field, 'id DESC', $limit);
            $arr = array_merge($rows, $blist);
            if (3 == count($arr)) {
                foreach ($arr as $value) {
                    $value['category'] = $types[$value['category']]['title'];
                    $value['tags'] = $this->tagToArray($value['tags']);
                    $list[] = $value;
                }
            } else {
                $num = 3 - count($arr);
                $clist = Blogs::selectLimit([['is_delete', '=', '0']], $field, 'id DESC', $num);
                $arrs = array_merge($arr, $clist);
                foreach ($arrs as $items) {
                    $items['category'] = $types[$items['category']]['title'];
                    $items['tags'] = $this->tagToArray($items['tags']);
                    $list[] = $items;
                }
            }
        }
        return $list ?? [];
    }

}