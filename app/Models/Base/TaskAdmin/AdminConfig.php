<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\Base\TaskAdmin;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of Config
 *
 * @author 七彩P1
 */
class AdminConfig extends Model{
    //put your code here
    public $table ="config";
    
    
    public $showKeyArr =[
        "pfid"=>"用户渠道",
        "usid"=>"厂商渠道",
    ];
}
