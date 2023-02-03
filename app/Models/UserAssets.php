<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $user_id
 * @property    $total_assets
 * @property    $total_assets_balance
 * @property    $sub_assets_balance
 * @property    $package_assets_balance
 * @property    $updated_at
 * @property    $created_at
 * @mixin       \Eloquent
 * Class UserAssets
 * @package App\Models
 */
class UserAssets extends Model
{
    protected $table = 'user_assets';


}