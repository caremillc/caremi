<?php declare (strict_types = 1);
namespace App\Http\Requests\User;

use Careminate\Http\Requests\FormRequest;

class StoreUserRequest extends FormRequest
{
     public function authorize(): bool
    {
        // Authorization logic (e.g., return auth()->check();)
        return true;
    }
    
    public function rules(): array
    {
        return [
             'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'The email has already been taken.',
        ];
    }
}
