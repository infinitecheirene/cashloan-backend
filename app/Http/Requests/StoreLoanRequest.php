<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isBorrower() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:personal,auto,home,business,student',
            'principal_amount' => 'required|numeric|min:100|max:10000000',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1|max:360',
            'purpose' => 'required|string|min:10|max:1000',
            'employment_status' => 'nullable|in:employed,self_employed,business_owner,unemployed',
            
            // Document validation
            'documents' => 'required|array|min:1',
            'documents.*.type' => 'required|string',
            'documents.*.file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Loan type is required',
            'type.in' => 'Invalid loan type selected',
            'principal_amount.required' => 'Loan amount is required',
            'principal_amount.min' => 'Minimum loan amount is $100',
            'principal_amount.max' => 'Maximum loan amount is $10,000,000',
            'interest_rate.required' => 'Interest rate is required',
            'interest_rate.min' => 'Interest rate cannot be negative',
            'term_months.required' => 'Loan term is required',
            'term_months.min' => 'Minimum term is 1 month',
            'term_months.max' => 'Maximum term is 30 years (360 months)',
            'purpose.required' => 'Loan purpose is required',
            'purpose.min' => 'Purpose must be at least 10 characters',
            'employment_status.in' => 'Invalid employment status',
            'documents.required' => 'At least one document is required',
            'documents.*.file.required' => 'Document file is required',
            'documents.*.file.mimes' => 'Document must be a JPG, PNG, or PDF file',
            'documents.*.file.max' => 'Document size cannot exceed 10MB',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert FormData array format to proper array
        if ($this->has('documents')) {
            $documents = [];
            $i = 0;
            
            while ($this->has("documents.$i.type")) {
                $documents[] = [
                    'type' => $this->input("documents.$i.type"),
                    'file' => $this->file("documents.$i.file"),
                ];
                $i++;
            }
            
            if (!empty($documents)) {
                $this->merge(['documents' => $documents]);
            }
        }
    }
}