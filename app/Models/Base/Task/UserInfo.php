<?php

namespace App\Models\base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
class UserInfo extends Model
{
    public $table = 'userinfo';
	//指定别的主键
	//protected $primaryKey = 'article_id';
    public $roule =  [
                    "puid"=>"required",
                    "pfid"=>"required",
                    "uname"=>"required",
    //                "uface"=>"required",
                    "usex" =>"required"
                 ];
    public $timestamps=false;
    public function vlidate($vadate){
        return Validator::make($vadate,$this->roule);
    }
    
    
    public function addUser($registerData){
        $reslut = array("status"=>FALSE,"msg"=>"","uid"=>"");
        $validator  = $this->vlidate($registerData);
        if($validator->passes()){//验证通过
            $reslut['uid']  = $this->insertGetId($registerData);
            $reslut = (new UserProperty())->initUserProperty(array("uid"=>$reslut['uid']));
            return $reslut;
        }else{
            $reslut['msg'] = $validator->messages();
            return $reslut;
        }
    }

}
