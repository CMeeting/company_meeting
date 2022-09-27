<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class LogoutUser
 * @package App\Models
 *
 * @property    $id
 * @property    $user_id        用户id
 * @property    $email          邮箱
 * @property    $full_name      全名
 * @property    $type           用户类型
 * @property    $register_time  注册时间
 * @property    $created_at
 * @property    $updated_at
 * @mixin       \Eloquent
 */

class LogoutUser extends Model
{
    public static function addFromUser(User $user){
        $logout_user = new self();
        $logout_user->user_id = $user->id;
        $logout_user->email = $user->email;
        $logout_user->full_name = $user->full_name;
        $logout_user->type = $user->type;
        $logout_user->register_time = $user->created_at;
        $logout_user->save();
    }
}