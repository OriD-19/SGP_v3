<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SprintUpdateRequest extends FormRequest
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
            'description' => 'sometimes|required|string|max:255',
            'duration' => 'sometimes|required|integer|min:1',
            'start_date' => 'sometimes|required|date',
            'active' => 'sometimes|required|boolean',
        ];
    }
}
