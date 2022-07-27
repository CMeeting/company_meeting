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
use http\Env;
use Illuminate\Support\Facades\Config;
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
    public function getBlogList($param)
    {
        $where="";
        if(isset($param['info'])&&$param['info']){

            $where=$param['query_type']."= '".$param['info']."'";
        }
        if ($where){
            $data = blog::whereRaw('is_delete = 0')->whereRaw($where)->orderByRaw('sort_id,id desc')->paginate(10);
        }else{
            $data = blog::whereRaw('is_delete = 0')->orderByRaw('sort_id,id desc')->paginate(10);
        }
        foreach ($data as $k=>$v){
            $v->tag_id = $this->getBlogTagTitle($v->tag_id);
        }
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
        $arr = $this->blogTypesModel->objToArr($this->blogTypesModel->getTypeskv());
        foreach ($arr as $v) {
            $data[$v['id']] = $v['title'];
        }
        return $data ?? [];
    }

    public function getBlogTypeAndSlugkv()
    {
        $arr = $this->blogTypesModel->objToArr($this->blogTypesModel->getTypeAndSlugkv());
        foreach ($arr as $v) {
            $data[$v['id']] = ['title' => $v['title'], 'slug' => $v['slug']];
        }
        return $data ?? [];
    }

    public function blogRow($id)
    {
        $data = $this->blogModel->_find('id =' . $id);
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
        if($arr['slug']){
            $list = $this->blogModel->_find('slug = '."'".$arr['slug']."'");
            if ($list){
                return "error";
            }
        }
        //上传图片
        $file = Input::file("cover");
        if ($file) {
            $result = $this->uploader->save($file, 'cover');
            if ($result) {
                $arr['cover'] = $result['path'];
//                $arr['cover'] = Config::get('ADMIN_HOST').$result['path'];
            }
        }
//        if (!empty($file)) {
//            $path = OssHelper::BLOG_PATH . '/' . rand(0, 99999) . time();
//            $url = OssHelper::upload('cover', $path)[0];
//            $url = str_replace('http://', 'https://', $url);
//            $arr['cover'] = $url;
//        }
        if (is_array($data['data']['tags'])) {
            $tag = '';
            foreach ($data['data']['tags'] as $v) {
                $tag .= $v . ',';
            }
            $arr['tag_id'] = rtrim($tag, ",");
            unset($arr['tags']);
        }else{
            $arr['tag_id'] = $data['data']['tags'];
            unset($arr['tags']);
        }
//        print_r($arr);die;
        $row = $this->blogModel->insertGetId($arr);
        return $row ??'';
    }

    public function blogUpdate($param,$id){
        $data = $param->request->all();
        $arr = $data['data'];
        if($arr['slug']){
            $list = $this->blogModel->_find('slug = '."'".$arr['slug']."' ".'AND id <> '.$id);
            if ($list){
                return "error";
            }
        }
        //上传图片
        $file = Input::file("cover");
        if ($file) {
            $result = $this->uploader->save($file, 'cover');
            if ($result) {
                $arr['cover'] = $result['path'];
            }
        }
        if (is_array($data['data']['tags'])) {
            $tag = '';
            foreach ($data['data']['tags'] as $v) {
                $tag .= $v . ',';
            }
            $arr['tag_id'] = rtrim($tag, ",");
            unset($arr['tags']);
        }else{
            $arr['tag_id'] = $data['data']['tags'];
            unset($arr['tags']);
        }
        $row = $this->blogModel->_update($arr,'id = '.$id);
        return $row ??'';
    }

    public function getBlogTypeList($param)
    {
        $where = 'is_delete = 0';
        if(isset($param['info'])&&$param['info']){
            $where .=" AND ".$param['query_type']." = '".$param['info']."'";
        }
        $order = 'sort_id,id DESC';
//        $data = $this->blogTypesModel->select($where,'*',$order);
        $data = BlogTypes::whereRaw($where)->orderByRaw($order)->paginate(10);
        return $data ?? [];
    }

    public function blogTypeRow($id)
    {
        $data = $this->blogTypesModel->_find('id = '.$id);
        return $data ?? [];
    }

    public function blogTypeCreate($param)
    {
        $arr = $param->request->all()['data'];
        if($arr['slug']){
            $list = $this->blogTypesModel->_find('slug = '."'".$arr['slug']."'");
            if ($list){
                return "error";
            }
        }
        $row = $this->blogTypesModel->insertGetId($arr);
        return $row ?? '';
    }

    public function blogTypeUpdate($param,$id){
        $arr = $param->request->all()['data'];
        if($arr['slug']){
            $list = $this->blogTypesModel->_find('slug = '."'".$arr['slug']."' ".'AND id <> '.$id);
            if ($list){
                return "error";
            }
        }
        $row = $this->blogTypesModel->_update($arr,'id = '.$id);
        return $row ?? '';
    }

    public function getBlogTagList($param)
    {
        $where="is_delete = 0";
        if(isset($param['info'])&&$param['info']){
            $where.=" and ".$param['query_type']."= '".$param['info']."'";
        }
        return blogTags::whereRaw($where)->orderByRaw('sort_id,id desc')->paginate(10);
    }

    public function blogTagRow($id)
    {
        $data = $this->blogTagsModel->_find('id = '.$id);
        return $data ?? [];
    }

    public function blogTagCreate($param)
    {
        $data = $param->request->all()['data'];
        if($data['title']){
            $tag = $this->blogTagsModel->_find(" title = '".$data['title']."'");
            if($tag){
                $row = 'error';
            }else{
                $row = $this->blogTagsModel->insertGetId($data);
            }
        }
        return $row??[];
    }

    public function blogTagUpdate($request,$id)
    {
        $data = $request->all()['data'];
        if($data['title']){
            $tag = $this->blogTagsModel->_find("id <> ".$id." AND title = '".$data['title']."'");
            if($tag){
                $row = 'error';
            }else{
                $row = $this->blogTagsModel->_update($data,'id = ' . $id);
            }
        }
        return $row ??'';
    }

    public function softDel($table,$id)
    {
        switch ($table) {
            case 'blog':
                $row = $this->blogModel->_update(['is_delete' => 1], 'id = ' . $id);
                break;
            case 'type':
                $data = $this->blogModel->objToArr($this->blogModel->select(['type_id' =>$id],'count(*)'))[0];
                if (isset($data)&&$data['count(*)']>0) {
                    $row = 'error';
                } else {
                    $row = $this->blogTypesModel->_update(['is_delete' => 1], 'id = ' . $id);
                }
                break;
            case 'tag':
                $row = $this->blogTagsModel->_update(['is_delete' => 1], 'id = ' . $id);
//                $row = BlogTags::find($id)->update(['is_delete' => 1]);
                break;
            default:
                $row = '';
        }
        return $row;

    }

    public function slugVerify($slug)
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
            $all_data = $this->blogModel->_where('type_id = '."'".$type[$category]."'".  ' AND is_delete = 0 ', 'sort_id,id DESC', $field);
            $data['data'] = $this->getSendList($all_data, $types);
            $data['currentCategory'] = $this->blogTypesModel->_where('slug = '."'".$category."'".  ' AND is_delete = 0 ', 'id DESC', 'title,slug,seo_title,keywords,description')[0];
        } else {
            $all_data = $this->blogModel->_where(' is_delete = 0 ', 'sort_id,id DESC', $field);
            $data['data'] = $this->getSendList($all_data, $types);
        }
        return $data;
    }

    public function getSendList($all_data, $types)
    {
        if ($all_data) {
            foreach ($all_data as $key => $value) {
//                print_r($all_data);die;
                $value['category'] = $types[$value['category']]['title'] ?? '';
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
        $row = $this->blogModel->objToArr($this->blogModel->_find('slug = ' ."'". $slug ."'"));
        $row['category'] = $types[$row['type_id']];
        $row['tags'] = $this->tagToArray($row['tag_id']);
        unset($row['tag_id']);
        $list_data = $this->blogModel->objToArr($this->blogModel->selectLimit([['type_id', '=', $row['type_id']], ['slug', '<>', $slug], ['is_delete', '=', '0']], $field, 'sort_id,id DESC', 3));
        $count = count($list_data);
        if (3 <= $count) {
            foreach ($list_data as $v) {
                $v['category'] = $types[$v['category']]['title'];
                $v['tags'] = $this->tagToArray($v['tags']);
                $list[] = $v;
            }
        } else if ($count > 0 && $count < 3) {
            $limit = 3 - $count;
            $blist = $this->blogModel->objToArr($this->blogModel->selectLimit([['type_id', '<>', $row['type_id']], ['slug', '<>', $slug], ['is_delete', '=', '0']], $field, 'id DESC', $limit));
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
            $blist = $this->blogModel->objToArr($this->blogModel->selectLimit([['slug', '<>', $slug], ['is_delete', '=', '0']], $field, 'id DESC', 3));
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
        $rows = $this->blogModel->objToArr($this->blogModel->selectLimit([['type_id', '=', $type[$category]], ['tag_id', 'like', $tag_like], ['is_delete', '=', '0']], $field, 'id DESC', 3));
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
            $blist = $this->blogModel->objToArr($this->blogModel->selectLimit([['type_id', '=', $type[$category]], ['id', 'not in', $ids], ['is_delete', '=', '0']], $field, 'id DESC', $limit));
            $arr = array_merge($rows, $blist);
            if (3 == count($arr)) {
                foreach ($arr as $value) {
                    $value['category'] = $types[$value['category']]['title'];
                    $value['tags'] = $this->tagToArray($value['tags']);
                    $list[] = $value;
                }
            } else {
                $num = 3 - count($arr);
                $clist = $this->blogModel->objToArr($this->blogModel->selectLimit([['is_delete', '=', '0']], $field, 'id DESC', $num));
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