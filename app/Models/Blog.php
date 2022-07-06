<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/6/21
 * @Time: 14:42
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    public $table = 'blogs';
    protected $fillable = [
        'title',
        'title_h1',
        'is_delete',
        'slug',
        'type_id',
        'tag_id',
        'cover',
        'keywords',
        'keywords',
        'description',
        'content',
        'sort_id',
        'abstract',
    ];
}