<?php

namespace App\Services;

use App\Models\Mailmagicboard as mail;

class MailmagicboardService
{

    public function __construct()
    {

    }

    public function data_list(){

            $where='deleted = 0';

            $email=new mail();
            $data=$email->whereRaw($where)->orderByRaw('displayorder,id desc')->paginate(10);
            $classification=$this->allCategories();
            $banben=$this->allVersion();

            if(!empty($data)){
                foreach ($data as $k=>$v){
                    $fenlei=$this->assemblyClassification($v->classification_ids,$classification);
                    $v->classification=$fenlei?implode("--",$fenlei):"";
                    $v->platformversion=$this->assemblyVersion(array($v->platformid,$v->version),$banben);
                }
            }
            return $data;

    }



}