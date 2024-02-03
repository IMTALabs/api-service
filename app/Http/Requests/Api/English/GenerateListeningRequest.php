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
            'listen_link' => 'required|url',
        ];
    }

    public function messages(): array
    {
        return [
            'listen_link.required' => __('Youtube url is required'),
            'listen_link.url' => __('Youtube url is invalid'),
        ];
    }
}
