<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDecisionRequest extends FormRequest
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
            'decision' => 'required|string|in:'.implode(',', config('data.decision_option', [ 'NOT YET TAKEN','STAYS AT COMPANY','MOVE TO DIFFERENT POSITION','LET GO'])),
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Decision is required.',
            'decision.in' => 'Decision must be one of the following: '.implode(', ', config('data.decision_option', [ 'NOT YET TAKEN','STAYS AT COMPANY','MOVE TO DIFFERENT POSITION','LET GO'])).'.',
        ];
    }
}
