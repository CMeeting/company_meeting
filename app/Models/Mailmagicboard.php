<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Base;

/**
 * Class Mailmagicboard
 * @property    $id
 * @property    $name
 * @property    $title
 * @property    $admin_id
 * @property    $admin_name
 * @property    $info
 * @property    $created_at
 * @property    $updated_at
 * @package App\Models
 * @mixin       \Eloquent
 */
class Mailmagicboard extends Model {

    protected $table = 'mail_magic_board';

    public static function getByName($name){
        return self::where('name', $name)->first();
    }
}