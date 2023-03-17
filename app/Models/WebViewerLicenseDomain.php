<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $license_id
 * @property    $domain             域名
 * @property    $server_region      服务器地区
 * @property    $updated_at
 * @property    $created_at
 * @mixin       \Eloquent
 * Class WebViewerLicenseDomain
 * @package App\Models
 */

class WebViewerLicenseDomain extends Model
{

    protected $table = 'webviewer_license_domain';

    public function add($license_id, $domain, $server_region){
        $model = new WebViewerLicenseDomain();
        $model->license_id = $license_id;
        $model->domain = $domain;
        $model->server_region = $server_region;
        $model->save();
    }
}