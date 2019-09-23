<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\base;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of UserDayLog
 *
 * @author 七彩P1
 */
class UserDayLog extends Model{
    //put your code here
    public $table="user_day_log";
    protected $fillable  = [//创建时候可以修改添加的字段
        "login_ip",
        //"uchip",
        "login_time",
        "is_new_user" ,
        "uid",
        "game" ,
        "date",
    ];    
    public function getUserDayLog($uid,$date=""){
        if(!$date){
            $date = date("Ymd");
        }
        $where['uid'] = $uid;
        $where['date'] = $date;
        $userDayLog = $this->where($where)->first();//每日记录表
        return $userDayLog;
    }
}
