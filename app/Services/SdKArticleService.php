<?php
declare (strict_types=1);

namespace App\Services;
use App\Models\DocumentationModel as PlatformVersion;
use App\Models\SdkclassificationModel as SdkClassification;
use App\Models\SdkarticleModel as SdKArticle;

class SdKArticleService
{
    public function __construct()
    {

    }


    public function sele_list($param)
    {
//        $where=array();
        $where='';
//        $where[]=['deleted','=',0];
        $where.='deleted = 0';
        if(isset($param['platformid']) &&$param['platformid']){
//            $where[]=['platformid','=',$param['platformid']];
            $where.='AND platformid = '.$param['platformid'];
        }
        if(isset($param['version']) &&$param['version']){
//            $where[]=['version','=',$param['version']];
            $where.='AND version = '.$param['version'];
        }
        if(isset($param['classification']) &&$param['classification']){
//            $where[]=['classification_ids','=',$param['classification']];
            $where.='AND classification_ids = '.$param['classification_ids'];
        }

        $SdKArticle=new SdKArticle();
//        $data=$SdKArticle->paginates($where,"*","displayorder,id desc",10);
        $data=$SdKArticle->whereRaw($where)->orderByRaw('displayorder,id desc')->paginate(10);;
//        dd($data);die;
        $classification=$this->allCategories();
        $banben=$this->allVersion();
        if(!empty($data)){
            foreach ($data as $k=>$v){
//                dump($v);
//                echo "-----------<br>";
                $fenlei=$this->assemblyClassification($v->classification_ids,$classification);
                $v->classification=$fenlei?implode("--",$fenlei):"";
                $v->platformversion=$this->assemblyVersion(array($v->platformid,$v->version),$banben);
//                dump($v);die;
            }
        }
//        dd($data);die;
        return $data;
    }

    public function appends(array $append)
    {
        foreach ($append as $k => $v) {
            if ($k !== $this->options['var_page']) {
                $this->options['query'][$k] = $v;
            }
        }

        return $this;
    }


    public function addEditcaregorical($param,$user_id=0)
    {
        $SdkClassification = new SdkClassification();
        $SdKArticle=new SdKArticle();
        if (isset($param['delid'])) {
            $bool = $SdKArticle->update(['deleted' => 1], "id=" . $param['delid']);
            if($bool){
                return ['code'=>1];
            }else{
                return ['code'=>0,'msg'=>'删除失败'];
            }
        }
        if (isset($param['data'])) {
            $data = $param['data'];
            $classification_ids=$data['classification_ids'];
            $wheres = "deleted=0 and id='$classification_ids'";
            $classification_data = $SdkClassification->find($wheres);
            $classification_data=$SdkClassification->objToArr($classification_data);
            if (isset($data['id'])){
                $is_edit=$SdKArticle->find("id='{$data['id']}'");
                $is_edit=$SdKArticle->objToArr($is_edit);
                if($data['titel']!=$is_edit['titel'] || $data['seotitel']!=$is_edit['seotitel']){
                    $is_titel=$SdKArticle->find("classification_ids='".$classification_ids."' and id!={$data['id']}  and (titel='".$data['titel']."' or seotitel='".$data['seotitel']."')  and deleted=0");
                }
                if($data['slug']!=$is_edit['slug']){
                    $slug=$SdKArticle->find("slug='".$data['slug']."'  and deleted=0 and platformid=".$classification_data['platformid']);
                }
            }else{
                $is_titel=$SdKArticle->find("classification_ids='".$classification_ids."' and (titel='".$data['titel']."' or seotitel='".$data['seotitel']."')  and deleted=0");
                $slug=$SdKArticle->find("slug='".$data['slug']."'  and deleted=0 and platformid=".$classification_data['platformid']);
            }
        }
        if(isset($is_titel) && $is_titel){
            return ['code'=>0,'msg'=>"Title H1或SEO Title在当前文章分类下已存在"];
        }
        if(isset($slug) && $slug){
            return ['code'=>0,'msg'=>"slug在当前平台下已存在"];
        }
        if(isset($classification_data['platformid'])&&isset($classification_data['version'])){
            $data['platformid']=$classification_data['platformid'];
            $data['version']=$classification_data['version'];
        }
        if (isset($data['id'])) {
            $where = "id='{$data['id']}'";
            $bool = $SdKArticle->update($data, $where);
        }  else {
            $bool = $SdKArticle->insertGetId($data);
//            if($bool){
//                $where['admin_id'] = $user_id;
//                $where['type'] = 'SdkDocumentation';
//                $DraftBox=new DraftBox();
//                $DraftBox->del_draft($where);
//            }
        }
        if($bool){
            return ['code'=>1];
        }else{
            return ['code'=>0,'msg'=>'没有更新数据'];
        }

    }

    public function getCategorical()
    {
        $SdkClassification = new SdkClassification();
        $where = "deleted=0";
        $field = "id,title,lv,pid,displayorder,enabled,platformid,version";
        $order = "displayorder";
        $material = $SdkClassification->select($where, $field, $order);
        $arr_project = $this->menuLeft($material);
        $banben = $this->allVersion();
        if($arr_project){
            foreach ($arr_project as $k=>$v){
                $str=$this->assemblyVersion(array($v['platformid'],$v['version']),$banben);;
                $arr[$v['id']]=$v['lefthtml'].$v['title']."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$str;
            }
        }
        return $arr ?? [];
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

    public function getplatform()
    {
        $PlatformVersion = new PlatformVersion();
        $where = "deleted=0 and lv=1";
        $data = $PlatformVersion->select($where,'id,name','displayorder');
        $arr=[];
        if($data){
            foreach ($data as $k=>$v){
                $arr[$v['id']]=$v['name'];
            }
        }
        return $arr;
    }
    public function getversion(){
        $PlatformVersion = new PlatformVersion();
        $where = "deleted=0";
        $data = $PlatformVersion->select($where,'id,name,pid,lv','displayorder');
        $arr=[];
        if($data){
            foreach ($data as $k=>$v){
                $str="";
                if($v['lv']==2 && $v['pid']){
                    foreach ($data as $ks=>$vs){
                        if($vs['id']==$v['pid'] && $vs['lv']==1){
                            $str=$vs['name']."--";
                        }
                    }
                    $arr[$v['id']]=$str.$v['name'];
                }
            }
        }
        return $arr;
    }

    public function getFindcategorical($id)
    {
        $SdKArticle = new SdKArticle();
        $where = "deleted=0 and id='$id'";
        $data = $SdKArticle->find($where);
        $data = $SdKArticle->objToArr($data);
        return $data;
    }
    function allCategories()
    {
        $SdkClassification = new SdkClassification();
        $where=array(["deleted","=",0]);
        //$where = "deleted=0";
        $field = "id,title,pid";
        $order = "lv";
        $data = $SdkClassification->select($where, $field,$order);
        $data = $SdkClassification->objToArr($data);
        return $data;
    }
    function allVersion()
    {
        $PlatformVersion = new PlatformVersion();
       // $where = "deleted=0";
        $where=array(["deleted","=",0]);
        $field = "id,name,pid";
        $order = "lv";
        $data = $PlatformVersion->select($where, $field,$order);
        $data = $PlatformVersion->objToArr($data);
        return $data;
    }

    function assemblyClassification($id,$data,&$arr = array()){
        foreach ($data as $k=>$v){
          if($v['id']==$id){
              array_push($arr, $v['title']);
              if($v['pid']!=0){
                  $this->assemblyClassification($v['pid'],$data,$arr);
              }
          }
        }
        $arrs=array_reverse($arr);
        return $arrs;
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

}
