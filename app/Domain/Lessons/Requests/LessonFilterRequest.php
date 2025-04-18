<?php

namespace App\Domain\Lessons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LessonFilterRequest extends FormRequest
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
            'faculty_id' => 'sometimes|exists:faculties,id',
            'department_id' => 'sometimes|exists:departments,id',
            'subject_id' => 'sometimes|exists:subjects,id',
            'title' => 'sometimes|string|max:100'
        ];
    }
}
