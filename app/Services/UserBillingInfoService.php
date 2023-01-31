<?php


namespace App\Services;


use App\Models\UserBillingInformation;

class UserBillingInfoService
{
    /**
     * 获取用户账单
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByUserId($user_id){
        return UserBillingInformation::where('user_id', $user_id)->first();
    }

    /**
     * 保存用户账单信息
     * @param $user_id
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $phone_number
     * @param $company
     * @param $country
     * @param $province
     * @param $city
     * @param $address
     * @param $zip
     */
    public function store($user_id, $first_name, $last_name, $email, $phone_number, $company, $country, $province = '', $city = '', $address ='', $zip = ''){
        $model = UserBillingInformation::where('user_id', $user_id)->first();
        if(!$model instanceof UserBillingInformation){
            $model = new UserBillingInformation();
        }

        $model->user_id = $user_id;
        $model->first_name = $first_name;
        $model->last_name = $last_name;
        $model->email = $email;
        $model->phone_number = $phone_number;
        $model->company = $company;
        $model->country = $country;

        if($province){
            $model->province = $province;
        }

        if($city){
            $model->city = $city;
        }

        if($address){
            $model->address = $address;
        }

        if($zip){
            $model->zip = $zip;
        }

        $model->save();
    }

    /**
     * 后天添加编辑用户 增加公司，国家信息
     * @param $user_id
     * @param $company
     * @param $country
     */
    public function addFromRegister($user_id, $company, $country){
        $model = UserBillingInformation::where('user_id', $user_id)->first();
        if(!$model instanceof UserBillingInformation){
            $model = new UserBillingInformation();
        }

        $model->user_id = $user_id;
        $model->company = $company;
        $model->country = $country;

        $model->save();
    }
}