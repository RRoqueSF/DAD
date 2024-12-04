<?php
namespace App\Http\Requests;
use App\Services\Base64Services;
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
            'base64ImagePhoto' => 'nullable|string',
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
