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
use Illuminate\Support\Facades\DB;

class BlogTypes extends Model
{
//    public static $table = 'blog_types';
    protected $table = 'blog_types';
    public function getTypeskv(){
        return Db::table($this->table)
            ->select(DB::raw('id,title'))
            ->whereRaw('is_delete = 0')
            ->orderByRaw('sort_id,id DESC')
            ->get();
    }

    public function getTypeAndSlugkv(){
        return Db::table($this->table)
            ->select(DB::raw('id,title,slug'))
            ->whereRaw('is_delete = 0')
            ->orderByRaw('sort_id,id DESC')
            ->get();
    }
}