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

use App\Models\base\PlatformVersion;
use App\Models\ChangeLogs;
use App\Models\Support;
use App\Models\SupportLog;
use Illuminate\Support\Facades\DB;

class ChangeLogsService
{
    protected $changeLogs;

    public function __construct(ChangeLogs $changeLogs)
    {
        $this->changeLogs = $changeLogs;
    }

    public function getList($param)
    {
        $where = "1=1";
        if (isset($param['info']) && $param['info']) {

            switch ($param['query_type']){
                case 'id':
                    $where.=" AND ".$param['query_type']." = ".$param['info'];
                    break;
                case 'version_no':
                    $where.=" AND ".$param['query_type']." like '%".$param['info']."%'";
                    break;
                case 'slug':
                    $where.=" AND ".$param['query_type']." like '%".$param['info']."%'";
                    break;
            }
        }
        if (isset($param['platform']) && '-1' != $param['platform']) {
            $where .= " AND platform = '" . $param['platform'] . "'";
        }
        if (isset($param['product']) && '-1' != $param['product']) {
            $where .= " AND product = '" . $param['product'] . "'";
        }
        if (isset($param['development_language']) && '-1' != $param['development_language']) {
            $where .= " AND development_language = '" . $param['development_language'] . "'";
        }
        if (isset($param['start_date']) && $param['start_date'] && isset($param['end_date']) && $param['end_date']) {
            $where .= " AND updated_at BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        } elseif (isset($param['start_date']) && $param['start_date'] && empty($param['end_date'])) {
            $where .= " AND updated_at >= '" . $param['start_date'] . "'";
        } elseif (isset($param['end_date']) && $param['end_date'] && empty($param['start_date'])) {
            $where .= " AND updated_at <= '" . $param['end_date'] . "'";
        }
        if ($where) {
            $data = changeLogs::whereRaw('is_delete = 0')->whereRaw($where)->orderByRaw('order_num,id desc')->paginate(10);
        } else {
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
        $arr = $param['data'];
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
        if($param['support']){
            $support=new Support();
            $email = new EmailService();
            $maile = new MailmagicboardService();
            $mailedatas = $maile->getFindcategorical(28);
            $supportlog=new SupportLog();
            $support->_update(['status'=>4,'updated_at'=>date("Y-m-d H:i:s")],"id in({$param['support']})");
            $datas=[];
            $ids=explode(',',$param['support']);
            $supportdata=$support->_where("is_delete=0 and product='{$arr['product']}' and platform='{$arr['platform']}' and development_language='{$arr['development_language']}'");
            $supportdataarr=[];
            foreach ($supportdata as $k=>$v){
                $supportdataarr[$v['id']]=$v;
            }
            foreach ($ids as $k=>$v){
                $datas[]=['order_no'=>$supportdataarr[$v]['order_no'],'status'=>4,'info'=>'状态更新为已发布','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")];
                $email->sendDiyContactEmail($supportdataarr[$v],3,$supportdataarr[$v]['e_mail'],$mailedatas);
            }
            $supportlog->_insert($datas);
        }
        return $row ?? '';
    }

    public function update($param,$id){
        $arr = $param['data'];
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
        if($param['support']){
            $support=new Support();
            $email = new EmailService();
            $maile = new MailmagicboardService();
            $mailedatas = $maile->getFindcategorical(28);
            $supportlog=new SupportLog();
            $support->_update(['status'=>4,'updated_at'=>date("Y-m-d H:i:s")],"id in({$param['support']})");
            $datas=[];
            $ids=explode(',',$param['support']);
            $supportdata=$support->_where("is_delete=0 and product='{$arr['product']}' and platform='{$arr['platform']}' and development_language='{$arr['development_language']}'");
            $supportdataarr=[];
            foreach ($supportdata as $k=>$v){
                $supportdataarr[$v['id']]=$v;
            }
            foreach ($ids as $k=>$v){
                $datas[]=['order_no'=>$supportdataarr[$v]['order_no'],'status'=>4,'info'=>'状态更新为已发布','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")];
                 $email->sendDiyContactEmail($supportdataarr[$v],3,$supportdataarr[$v]['e_mail'],$mailedatas);
            }
            $supportlog->_insert($datas);
        }
        return $row ?? '';
    }

    public function softDel($id)
    {
        $row = $this->changeLogs->_update(['is_delete' => 1], 'id = ' . $id);
        return $row ?? '';

    }
    public function getPlatformdata()
    {
        $PlatformVersion = new \App\Models\DocumentationModel();
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
        $platform = DB::table("platform_version")
            ->where("lv", 1)
            ->where("deleted", 0)
            ->select("id", "name")
            ->get();
        return $platform ? two_to_one(obj_to_arr($platform), 'id', 'name') : [];
    }

    public function getProductKv()
    {
        $platform = DB::table("platform_version")
            ->where("lv", 2)
            ->where("deleted", 0)
            ->select("id", "name")
            ->get();
        return $platform ? two_to_one(obj_to_arr($platform), 'id', 'name') : [];
//        $platform = $this->changeLogs->product;
//        return $platform ?? [];
    }

    public function getDevelopmentLanguageKv()
    {
        $platform = $this->changeLogs->development_language;
        return $platform ?? [];
    }


    public function getsupport($data){
        $support=new Support();
        $admin=$this->getAdminsKv();
        $where="is_delete =0 and platform='{$data['platform']}' and product='{$data['product']}' and development_language='{$data['development_language']}' and status=3";
        $info=$support->_where($where,"updated_at desc");
        $html='';
        if(!$info){
            $html.='<p style="font-size: 30px;font-style: normal;position: absolute;left: 40%;top: 40%;">暂无相关数据！</p>';
        }else{
            $html.='<div class="form-group" style="padding-left: 18px;"><table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;"><thead><tr><th style="width: 30px">选择</th><th>order_no</th><th>e_mail</th><th>create_user</th><th>handler</th><th>updated_at</th></tr></thead><tbody>';
          foreach ($info as $k=>$v){
              $html.='<tr><td><label style="margin-bottom: 10px;margin-right: 5px"><input class="required class" type="checkbox" name="support[id]" value="'.$v['id'].'"></label></td><td id="td1_'.$v['id'].'">'.$v['order_no'].'</td><td id="td2_'.$v['id'].'">'.$v['e_mail'].'</td><td id="td3_'.$v['id'].'">'.$admin[$v['create_user']].'</td><td id="td4_'.$v['id'].'">'.$admin[$v['handler']].'</td><td id="td5_'.$v['id'].'">'.$v['updated_at'].'</td></tr>';
          }
          $html.='</tbody></table></div>';
        }
        return $html;
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

}