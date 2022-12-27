<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 16:26
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * @package App\Models
 * @mixin       \Eloquent
 */

class Order extends Model
{

    protected $table = 'orders';

    const STATUS_0_UNPAID = 0;
    const DETAILS_STATUS_1_TRIAL = 1;
}