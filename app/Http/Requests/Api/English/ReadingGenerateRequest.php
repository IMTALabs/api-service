<?php

namespace App\Http\Requests\Api\English;

use Illuminate\Foundation\Http\FormRequest;

class ReadingGenerateRequest extends FormRequest
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
            'mode' => 'required|in:gen_topic,no_gen_topic',
            'topic' => 'nullable',
            'paragraph' => 'required',
        ];
    }
}
