<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 15:54
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

declare (strict_types=1);

namespace App\Services;

use Auth;
use App\Models\Support;
use Illuminate\Support\Facades\DB;

class SupportService
{
    protected $support;

    public function __construct(Support $support)
    {
        $this->support = $support;
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
            $data = support::whereRaw('is_delete = 0')->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        }else{
            $data = support::whereRaw('is_delete = 0')->orderByRaw('order_num,id desc')->paginate(10);
        }
        return $data ?? [];
    }

    public function getRow($id)
    {
        $data = $this->support->_find('id = '.$id);
        return $data ?? [];
    }

    function getRandStr($length){
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $randStr = str_shuffle($str);//打乱字符串
        $rands= substr($randStr,0,$length);//substr(string,start,length);返回字符串的一部分
        return $rands;
    }

    public function store($param)
    {
        $arr = $param;
        if($arr['version']){
            $list = $this->support->_find('is_delete = 0 AND version = '."'".$arr['version']."' AND platform = '".$arr['platform']."'"." AND product = '".$arr['product']."'"." AND development_language = '".$arr['development_language']."'"." AND type = '".$arr['type']."'");
            if ($list){
                return "same_version";
            }
        }
        $arr['create_user'] = Auth::guard('admin')->user()->id;
        $arr['order_no'] = self::getProductKv()[$arr['product']]['code'].self::getPlatformKv()[$arr['platform']]['code'].self::getDevelopmentLanguageKv()[$arr['development_language']]['code'].'0-'.self::getRandStr(4);
        $row = $this->support->insertGetId($arr);
        return $row ?? '';
    }

    public function update($param,$id){
        $arr = $param;
        if($arr['version']){
            $list = $this->support->_find('is_delete = 0 AND version = '."'".$arr['version']."' AND platform = '".$arr['platform']."' ".'AND id <> '.$id);
            if ($list){
                return "same_version_no";
            }
        }
        $arr['order_no'] = self::getProductKv()[$arr['product']]['code'].self::getPlatformKv()[$arr['platform']]['code'].self::getDevelopmentLanguageKv()[$arr['development_language']]['code'].'0-'.self::getRandStr(4);
        $row = $this->support->_update($arr,'id = '.$id);
        return $row ?? '';
    }

    public function softDel($id)
    {
        $row = $this->support->_update(['is_delete' => 1], 'id = ' . $id);
        return $row ?? '';

    }

    public function getPlatformKv()
    {
        $platform = $this->support->platform;
        return $platform ?? [];
    }

    public function getProductKv()
    {
        $platform = $this->support->product;
        return $platform ?? [];
    }

    public function getTypeKv()
    {
        $platform = $this->support->type;
        return $platform ?? [];
    }

    public function getStatusKv()
    {
        $platform = $this->support->status;
        return $platform ?? [];
    }

    public function getDevelopmentLanguageKv()
    {
        $platform = $this->support->development_language;
        return $platform ?? [];
    }

    public function getAdminsKv()
    {
        $data = [];
        $admins = Db::table('admins')
            ->select(DB::raw('id,name'))
            ->orderByRaw('id DESC')
            ->get()
            ->toArray();
        foreach ($admins as $v) {
            $data[$v->id] = $v->name;
        }
        return $data ?? [];
    }

    public static function selectHtml($pid)
    {
        $html = '';
        $arr = Db::table('support_rules')
//            ->select(DB::raw('id,name'))
            ->whereRaw('pid = '.$pid)
            ->orderByRaw('id ASC')
            ->get()
            ->toArray();
        foreach ($arr as $v){
                $html .= '<option value="'.$v->id.'">'.$v->title.'</option>';
        }
        echo $html;
    }



}