<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterMensaRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required' , 'min:3' , 'max:50' , 'string'],
            'address' => ['required' , 'string' , 'max:255'],
            'email' => ['required' , 'email' , 'unique:users,email'],
            'username' => ['required' , 'string' ,'unique:users,username' ],
            'password' => ['required' , 'string' , 'confirmed','min:8']
        ];
    }
}
