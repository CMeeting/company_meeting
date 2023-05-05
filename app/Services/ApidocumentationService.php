<?php

namespace App\Services;

use App\Models\DocumentationModel as PlatformVersion;
use App\Models\SdkclassificationModel as SdkClassification;
use App\Models\SdkarticleModel as SdKArticle;
use App\Models\User;

class Apidocumentationservice
{

    /**
     * @param $param
     * @return array
     * 组装平台版本和分类文章数据
     */
    public function getdata($param)
    {
        $source = $param['source'];
        $data=[];
        $PlatformVersion = new PlatformVersion();
        $versiondata=$this->getVersion($source);
        //组装平台版本数据
        $Platform=$PlatformVersion->finds("name like '".$param['platformname']."' and deleted=0 and enabled=1 and lv=1","displayorder");
        if(!$Platform){
            return ['data'=>'','code'=>403,'msg'=>"暂无数据"];
        }
        if(isset($param['versionname']) && $param['versionname']){
            $newversion=$PlatformVersion->finds("pid=".$Platform['id']." and name like '".$param['versionname']."' and deleted=0 and enabled=1","displayorder");
        }else{
            $newversion=$PlatformVersion->finds("pid=".$Platform['id']." and deleted=0 and enabled=1","displayorder");
        }
        $version=$newversion?$newversion['id']:0;
        $platform=$this->theassembly($versiondata,[$Platform['id'],$version]);
        $data['list']=isset($platform['list'])?$platform['list']:'暂无数据';
        $data['platformname']=isset($platform['platformname'])?$platform['platformname']:"";
        $data['versionname']=isset($platform['versionname'])?$platform['versionname']:'';
        //组装平台版本数据结束
        $Classification=$this->getClassification([$Platform['id'],$version]);
        $articledata=$this->sdKArticle([$Platform['id'],$version]);
        $data['classification']=$this->theassemblydata($Classification,$articledata);
        return $data;
    }

    /**
     * @param $param
     * @return array|\think\response\Json|void
     * 获取文章详情
     */
    public function getInfo($param){
        $data=[];
        $source = $param['source'];
        $SdKArticle_data = new SdKArticle();
        $PlatformVersion = new PlatformVersion();
        $SdkClassification = new SdkClassification();
        $Platform=$PlatformVersion->finds("name like '".$param['platformname']."' and deleted=0 and enabled=1 and lv=1","displayorder");
        if(!$Platform){
            return ['data'=>'','code'=>403,'msg'=>"缺少参数"];
        }
        $version=$PlatformVersion->finds("name like '".$param['category']."' and pid=".$Platform['id']." and deleted=0 and enabled=1","displayorder");
        if(!$version){
            return ['data'=>'','code'=>403,'msg'=>"没有找到该产品"];
        }
        $SdKArticle=$SdKArticle_data->_find("deleted=0 and enabled=1 and platformid=".$Platform['id']." and version=".$version['id']." and slug='".$param['slugs']."'","displayorder");
        if($SdKArticle){
            $SdKArticle=$SdKArticle_data->objToArr($SdKArticle);
            $fenlei=$SdkClassification->finds("deleted=0 and enabled=1 and id=".$SdKArticle['classification_ids']);
            if(!$fenlei)return ['data'=>'','code'=>403,'msg'=>"分类属于隐藏状态"];
            $Classification=$this->getClassification([$SdKArticle['platformid'],$SdKArticle['version']]);
            $articledata=$this->sdKArticle([$SdKArticle['platformid'],$SdKArticle['version']]);
            $classificationids=$this->sele_classificationid($SdKArticle['classification_ids'],$Classification);

            $PlatformVersion = new PlatformVersion();
            $versiondata=$this->getVersion($source);
            //组装平台版本数据
            $Platform=$PlatformVersion->finds("id =".$SdKArticle['platformid']." and deleted=0 and enabled=1","displayorder");
            $newversion=$PlatformVersion->finds("pid=".$SdKArticle['platformid']." and id =".$SdKArticle['version']." and deleted=0 and enabled=1","displayorder");
            $version=$newversion['id'];
            $platform=$this->theassembly($versiondata,[$Platform['id'],$version]);
            $data['list']=isset($platform['list'])?$platform['list']:'暂无数据';
            $data['platformname']=isset($platform['platformname'])?$platform['platformname']:"";
            $data['versionname']=isset($platform['versionname'])?$platform['versionname']:'';

            $endid=count($classificationids)-1;
            $data['current']=$this->getfinddata($classificationids[0],$classificationids[$endid],$classificationids,$Classification);
            $data['classification']=$this->theassemblydata($Classification,$articledata);
            $data['id']=$SdKArticle['id'];
            $data['name']=$SdKArticle['titel'];
            $data['seo']=$SdKArticle['seotitel'];
            $data['seo_description']=$SdKArticle['seo_description'];
            $data['sgluid']=$SdKArticle['slug'];
            $data['type']=$SdKArticle['type'];
            $data['data']=$SdKArticle['info'];
            $data['created']=$SdKArticle['created_at'];
            $data['updated']=$SdKArticle['updated_at'];
            return $data;
        }else{
            return ['data'=>'','code'=>403,'msg'=>"缺少参数"];
        }
    }


