<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'start_date'         => 'required|date',
            'end_date'           => 'required|date',

            // Validasi untuk array 'items'
            'items'              => 'required|array|min:1',

            // Validasi untuk setiap elemen di dalam array 'items'
            // Tanda * berarti "setiap item di dalam array items"
            'items.*.camera_id'  => 'required|integer|exists:cameras,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ];
    }
}
