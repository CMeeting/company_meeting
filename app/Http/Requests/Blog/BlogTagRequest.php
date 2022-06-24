<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class BlogTagRequest extends FormRequest
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
                'title'     => 'required',
                'sort_id'  => 'required:integer',
            ];
        }else{
            return [
                'title'     => 'required',
                'sort_id'  => 'required:integer',
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
            'sort_id.required'  => '用户所属角色不能为空',
            'sort_id.integer'   => '表单不合法',
            'title.required'   => '状态不能为空',
        ];
    }
}
