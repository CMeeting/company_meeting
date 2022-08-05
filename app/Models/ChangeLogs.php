<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/3
 * @Time: 15:42
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLogs extends Model
{
    public $platform = ['mac' => 0 , 'windows' => 1 , 'ios' => 2 , 'android' => 3 , 'windows_uwp' => 4];
    protected $table = 'change_logs';
}