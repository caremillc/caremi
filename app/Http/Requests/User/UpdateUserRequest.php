<?php

namespace App\Http\Requests\User;

use Careminate\Http\Requests\Request;

class UpdateUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Add validation rules here
        ];
    }

    /**
     * Configure the request authorization.
     *
     * @return bool
     */
    public function authorize()
    {
        // Return true or false based on authorization logic
        return true;
    }
}
