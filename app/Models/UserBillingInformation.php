<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property        $user_id        用户id
 * @property        $first_name     用户名
 * @property        $last_name      用户姓
 * @property        $email          邮箱
 * @property        $company        公司
 * @property        $country        国家
 * @property        $phone_number   用户电话
 * @property        $province       省份/区
 * @property        $city           城市
 * @property        $address        详细地址
 * @property        $zip            邮箱
 * @property        $created_at
 * @property        $updated_at
 * @mixin           \Eloquent
 * Class UserBillingInformation
 * @package App\Models
 */

class UserBillingInformation extends Model
{
    protected $table = 'user_billing_information';
}