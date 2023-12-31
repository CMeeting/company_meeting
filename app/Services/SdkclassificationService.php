<?php
declare (strict_types=1);

namespace App\Services;

use App\Models\DocumentationModel as PlatformVersion;
use App\Models\SdkarticleModel as SdKArticle;
use App\Models\SdkclassificationModel as SdkClassification;

class SdkclassificationService
{
    public function __construct()
    {

    }

    public function getCategoricalData()
    {
        $SdkClassification = new SdkClassification();
        $where=array(["deleted","=",0]);
        $field = "id,title,lv,pid,displayorder,enabled,platformid,version";
        $order = "displayorder,id desc";
        $list1 = $SdkClassification->select($where, $field, $order);
        $list1 = $SdkClassification->objToArr($list1);
        $banben = $this->allVersion();
        if ($list1) {
            $data = $this->assemblyHtml($list1,$banben);
        } else {
            $data = '<div style="height: 300px; width: 100%; text-align: center; padding-top: 130px;"><div>暂无数据</div></div>';
        }
        return $data;
    }

    /**
     * @param $param
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function getCategorical($data=array())
    {
        $SdkClassification = new SdkClassification();
        $PlatformVersion = new PlatformVersion();
        $field = "id,name,pid";
        $pingt= $PlatformVersion->select(array(["deleted","=",0]), $field, "lv");
        $pingt= $PlatformVersion->objToArr($pingt);

        $where=array(["deleted","=",0]);
        if(count($data)>0){
            $where[]=['platformid','=',$data['platformid']];
            $where[]=['version','=',$data['version']];
        }
        $field = "id,title,lv,pid,displayorder,enabled,platformid,version";
        $order = "displayorder,id desc";
        $material = $SdkClassification->select($where, $field, $order);
        $material = $SdkClassification->objToArr($material);

        $arr_project = $this->menuLeft($material,$pingt);
        return $arr_project ?? [];
    }

    public function addEditcaregorical($param)
    {
        $SdkClassification = new SdkClassification();
        if (isset($param['data'])) {
            $data = $param['data'];
        }
        $lv = (isset($data['pid']) && $data['pid']) ? 2 : 1;
        $data['lv'] = $lv;
        if (isset($data['id'])) {
            $where = "id='{$data['id']}'";
            $is_find = $SdkClassification->find($where);
            $is_find = $SdkClassification->objToArr($is_find);
            if ($is_find['title'] != $data['title']) {
                $names = $SdkClassification->find("title='" . $data['title'] . "' and pid='{$data['pid']}' and platformid={$data['platformid']} and version={$data['version']} and deleted=0");
            }
            if (isset($names) && $names) {
                return "repeat";
            }

            $bool = $SdkClassification->update($data, $where);
            if($bool){
                $ids = $this->getIds($data['id'], "sdk_classification");
                $ids = implode(',', $ids);
                $SdkClassification->update(['platformid'=>$data['platformid'],'version'=>$data['version']], "id in(".$ids.")");
            }
        } elseif (isset($param['delid'])) {
            $bool = $SdkClassification->update(['deleted' => 1], "id=" . $param['delid']);
            if ($bool) {
                $ids = $this->getIds($param['delid'], "sdk_classification");
                $strids="";
                $i=0;
                foreach ($ids as $k=>$v){
                    if($i==0){
                        $strids.="'".$v."'";
                    }else{
                        $strids.=",'".$v."'";
                    }
                    $i++;
                }
                $ids = implode(',', $ids);
                $SdkClassification->update(['deleted' => 1], "id in(".$ids.")");
                $SdKArticle = new SdKArticle();
                $SdKArticle->_update(['deleted' => 1], "classification_ids in(".$strids.")");
            }
        } else {
            //dump($data);exit;
            $names = $SdkClassification->find("title='" . $data['title'] . "' and pid='{$data['pid']}' and platformid={$data['platformid']} and version={$data['version']} and deleted=0");
            if ($names) {
                return "repeat";
            }
            $bool = $SdkClassification->insertGetId($data);
        }
        return $bool;
    }

    public function getFindcategorical($id)
    {
        $SdkClassification = new SdkClassification();
        $where = "deleted=0 and id='$id'";
        $data = $SdkClassification->find($where);
        $data = $SdkClassification->objToArr($data);
        return $data;
    }


    function menuLeft($menu, $banben=array(),$id_field = 'id', $pid_field = 'pid', $lefthtml = '─', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($menu as $v) {
            if ($v[$pid_field] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin;
                $v['lefthtml'] = '├' . str_repeat($lefthtml, $lvl);
                if($lvl==0 && count($banben)>0){
                      foreach ($banben as $ks=>$vs){
                          if($v['platformid']==$vs['id']){
                              $v['platforname'] = $vs['name'];
                          }
                      }
                    foreach ($banben as $ks=>$vs){
                        if($v['version']==$vs['id']){
                            $v['versionname'] = $vs['name'];
                        }
                    }
                }
                $arr[] = $v;
                $arr = array_merge($arr, $this->menuLeft($menu,[], $id_field, $pid_field, $lefthtml, $v[$id_field], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }

    function assemblyHtml($data,$banben)
    {
        $html = '<ol class="dd-list">';
        foreach ($data as $k => $v) {
            if ($v['lv'] == 1) {
                $str=$this->assemblyVersion(array($v['platformid'],$v['version']),$banben);
                $html .= '<li class="dd-item dd3-item item_' . $v['id'] . '" data-id="' . $v['id'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;<font  style="font-size: 1em">排序</font>:[' . $v['displayorder'] . ']</span><span class="banben">'.$str.'</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                if ($v['enabled'] == 0) {
                    $html .= '<a style="text-decoration: none" type="button"  data-id="' . $v['id'] . '"  class="openBtn_' . $v['id'] . ' abutton cloros" data-style="zoom-out" onclick="show(' . $v['id'] . ');"><span class="ladda-label">show</span></a>';
                } else {
                    $html .= '<a style="text-decoration: none" type="button"  data-id="' . $v['id'] . '"  class="openBtn_' . $v['id'] . ' abutton cloros1" data-style="zoom-out" onclick="show(' . $v['id'] . ');"><span class="ladda-label">hide</span></a>';
                }
                $html .='<a style="text-decoration: none" onclick="level(' . $v['id'] . ')" class="abutton cloros2 "><i class="fa fa-files-o"></i> level</a>';
                $html .= '<a style="text-decoration: none" class="abutton cloros2" href="'.$this->headerurl().'/admin/createSdkClassification/'.$v['id'].'/'.$v['platformid'].'/'.$v['version'].'"><i class="fa fa-plus-circle "></i> add</a><a style="text-decoration: none" class="abutton cloros2" href="'.$this->headerurl().'/admin/createsdkDocumentation/' . $v['id'] . '"><i class="fa fa-plus-circle "></i> addArticle</a><a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="'.$this->headerurl().'/admin/updateSdkClassification/'.$v['id'].'"><i class="fa fa-edit"></i> edit</a><a onclick="del(' . $v['id'] . ')" class="abutton cloros4" style="text-decoration: none"><i class="fa fa-trash-o fa-delete"></i> del</a></div></div>';
                $html .= $this->assemPage($v['id'],$data);
                $html .= '</li>';
            }
        }
        $html .= '</ol>';
        return $html;
    }

    function assemPage($pid,$data,&$html = "")
    {
        $html .= '<ol class="dd-list">';
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid && $v['pid'] != 0) {
                $html .= '<li class="dd-item dd3-item" data-id="' . $v['id'] . '" parentid="' . $v['pid'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;排序:[' . $v['displayorder'] . ']</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                if ($v['enabled'] == 0) {
                    $html .= '<a style="text-decoration: none" type="button"  data-id="' . $v['id'] . '"  class="openBtn_' . $v['id'] . ' abutton cloros" data-style="zoom-out" onclick="show(' . $v['id'] . ');"><span class="ladda-label">show</span></a>';
                } else {
                    $html .= '<a style="text-decoration: none" type="button"  data-id="' . $v['id'] . '"  class="openBtn_' . $v['id'] . ' abutton cloros1" data-style="zoom-out" onclick="show(' . $v['id'] . ');"><span class="ladda-label">hide</span></a>';
                }
                $html .= '<a style="text-decoration: none" class="abutton cloros2" href="'.$this->headerurl().'/admin/createSdkClassification/'.$v['id'].'/'.$v['platformid'].'/'.$v['version'].'"><i class="fa fa-plus-circle "></i> add</a><a style="text-decoration: none" class="abutton cloros2" href="'.$this->headerurl().'/admin/createsdkDocumentation/' . $v['id'] . '"><i class="fa fa-plus-circle "></i> addArticle</a><a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="'.$this->headerurl().'/admin/updateSdkClassification/'.$v['id'].'"><i class="fa fa-edit"></i> edit</a><a style="text-decoration: none" onclick="del(' . $v['id'] . ')" class="abutton cloros4"><i class="fa fa-trash-o fa-delete"></i> del</a></div></div></li>';
                $html .= $this->assemPage($v['id'], $data);
            }
        }
        $html .= '</ol>';

        return $html;
    }

    function allVersion()
    {
        $PlatformVersion = new PlatformVersion();
        $where=array(["deleted","=",0]);
        $field = "id,name,pid";
        $order = "lv";
        $data = $PlatformVersion->select($where, $field, $order);
        $data = $PlatformVersion->objToArr($data);
        return $data;
    }
    function assemblyVersion($ids,$data){
        $str="";
        $i=1;
        foreach ($ids as $k=>$v){
            foreach ($data as $ks=>$vs){
                if($v==$vs['id']){
                    if($i==1){
                        $str.=$vs['name']."--";
                    }else{
                        $str.=$vs['name'];
                    }

                }
            }
            $i++;
        }

        return $str;
    }



    function getIds($id, $type, &$arr = array())
    {
        array_push($arr, $id);
        if ($type == "platform_version") {
            $operate = new PlatformVersion();
        }elseif ($type == "sdk_classification"){
            $operate = new SdkClassification();
        }
        $where=array(["deleted","=",0],['pid',"=",$id]);
        $lower_ids = $operate->select($where, "id");
        $lower_ids = $operate->objToArr($lower_ids);
        if ($lower_ids) {
            foreach ($lower_ids as $key => $val) {

                $this->getIds($val['id'], $type, $arr);
            }
        }
        return $arr;
    }

    function headerurl(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return  $http_type . $_SERVER['HTTP_HOST'];
    }

    public function update_level($id){
        $SdkClassification = new SdkClassification();
        $PlatformVersion = new PlatformVersion();
        $SdKArticle = new SdKArticle();
        $wheres = "deleted=0 and id='$id'";
        $data = $SdkClassification->find($wheres);
        $data = $SdkClassification->objToArr($data);
        $wheres = "deleted=0 and pid='{$data['platformid']}' and name='{$data['title']}'";
        $banben = $PlatformVersion->find($wheres);
        if(!$banben)return ['code'=>0,'msg'=>'相同平台下没有对应产品信息'];
        $banben =$PlatformVersion->objToArr($banben);
        $ids=$this->getIds($id,'sdk_classification');
        if($ids){
            $ids=implode(',',$ids);
            $SdkClassification->update([
                'version'=>$banben['id'],
                'updated_at'=>date("Y-m-d H:i:s")],
                "id in(".$ids.") and deleted=0");
            $SdKArticle->_update([
                'version'=>$banben['id'],
                'updated_at'=>date("Y-m-d H:i:s")],
                "classification_ids in(".$ids.") and deleted=0");

        }
        $rest=$SdkClassification->update([
            'lv'=>1,
            'pid'=>0,
            'version'=>$banben['id'],
            'updated_at'=>date("Y-m-d H:i:s")],"pid='{$id}' and deleted=0");
        if($rest){
            $SdkClassification->update(['deleted'=>1,'updated_at'=>date("Y-m-d H:i:s")],"id='{$id}' and deleted=0");
            return ['code'=>1];
        }else{
            return ['code'=>0,'msg'=>'调整失败'];
        }
    }

}