<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(request()->method() == 'POST')
        {
            return [
                'name'     => 'required',
                'password' => 'required',
                'avatr'    => 'max:2048',
                'role_id'  => 'required:integer',
                'status'   => 'required:integer',
            ];
        }else{
            return [
                'name'     => 'required',
                'avatr'    => 'max:2048',
                'role_id'  => 'required:integer',
                'status'   => 'required:integer',
            ];
        }
    }

    /**
     * 提示信息s
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'     => '用户名不能为空',
            'password.required' => '密码不能为空',
            'avatr.max'         => '头像不能超过2048KB',
            'role_id.required'  => '用户所属角色不能为空',
            'role_id.integer'   => '表单不合法',
            'status.required'   => '状态不能为空',
            'status.integer'    => '表单不合法',
        ];
    }
}
