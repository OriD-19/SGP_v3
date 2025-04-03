<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SprintCreateRequest extends FormRequest
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
            'description' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'user_stories' => 'array',
            'user_stories.*' => 'exists:user_stories,id',
        ];
    }
}
