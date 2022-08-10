<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/3
 * @Time: 15:36
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

declare (strict_types=1);

namespace App\Services;

use App\Models\ChangeLogs;

class ChangeLogsService
{
    protected $changeLogs;

    public function __construct(ChangeLogs $changeLogs)
    {
        $this->changeLogs = $changeLogs;
    }

    public function getList($param)
    {
        $where="1=1";
        if(isset($param['info'])&&$param['info']){

            $where.=" AND ".$param['query_type']." like '%".$param['info']."%'";
        }
        if(isset($param['platform'])&&'-1'!=$param['platform']){
            $where.=" AND platform = '".$param['platform']."'";
        }
        if(isset($param['start_date'])&&$param['start_date'] && isset($param['end_date'])&&$param['end_date']){
            $where.=" AND created_at BETWEEN '".$param['start_date']."' AND '".$param['end_date']."'";
        }elseif (isset($param['start_date'])&&$param['start_date'] && empty($param['end_date'])){
            $where.=" AND created_at >= '".$param['start_date']."'";
        }elseif (isset($param['end_date'])&&$param['end_date'] && empty($param['start_date'])){
            $where.=" AND created_at <= '".$param['end_date']."'";
        }
        if ($where){
            $data = changeLogs::whereRaw('is_delete = 0')->whereRaw($where)->orderByRaw('order_num,id desc')->paginate(10);
        }else{
            $data = changeLogs::whereRaw('is_delete = 0')->orderByRaw('order_num,id desc')->paginate(10);
        }
        return $data ?? [];
    }

    public function getRow($id)
    {
        $data = $this->changeLogs->_find('id = '.$id);
        return $data ?? [];
    }

    public function store($param)
    {
        $arr = $param;
        if($arr['version_no']){
            $list = $this->changeLogs->_find('is_delete = 0 AND version_no = '."'".$arr['version_no']."' AND platform = '".$arr['platform']."'"." AND product = '".$arr['product']."'"." AND development_language = '".$arr['development_language']."'");
            if ($list){
                return "same_version_no";
            }
        }
        if($arr['slug']){
            $list = $this->changeLogs->_find('is_delete = 0 AND slug = '."'".$arr['slug']."'");
            if ($list){
                return "same_slug";
            }
        }
        $row = $this->changeLogs->insertGetId($arr);
        return $row ?? '';
    }

    public function update($param,$id){
        $arr = $param;
        if($arr['version_no']){
            $list = $this->changeLogs->_find('is_delete = 0 AND version_no = '."'".$arr['version_no']."' AND platform = '".$arr['platform']."'"." AND product = '".$arr['product']."'"." AND development_language = '".$arr['development_language']."' ".'AND id <> '.$id);
            if ($list){
                return "same_version_no";
            }
        }
        if($arr['slug']){
            $list = $this->changeLogs->_find('is_delete = 0 AND slug = '."'".$arr['slug']."' ".'AND id <> '.$id);
            if ($list){
                return "same_slug";
            }
        }
        $row = $this->changeLogs->_update($arr,'id = '.$id);
        return $row ?? '';
    }

    public function softDel($id)
    {
        $row = $this->changeLogs->_update(['is_delete' => 1], 'id = ' . $id);
        return $row ?? '';

    }

    public function getPlatformKv()
    {
        $platform = $this->changeLogs->platform;
        return $platform ?? [];
    }

    public function getProductKv()
    {
        $platform = $this->changeLogs->product;
        return $platform ?? [];
    }

    public function getDevelopmentLanguageKv()
    {
        $platform = $this->changeLogs->development_language;
        return $platform ?? [];
    }

}