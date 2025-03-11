<?php

namespace Src\Company\Security\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'role' => ['required','not_in:Select'],
            'name' => ['required','string','max:255'],
            'contact_number' => ['required'],
            'email' => ['required','email',  'unique:users,email,' . request()->route('user')->id],
        ];
    }
}
