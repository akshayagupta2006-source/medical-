<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicalCaseRequest extends FormRequest
{
    /**
     * Get the targeted medical case instance.
     */
    protected function getTargetCase(): ?MedicalCase
    {
        $caseParam = $this->route('case') ?? $this->route('id');

        if ($caseParam instanceof MedicalCase) {
            return $caseParam;
        }

        return $caseParam ? MedicalCase::find($caseParam) : null;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $case = $this->getTargetCase();

        if (! $case) {
            return true; // Let the controller handle 404
        }

        return $this->user() && $this->user()->can('update', $case);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $case = $this->getTargetCase();
        $caseId = $case ? $case->id : ($this->route('case') ?? $this->route('id'));

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => ['required', 'string', 'max:100'],
            'surgeon_name' => ['required', 'string', 'max:100'],
            'implant_type' => ['required', 'string', 'max:150'],
            'status' => ['required', 'string', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', 'string', Rule::in(MedicalCase::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'case_number.required' => 'The case number is required.',
            'case_number.unique' => 'This case number is already in use by another case.',
            'patient_reference.required' => 'The patient reference is required.',
            'surgeon_name.required' => 'The surgeon name is required.',
            'implant_type.required' => 'The implant type is required.',
            'status.required' => 'A valid case status is required.',
            'priority.required' => 'A valid priority level is required.',
            'surgery_date.date' => 'The surgery date must be a valid date.',
        ];
    }
}
