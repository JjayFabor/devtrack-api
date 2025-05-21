<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
                'description' => 'nullable|string',
                'status' => 'required|string|in:todo,in_progress,done',
                'priority' => 'required|string|in:low,medium,high',
                'deadline' => 'nullable|date',
                'is_recurring' => 'boolean'
            ];
        } else {
            return [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'status' => 'sometimes|required|string|in:todo,in_progress,done',
                'priority' => 'sometimes|required|string|in:low,medium,high',
                'deadline' => 'sometimes|nullable|date',
                'is_recurring' => 'sometimes|boolean'
            ];
        }
    }
}
