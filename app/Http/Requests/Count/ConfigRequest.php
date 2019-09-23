<?php

namespace App\Http\Requests\Count;

use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
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
    public function rules( )
    {
        $routeAs = $this->route()->action["as"];
        if($routeAs == 'config.opeary')
        {
            return [
                'id'     => 'sometimes',
                'type' => 'required|integer',
                'value'  => 'required',
                'key'   => 'required',
            ];
        }else{
            return [];
        }
    }

    /**
     * 提示信息s
     * @return array
     */
    public function messages()
    {
        return [
            //'id.required'     => '用户名不能为空',
            'type.required'      => 'ID必传',
            'type.integer' => 'ID必需是数字',
            //'avatr.max'         => '头像不能超过128个字符',
            'value.required'  => '名称必传',
            'key.integer'   => '类型必传',
        ];
    }
}
