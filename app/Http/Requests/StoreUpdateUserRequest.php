<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreUpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        // Permite o uso da requisição; ajuste conforme necessário
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'email|unique:users,email,' . $this->user->id,
            'nickname' => 'string|max:255|unique:users,nickname,' . $this->user->id,
            'name' => 'string|max:255',
            'photo' => 'nullable|file|image',  // Ensure the photo is validated as an image
            'custom' => 'nullable|string',
        ];
    }
    
}
