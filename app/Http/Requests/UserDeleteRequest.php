<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserDeleteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Make sure the user is authenticated
    }

    public function rules()
    {
        return [
            'confirmation' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'confirmation.required' => 'The confirmation field is required.',
            'confirmation.string' => 'The confirmation must be a string.',
        ];
    }

    public function validateConfirmation()
    {
        $user = $this->user();

        if ($this->confirmation !== $user->nickname && !Hash::check($this->confirmation, $user->password)) {
            return false;
        }

        return true;
    }
}
