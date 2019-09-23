<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;
use App\Models\Base\TaskAdmin\AdminConfig;
/**
 * Description of ConfigModel
 *
 * @author 七彩P1
 */
class ConfigModel extends AdminConfig{
    //put your code here
    
    
    public function __get($key) {
        $ret = $this->where("key",$key)->pluck("value","type");
        if($ret){
            return $ret->toArray();
        }
        return [];
    }
}
