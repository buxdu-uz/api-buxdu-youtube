<?php

namespace App\Domain\Lessons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.*.teacher_id' => 'required|exists:users,id',
            'data.*.subject_id' => 'required|exists:subjects,id',
            'data.*.title' => 'required|string|max:255',
            'data.*.url' => 'required|url',
            'data.*.date' => 'required|date'
        ];
    }
}
