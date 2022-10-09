<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Base;

/**
 *
 * @property        $id         id
 * @property        $email      邮箱
 * @property        $status     状态
 * @property        $admin_id   创建人id
 * @property        $admin_name 创建人名称
 * @property        $created_at 创建时间
 * @property        $updated_at 更新时间
 * @property        $deleted 时候删除
 * @mixin           \Eloquent
 * Class Subscription
 * @package App\Models
 */

class Subscription extends Base {

    public static $table = 'subscription';
}
