<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ];
        
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload an Excel file.',
            'file.mimes' => 'Only .xlsx and .xls files are allowed.',
            'file.max' => 'File size should not exceed 5MB.',
        ];
    }
}
