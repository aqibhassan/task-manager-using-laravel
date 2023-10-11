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
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|date|after:today',
            'priority' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|in:new,in_progress,on_hold,completed,review',
            'completed' => 'sometimes|required|boolean',
            'notes' => 'nullable|string'
        ];
    }
}
