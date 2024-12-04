<?php

namespace App\Http\Requests;
use App\Services\Base64Services;
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
            'base64ImagePhoto' => 'nullable|string',
            'custom' => 'nullable|string',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $base64ImagePhoto = $this->base64ImagePhoto ?? null;
            if ($base64ImagePhoto) {
                $base64Service = new Base64Services();
                $mimeType = $base64Service->mimeType($base64ImagePhoto);
                if (!in_array($mimeType, ['image/png', 'image/jpg', 'image/jpeg'])) {
                    $validator->errors()->add('base64ImagePhoto', 'File type not supported (only supports "png" and "jpeg" images).');
                }
            }
        });
    }
}
