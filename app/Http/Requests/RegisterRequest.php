<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to register
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255|unique:users,nickname',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:3|confirmed', // Password validation with confirmation
            'photoUrl' => 'nullable|url', // Optional photo URL field
        ];
    }

}
