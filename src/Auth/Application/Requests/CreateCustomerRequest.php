<?php

namespace Src\Auth\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['nullable'],
            'email' => ['nullable', 'unique:users,email'],
            'contact_no' => ['required', 'unique:users,contact_no'],
            'nric' => ['nullable'],
            'source' => ['nullable'],
            'customer_attachment' => ['nullable', 'file', 'mimes:jpg,png,pdf', 'max:10240'],
        ];
    }
}
