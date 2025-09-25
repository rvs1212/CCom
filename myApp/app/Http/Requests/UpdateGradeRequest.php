<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
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
            'grade' => 'required|string|in:'.implode(',', config('data.grade_options', ['A','B','C','D','F'])),
        ];
    }


    public function messages(): array
    {
        return [
            'grade.required' => 'Grade is required.',
            'grade.in' => 'Grade must be one of the following: '.implode(', ', config('data.grade_options', ['A','B','C','D','F'])).'.',
        ];
    }
}
