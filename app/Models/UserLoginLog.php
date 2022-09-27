<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property        $id
 * @property        $user_id        用户id
 * @property        $type           登录设备 1：web 2：ios 3：android
 * @property        $uuid           设备唯一标识
 * @property        $created_at
 * @property        $updated_at
 * @mixin           \Eloquent
 * Class UserLoginLog
 * @package App\Models
 */

class UserLoginLog extends Model
{

}