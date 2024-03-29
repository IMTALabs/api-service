<?php

namespace App\Http\Requests\Api\English;

use Illuminate\Foundation\Http\FormRequest;

class GradingListeningRequest extends FormRequest
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
            'hash' => 'required|string|size:32|exists:english_requests,hash',
            'submit' => 'required',
        ];
    }
}
