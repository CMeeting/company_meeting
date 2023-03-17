<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $user_id
 * @property    $license_code       序列码
 * @property    $type               1：试用   2：购买
 * @property    $expiration         过期时间
 * @property    $updated_at
 * @property    $created_at
 * @mixin       \Eloquent
 * Class WebViewerLicense
 * @package App\Models
 */

class WebViewerLicense extends Model
{
    protected $table = 'webviewer_license';

    private $key = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+/';

    const TYPE_1_TRIAL = 1;
    const TYPE_2_BUY = 2;

    /**
     * 限制域名
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function licenseDomain(){
        return $this->hasMany(WebViewerLicenseDomain::class, 'license_id', 'id');
    }

    /**
     * 新增license
     * @param $user_id
     * @param $type
     * @param $license_code
     * @param $expiration
     * @return mixed
     * @throws \Exception
     */
    public function add($user_id, $type, $license_code, $expiration){
        $model = new WebViewerLicense();
        $model->license_code = $license_code;
        $model->user_id = $user_id;
        $model->type = $type;
        $model->expiration = $expiration;
        $model->save();

        return $model->id;
    }

    /**
     * 生成
     * @return string
     * @throws \Exception
     */
    public function generateCode(){
        return base64_encode(random_bytes(32));
    }
}