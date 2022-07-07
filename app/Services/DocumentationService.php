<?php

namespace App\Services;

use App\Models\DocumentationModel as PlatformVersion;
use App\Models\SdkclassificationModel as SdkClassification;
use App\Models\SdkarticleModel as SdKArticle;

class DocumentationService
{

    public function __construct()
    {

    }

    /**
     * 获取当前所有平台和版本分类数据
     * @return array
     * @throws \think\exception\DbException
     */
    public function getCategoricalData()
    {
        $PlatformVersion = new PlatformVersion();
        $where=array();
        $where[]=["deleted","=",0];
        $field = "id,name,lv,pid,displayorder,enabled";
        $order = "id desc";
        $list1 = $PlatformVersion->select($where, $field,$order);
        $list1 = $PlatformVersion->objToArr($list1);
        $cateList1 = array();
        $childCateList = array();
        foreach ($list1 as $v) {
            if ($v['pid'] == 0) {
                $cateList1[] = $v;
            } else {
                $childCateList[$v['pid']][] = $v;
            }
        }
        $data['parent'] = $list1;
        $data['cateList'] = $cateList1;
        $data['childCateList'] = $childCateList;
        return $data;
    }


    /**
     * @param $param
     * @remark 增/删改/平台版本分类
     * @return int|string
     * @throws \think\db\exception\DbException
     */
    public function addEditcaregorical($param)
    {
        $PlatformVersion = new PlatformVersion();
        if (isset($param['data'])) {
            $data = $param['data'];
        }
        $lv = (isset($data['pid']) && $data['pid']) ? 2 : 1;
        $data['lv'] = $lv;
        if (!isset($param['delid']) && $data['lv'] != 1) {
            unset($data['seotitel']);
            unset($data['h1title']);
        }
        if (isset($data['id'])) {
            $where = "id='{$data['id']}'";
            $is_find = $PlatformVersion->find($where);
            $is_find = $PlatformVersion->objToArr($is_find);
            if (($is_find['name'] != $data['name']) || (isset($data['seotitel']) && $is_find['seotitel'] != $data['seotitel']) || (isset($data['h1title']) && $is_find['h1title'] != $data['h1title'])) {
                if ($lv == 1) {
                    $names = $PlatformVersion->find("(name='" . $data['name'] . "' OR seotitel='" . $data['seotitel'] . "' or h1title='" . $data['h1title'] . "')  and id!='{$data['id']}' and deleted=0");
                } else {
                    $names = $PlatformVersion->find("name='" . $data['name'] . "' and pid='{$data['pid']}' and deleted=0");
                }
            }
            if ((isset($names) && $names)) {
                return "repeat";
            }
            $bool = $PlatformVersion->update($data, $where);
        } elseif (isset($param['delid'])) {
            $bool = $PlatformVersion->update(['deleted' => 1], "id=" . $param['delid']);
            if ($bool) {
                $PlatformVersion->update(['deleted' => 1], "pid=" . $param['delid']);
                $SdkClassification = new SdkClassification();
                $SdKArticle = new SdKArticle();
                $SdkClassification->update(['deleted' => 1], "platformid=" . $param['delid'] . " or version=" . $param['delid']);
                $SdKArticle->update(['deleted' => 1], "platformid=" . $param['delid'] . " or version=" . $param['delid']);
            }
        } else {
            if ($lv == 1) {
                $names = $PlatformVersion->find("(name='" . $data['name'] . "' or seotitel='" . $data['seotitel'] . "' or h1title='" . $data['h1title'] . "')  and pid='{$data['pid']}' and deleted=0");
                $names=$PlatformVersion->objToArr($names);
            } else {
                $names = $PlatformVersion->find("name='" . $data['name'] . "' and pid={$data['pid']} and deleted=0");
                $names=$PlatformVersion->objToArr($names);
            }
            if (isset($names) && $names) {
                return "repeat";
            }
            $bool = $PlatformVersion->insertGetId($data);
        }
        return $bool;
    }

    public function showHide($param)
    {
        if ($param['type'] == "platform_version") {
            $show = new PlatformVersion();
        } elseif ($param['type'] == "sdk_classification") {
            $show = new SdkClassification();
        } elseif ($param['type'] == "sdk_documentation") {
            $show = new SdKArticle();
        }
        if($param['type'] == "sdk_documentation"){
            $status = $show->_find("id=" . $param['id']);
        }else{
            $status = $show->find("id=" . $param['id']);
        }
        $status= $show->objToArr($status);
        $enabled = ($status['enabled'] == 1) ? 0 : 1;
        if ($param['type'] != "sdk_documentation") {
            if ($param['type'] == "platform_version") {
                $ids = $this->getIds($param['id'], "platform_version");
            } elseif ($param['type'] == "sdk_classification") {
                $ids = $this->getIds($param['id'], "sdk_classification");
            }
            $ids = implode(',', $ids);
            $bool = $show->update(['enabled' => $enabled], "id in(" . $ids . ")");
        } elseif ($param['type'] == "sdk_documentation") {
            $bool = $show->_update(['enabled' => $enabled], "id=" . $param['id']);
        }
        return array('code'=>$bool,"status"=>$enabled);
    }

    /**
     * @param $param
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function getCategorical()
    {
        $PlatformVersion = new PlatformVersion();
        $where=array(["deleted","=",0],['lv','=',1]);
        $field = "id,name,lv,pid,displayorder,enabled";
        $order = "displayorder desc";
        $material = $PlatformVersion->select($where, $field, $order);
        $material = $PlatformVersion->objToArr($material);
        $arr_project = $this->menuLeft($material);
        return $arr_project ?? [];
    }

    public function getFindcategorical($id)
    {
        $PlatformVersion = new PlatformVersion();
        $where = "deleted=0 and id='$id'";
        $data = $PlatformVersion->find($where);
        $data = $PlatformVersion->objToArr($data);
        return $data;
    }


    function menuLeft($menu, $id_field = 'id', $pid_field = 'pid', $lefthtml = '─', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($menu as $v) {
            if ($v[$pid_field] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin;
                $v['lefthtml'] = '├' . str_repeat($lefthtml, $lvl);
                $arr[] = $v;
                $arr = array_merge($arr, $this->menuLeft($menu, $id_field, $pid_field, $lefthtml, $v[$id_field], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }

    function getIds($id, $type, &$arr = array())
    {
        array_push($arr, intval($id));
        if ($type == "platform_version") {
            $operate = new PlatformVersion();
        } elseif ($type == "sdk_classification") {
            $operate = new SdkClassification();
        }
        $where=array(["deleted","=",0],["pid","=",$id]);
        $lower_ids = $operate->select($where, "id");
        $lower_ids = $operate->objToArr($lower_ids);
        if ($lower_ids) {
            foreach ($lower_ids as $key => $val) {

                $this->getIds($val['id'], $type, $arr);
            }
        }
        return $arr;
    }



}