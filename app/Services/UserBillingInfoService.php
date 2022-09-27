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
    public function store($user_id, $first_name, $last_name, $email, $phone_number, $company, $country, $province, $city, $address, $zip){
        $model = UserBillingInformation::where('user_id', $user_id)->first();
        if(!$model instanceof UserBillingInformation){
            $model = new UserBillingInformation();
        }

        $model->first_name = $first_name;
        $model->last_name = $last_name;
        $model->email = $email;
        $model->phone_number = $phone_number;
        $model->company = $company;
        $model->country = $country;
        $model->province = $province;
        $model->city = $city;
        $model->address = $address;
        $model->zip = $zip;
        $model->save();
    }
}