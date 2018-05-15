<?php

/**
 * 
 */
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\roleIdHasSubordinates;
use App\Rules\LineManagerUserId;

class UserCreateUpdate extends FormRequest
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
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'bail|required|email|unique:users,id,' . $this->input('id'),
            'role_id' => ['bail', 'required', new RoleIdHasSubordinates($this->input('id'))],
            'line_manager_user_id' => ['bail', 'required', new LineManagerUserId($this->input('id'), $this->input('role_id'))],
            'active' => 'required_if:has-subordinates,1|required_if:num-super-admins,<,2'
        ];
    }
}
