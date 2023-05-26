<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**\
 * Class BackGroundUser
 * @package App\Models
 * @property    $id
 * @property    $tenant_id
 * @property    $full_name
 * @property    $username
 * @property    $password
 * @property    $status
 * @property    $first_login_time
 * @property    $last_login_time
 * @property    $create_date
 * @property    $create_by
 * @property    $update_date
 * @property    $update_by
 * @property    $compdfkit_id
 */

class BackGroundUser extends Model
{
    protected $table = 'background_user';

    const CREATED_AT =  'create_date';
    const UPDATED_AT = 'update_date';

    const STATUS_0_DELETED = 0;
    const STATUS_1_ACTIVE = 1;

    public static function getByCompdfkitId($compdfkit_id){
        return BackGroundUser::query()
            ->where('status', self::STATUS_1_ACTIVE)
            ->where('compdfkit_id', $compdfkit_id)
            ->first();
    }
}