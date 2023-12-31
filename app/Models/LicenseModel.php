<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/6/21
 * @Time: 14:42
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin       \Eloquent
 * Class LicenseModel
 * @package App\Models
 */
class LicenseModel extends Model
{
    protected $table = 'license_code';

    const LICENSE_TYPE_1_ON_TRIAL = 1;
    const LICENSE_TYPE_2_PAID = 2;

    const LICENSE_STATUS_1_NORMAL = 1;
    const LICENSE_STATUS_2_STOP = 2;
    const LICENSE_STATUS_3_EXPIRE = 3;
    const LICENSE_STATUS_4_EXPIRE_SOON = 4;
}