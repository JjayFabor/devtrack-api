<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'tags' => 'nullable|array',
                'github_url' => app()->environment('local') ? 'nullable|string' : 'nullable|url:https',
                'status' => 'required|string|in:planning,active,paused,completed',
            ];
        } else {
            return [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'tags' => 'nullable|array',
                'github_url' => app()->environment('local') ? 'sometimes|nullable|string' : 'sometimes|nullable|url:https',
                'status' => 'sometimes|required|string|in:planning,active,paused,completed',
            ];
        }
    }
}
