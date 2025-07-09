<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCameraRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'rental_price_per_day' => 'required|numeric|min:0',
            'foto_camera' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Maksimal 2MB
            'status' => 'required|in:available,rented,maintenance',
        ];
    }
}
