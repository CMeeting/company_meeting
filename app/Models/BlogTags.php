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

class BlogTags extends Base
{
    public static $table = 'blog_tags';

    public static function getTagskv(){
        return Db::table(self::$table)
            ->select(DB::raw('id,title'))
            ->whereRaw('is_delete = 0')
            ->orderByRaw('sort_id,id DESC')
            ->get();
    }
}