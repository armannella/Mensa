<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
{
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
            'matricola' => ['required', 'integer' , 'min_digits:6' , 'max_digits:8' , 'unique:students,matricola'],
            'email' => ['required' , 'email' , 'unique:users,email' ,'ends_with:@studenti.unime.it'],
            'username' => ['required' , 'string' , 'size:16' , 'unique:users,username'],
            'password' => ['required' , 'string' , 'confirmed','min:8']
        ];
    }
}
