<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**

 * @bodyParam email string required Alamat email yang unik. Contoh: john@example.com
 * @bodyParam password string required Kata sandi minimal 6 karakter. Contoh: rahasia123
 * @bodyParam role_id integer required ID dari role yang tersedia. Contoh: 2
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'     => 'required|string|max:255|regex:/^[a-z]+$/i|unique:users,name',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
    }
}
