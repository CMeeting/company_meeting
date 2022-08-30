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

class Support extends Model
{
    public $platform = [ 1 => ['title' => 'ios','code' => 'A'] , 2 => ['title' => 'Android','code' => 'B'] , 3 => ['title' => 'Windows','code' => 'C'] , 4 => ['title' => 'Mac','code' => 'D'] , 5 => ['title' => 'Web','code' => 'E']];
    public $product = [1 => ['title' => 'ComPDFKit PDF SDK','code' => 'A'] , 2 => ['title' => 'ComPDFKit Conversion SDK','code' => 'B']];
    public $platformarr = [ 'iOS' => ['title' => 'ios','code' => 'A'] , 'Android' => ['title' => 'Android','code' => 'B'] , 'Windows' => ['title' => 'Windows','code' => 'C'] , 'Mac' => ['title' => 'Mac','code' => 'D'] , 'Web' => ['title' => 'Web','code' => 'E']];
    public $productarr = ['ComPDFKit PDF SDK' => ['title' => 'ComPDFKit PDF SDK','code' => 'A'] , 'ComPDFKit Conversion SDK' => ['title' => 'ComPDFKit Conversion SDK','code' => 'B']];
    public $development_language = [ 1 => ['title' => 'Objective-C','code' => 'A'] , 2 => ['title' => 'Swift','code' => 'B'] , 3 => ['title' => 'Java','code' => 'C'] , 4 => ['title' => 'Kotlin','code' => 'D'] , 5 => ['title' => 'C# WPF','code' => 'E'] , 6 => ['title' => 'C# UWP','code' => 'F'] , 7 => ['title' => 'JavaScript','code' => 'G']];
//    public $development_language = ['Objective-C' => 1 , 'Swift' => 2,'Java'=>3 ,'Kotlin'=>4,'C# WPF'=>5,'C# UWP'=>6,'JavaScript'=>7];
    public $type = [1 => 'Bug' , 2 => '优化' , 3 => '新增'];
    public $status = [1 => '待确认' , 2 => '已接收' , 3 => '已解决' , 4 => '已发布'];
    protected $table = 'support';
}