<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\base;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of LoginLog
 *
 * @author 七彩P1
 */
class LoginLog  extends Model {
    //put your code here
    public $connection ="mysql_two";
    public $table ="login_log";
}
