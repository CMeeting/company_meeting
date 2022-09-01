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

use App\Models\SupportLog;
use Auth;
use App\Models\Support;
use App\Models\Mailmagicboard;
use App\Models\DocumentationModel as PlatformVersion;
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
            switch ($param['query_type']){
                case 'id':
                    $where.=" AND ".$param['query_type']." = ".$param['info'];
                    break;
                case 'version':
                    $where.=" AND ".$param['query_type']." like '%".$param['info']."%'";
                    break;
                case 'slug':
                    $where.=" AND ".$param['query_type']." like '%".$param['info']."%'";
                    break;
            }
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
    public function getfind($id)
    {
        $data = $this->support->_find('id = '.$id);
        $data = $this->support->objToArr($data);
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
        $PlatformVersion = new PlatformVersion();
        $where = "deleted=0";
        $Versiondata = $PlatformVersion->selects($where);
        $product_name='';
        $version_name='';
        foreach ($Versiondata as $k=>$v){
            if($v['id']==$arr['platform']){
                $product_name=$v['name'];
            }
            if($v['id']==$arr['product']){
                $version_name=$v['name'];
            }
        }

        if($arr['version']){
            $list = $this->support->_find('is_delete = 0 AND version = '."'".$arr['version']."' AND platform = '".$arr['platform']."'"." AND product = '".$arr['product']."'"." AND development_language = '".$arr['development_language']."'"." AND type = '".$arr['type']."'");
            if ($list){
                return "same_version";
            }
        }
        $arr['create_user'] = Auth::guard('admin')->user()->id;
        $arr['order_no'] = self::getProductKv2()[$version_name]['code'].self::getPlatformKv2()[$product_name]['code'].self::getDevelopmentLanguageKv()[$arr['development_language']]['code'].'0-'.self::getRandStr(4);
        $row = $this->support->insertGetId($arr);
        return $row ?? '';
    }

    public function update($param,$id){
        $arr = $param;
//        $PlatformVersion = new PlatformVersion();
        $where = "deleted=0";
        //$Versiondata = $PlatformVersion->selects($where);
        $product_name='';
        $version_name='';
//        foreach ($Versiondata as $k=>$v){
//            if($v['id']==$arr['platform']){
//                $product_name=$v['name'];
//            }
//            if($v['id']==$arr['product']){
//                $version_name=$v['name'];
//            }
//        }
//        if($arr['version']){
//            $list = $this->support->_find('is_delete = 0 AND version = '."'".$arr['version']."' AND platform = '".$arr['platform']."' ".'AND id <> '.$id);
//            if ($list){
//                return "same_version_no";
//            }
//        }
//        $arr['order_no'] = self::getProductKv2()[$version_name]['code'].self::getPlatformKv2()[$product_name]['code'].self::getDevelopmentLanguageKv()[$arr['development_language']]['code'].'0-'.self::getRandStr(4);
        $row = $this->support->_update($arr,'id = '.$id);
        return $row ?? '';
    }

    public function softDel($id)
    {
        $row = $this->support->_update(['is_delete' => 1], 'id = ' . $id);
        return $row ?? '';

    }

    public function update_status($data)
    {
        $supportlog=new SupportLog();
        $supportdataarr=$this->getfind($data['id']);
        $status=$supportdataarr['status']+1;
        $datas=['order_no'=>$supportdataarr['order_no'],'status'=>$status,'info'=>$data['info'],'created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")];
        $supportlog->_insert($datas);

        $row = $this->support->_update(['status' => $status,'handler'=>Auth::guard('admin')->user()->id,'updated_at'=>date("Y-m-d H:i:s")], 'id = ' . $data['id']);
        if($row){
            return ['code'=>1,'status'=>$status];
        }else{
            return ['code'=>0,'msg'=>"更新失败"];
        }
    }

    public function getPlatformdata()
    {
        $PlatformVersion = new PlatformVersion();
        $where = "deleted=0";
        $Versiondata = $PlatformVersion->selects($where);
        $arr=[];
        foreach ($Versiondata as $k=>$v){
            $arr[$v['id']]=$v;
        }
        return $arr;
    }

    public function getPlatformKv()
    {
        $platform = $this->support->platform;
        return $platform ?? [];
    }
    public function getPlatformKv2()
    {
        $platform = $this->support->platformarr;
        return $platform ?? [];
    }

    public function getProductKv()
    {
        $platform = $this->support->product;
        return $platform ?? [];
    }
    public function getProductKv2()
    {
        $platform = $this->support->productarr;
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



    public function get_email(){
        $email=new Mailmagicboard();
        $data=$email->_where("deleted=0","id DESC","id,name");
        return $data;
    }


}