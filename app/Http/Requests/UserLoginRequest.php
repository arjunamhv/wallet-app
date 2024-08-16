<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|min:11|max:15',
            'pin' => 'required|string|min:6|max:6',
        ];
    }
    protected function failedValidation (Validator $validator) {
        throw new HttpResponseException(response(['errors' => $validator->getMessageBag()], 422));
    }

    public function attributes(): array
    {
        return [
            'phone_number' => 'Phone Number',
            'pin' => 'PIN',
        ];
    }
}
