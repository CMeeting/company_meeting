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

class Blog extends Model
{
    protected $fillable = ['title', 'slug', 'cover', 'is_delete'];

}