    function getVersion($source){
        $PlatformVersion = new PlatformVersion();
        if($source == User::SOURCE_2_SAAS){
            return $PlatformVersion->selects("deleted=0 and enabled=1 and (name='docs' or name = 'API Reference')","id,name,lv,pid,seotitel,h1title","lv,displayorder");
        }else{
            return $PlatformVersion->selects("deleted=0 and enabled=1 and name!='docs'","id,name,lv,pid,seotitel,h1title","lv,displayorder");
        }

    }
    function getClassification($ids){
        $SdkClassification = new SdkClassification();
        return $SdkClassification->selects("deleted=0 and enabled=1 and platformid=".$ids[0]." and version=".$ids[1],"id,title,lv,pid","lv,displayorder,id desc");
    }
    function sdKArticle($ids){
        $SdKArticle = new SdKArticle();
        return $SdKArticle->selects("deleted=0 and enabled=1 and platformid=".$ids[0]." and version=".$ids[1],"id,titel,seotitel,slug,info,classification_ids,seo_description","displayorder");
    }

    function theassembly($data,$ids){
        $i=0;
        $s=0;
        $arr=[];

        foreach ($data as $k=>$v){
            if($v['id']==$ids[0]){
                $arr['platformname']['name']=$v['name'];
                $arr['platformname']['seo_titels']=$v['seotitel'];
                $arr['platformname']['h1_titles']=$v['h1title'];
            }
            if($v['pid']==$ids[0]){
                $arr['platformname']['list'][$s]=$v['name'];
                $s++;
            }
            if($v['id']==$ids[1]){
                $arr['versionname']=$v['name'];
            }
            if($v['lv']==1){
                $j=0;
             $arr['list'][$i]['name']=$v['name'];
             $arr['list'][$i]['seo_titels']=$v['seotitel'];
             $arr['list'][$i]['h1_titles']=$v['h1title'];
             foreach ($data as $ks=>$vs){
                 if($vs['pid']==$v['id']){
                     $arr['list'][$i]['list'][$j]['name']=$vs['name'];
                     $j++;
                 }
             }
             $i++;
           }
        }
     return $arr;
     }


     function theassemblydata($classificationdata,$articledata,$ids=array()){
       $i=0;
       $s=0;
       $arr=[];
        foreach ($classificationdata as $k=>$v){
          if($v['lv']==1){
            $arr[$i]['id']=$v['id'];
            $arr[$i]['pid']=$v['pid'];
            $arr[$i]['name']=$v['title'];
            $j=0;
            $arr[$i]['data']=[];
            foreach ($articledata as $ks=>$vs){
              if($vs['classification_ids']==$v['id']){
                  $arr[$i]['data'][$j]['id']=$vs['id'];
                  $arr[$i]['data'][$j]['name']=$vs['titel'];
                  $arr[$i]['data'][$j]['seo']=$vs['seotitel'];
                  $arr[$i]['data'][$j]['sgluid']=$vs['slug'];
                  $arr[$i]['data'][$j]['seo_description']=$vs['seo_description'];
                  $j++;
              }
            }
            $arr[$i]['list']=$this->lowerData($v['id'],$classificationdata,$articledata);
            $i++;
          }
        }
        return $arr;
     }

     function lowerData($id,$classificationdata,$articledata,&$arr=array()){
        $i=0;
        foreach ($classificationdata as $k=>$v){
            if($v['pid']==$id){
                $arr[$i]['id']=$v['id'];
                $arr[$i]['pid']=$v['pid'];
                $arr[$i]['name']=$v['title'];
                $j=0;
                foreach ($articledata as $ks=>$vs){
                    if($vs['classification_ids']==$v['id']){
                        $arr[$i]['data'][$j]['name']=$vs['titel'];
                        $arr[$i]['data'][$j]['id']=$vs['id'];
                        $arr[$i]['data'][$j]['sgluid']=$vs['slug'];
                        $arr[$i]['data'][$j]['seo']=$vs['seotitel'];
                        $j++;
                    }
                }
                $arr[$i]['list']=$this->lowerData($v['id'],$classificationdata,$articledata);
                    $i++;
            }
        }
        return $arr;
     }


     public function sele_classificationid($id,$data,&$arr=array()){
        array_push($arr,intval($id));
        foreach ($data as $k=>$v){
            if($v['id']==$id && $v['pid']){
                $this->sele_classificationid($v['pid'],$data,$arr);
            }
        }
         $arrs=array_reverse($arr);
         return $arrs;
     }

     function getfinddata($id,$enid,$ids,$data){
         foreach ($data as $ks=>$vs){
             if($id==$vs['id']){
                 $arr['id']=$vs['id'];
                 $arr['name']=$vs['title'];
                 $arr['list']=$this->currentData($vs['id'],$enid,$ids,$data);
             }
         }
         return $arr;
     }
     public function currentData($id,$enid,$ids,$data,&$arr=array()){
             $i=0;
             foreach ($data as $ks=>$vs){
                    if($id==$vs['pid'] && in_array($vs['id'],$ids)){
                        $arr['id']=$vs['id'];
                        $arr['name']=$vs['title'];
                        if($vs['id']==$enid){
                            $arr['list']="";
                        }else{
                            $arr['list']=$this->currentData($vs['id'],$enid,$ids,$data);
                        }
                        $i++;
                    }
                }

         return $arr;
     }



}