<?php

namespace App\Http\Requests\Api\English;

use Illuminate\Foundation\Http\FormRequest;

class GenerateListeningRequest extends FormRequest
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
            'youtube_url' => 'required|url',
        ];
    }

    public function messages(): array
    {
        return [
            'youtube_url.required' => __('Youtube url is required'),
            'youtube_url.url' => __('Youtube url is invalid'),
        ];
    }
}
