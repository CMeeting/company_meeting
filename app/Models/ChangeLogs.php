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
    public $platform = [ 1 => 'ios', 2 => 'Android' , 3 => 'Windows' , 4 => 'Mac', 5 => 'Web'];
    public $product = [1 => 'ComPDFKit PDF SDK' , 2 => 'ComPDFKit Conversion SDK'];
    public $development_language = [ 1 => 'Objective-C' , 2 => 'Swift' , 3 => 'Java' , 4 => 'Kotlin' , 5 => 'C# WPF' , 6 => 'C# UWP' , 7 => 'JavaScript'];
    protected $table = 'change_logs';
}