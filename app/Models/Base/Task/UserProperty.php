<?php

namespace App\Models\base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
//use App\Models\MoneyModel;
class UserProperty extends Model {

    public $table = 'user_property';
    //指定别的主键
    //protected $primaryKey = 'article_id';
    public $roule = ['uid' => "required"];
    protected $fillable  = [//创建用户时候可以修改添加的字段
        "uid",
        //"uchip",
        "uldays" ,
        "ulogins" ,
        "utime",
    ];
    public $timestamps = false;
    
    public function vlidate($vadate) {
        return Validator::make($vadate, $this->roule);
    }

    /*
     * 初始化一个注册用户
     */

    public function initUserProperty($vadate) {
        $relsut = array("status" => "false", "msg" => "", "uid" => "");
        $validator = $this->vlidate($vadate);
        if ($validator->passes()) {//验证通过
            $initData = $this->setInitData() + $vadate;
            $this->fill($initData)->save();
            
            $relsut['status'] = true;
            $relsut['uid'] = $vadate['uid'];
        } else {
            $relsut['msg'] = $validator->messages();
        }
        return $relsut;
    }
    /*
     * 设置初始化用户的必须的值
     */
    public function setInitData(){
        $initData = array();
        //$initData = array_add($initData,"uchip", env("INITMONENY"));
        $initData = array_add($initData,"utime", time());
        //$initData = array_add($initData,"uchip",env("MUCOUNT"));
        return $initData;  
    }
    

}
