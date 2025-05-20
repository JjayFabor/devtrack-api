<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LearningLogRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'title' => 'required|string|max:255',
                'topic' => 'required|string|max:255',
                'summary' => 'required|string',
                'duration' => 'required|integer',
                'resources' => 'nullable|array',
            ];
        } else {
            return [
                'title' => 'sometimes|required|string|max:255',
                'topic' => 'sometimes|required|string|max:255',
                'summary' => 'sometimes|required|string',
                'duration' => 'sometimes|required|integer',
                'resources' => 'sometimes|nullable|array',
            ];
        }
    }
}
