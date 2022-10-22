<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @property    $id
 * @property    $email          邮箱
 * @property    $full_name      全名
 * @property    $type           用户类型
 * @property    $password       密码
 * @property    $created_at
 * @property    $updated_at
 * @mixin       \Eloquent
 */


class User extends Model
{
    const TYPE_1_FREE = 1;
    const TYPE_2_TRY_OUT = 2;
    const TYPE_3_SAAS = 3;
    const TYPE_4_SDK = 4;
    const TYPE_5_SAAS_ADN_SDK = 5;

    const CODE_1_YES = 1;
    const CODE_0_NO = 0;

    public static $typeArr = [
        1 => '免费用户',
        2 => '试用用户',
        3 => 'SaaS用户',
        4 => 'SDK用户',
        5 => 'SaaS、SDK用户'
    ];

    protected $table = 'users';

    /**
     * 账单信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userBillingInformation(){
        return $this->hasOne(UserBillingInformation::class, 'user_id', 'id');
    }

    /**
     * 邮箱是否存在
     * @param $email
     * @param $id
     * @return mixed
     */
    public static function existsEmail($email, $id = null){
        $query = User::where('email', $email);
        if($id){
            $query->where('id', '!=', $id);
        }

        return $query->exists();
    }

    /**
     * @param  $len
     * 获取随机密码
     * @return false|string
     */
    public static function getRandStr($len = null){
        //生成随机密码
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if(!$len){
            $len = rand(6, 24);
        }
        $randStr = str_shuffle($str);//打乱字符串
        return substr($randStr,0, $len);
    }

    /**
     * 修改器 - 加密密码
     * @param $value
     */
    public function setPasswordAttribute($value){
        $this->attributes['password'] = md5('compdf'. $value);
    }

    /**
     * 加密密码
     * @param $password
     * @return string
     */
    public static function encryptPassword($password){
        return md5('compdf' . $password);
    }
}