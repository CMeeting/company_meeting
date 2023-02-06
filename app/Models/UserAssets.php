<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $user_id                    用户id
 * @property    $type                       类型 1:订阅 2：package
 * @property    $total                      总量
 * @property    $balance                    余量
 * @property    $expire_date                过期时间
 * @property    $updated_at
 * @property    $created_at
 * @mixin       \Eloquent
 * Class UserAssets
 * @package App\Models
 */
class UserAssets extends Model
{
    protected $table = 'user_assets';

    const TYPE_1_SUB = 1;
    const TYPE_2_PACKAGE = 2;
}