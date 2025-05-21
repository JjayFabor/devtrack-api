<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ErrorRequest extends FormRequest
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
                'description' => 'required|string',
                'code_snippet' => 'nullable|string',
                'cause' => 'nullable|string',
                'resolution' => 'nullable|string',
                'severity' => 'required|string|in:low,medium,high',
                'status' => 'required|string|in:unresolved,resolved',
            ];
        } else {
            return [
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'code_snippet' => 'sometimes|nullable|string',
                'cause' => 'sometimes|nullable|string',
                'resolution' => 'sometimes|nullable|string',
                'severity' => 'sometimes|string|in:low,medium,high',
                'status' => 'sometimes|string|in:unresolved,resolved',
            ];
        }
    }
}
