<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id                 id
 * @property    $email              邮箱
 * @property    $admin_id           操作管理员
 * @property    $updated_at         更新时间
 * @property    $created_at         创建时间
 * Class EmailBlacklist
 * @package App\Models
 * @mixin \Eloquent
 */

class EmailBlacklist extends Model
{
    public static $table = 'email_blacklist';
}