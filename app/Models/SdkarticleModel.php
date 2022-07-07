<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Base;

class SdkarticleModel extends Model {

//    public static $table = 'sdk_article';
    protected $table = 'sdk_article';
    protected $fillable = [
        'title',
        'seotitel',
        'deleted',
        'slug',
        'platformid',
        'version',
        'enabled',
        'classification_ids',
        'info',
        'displayorder',
    ];
}