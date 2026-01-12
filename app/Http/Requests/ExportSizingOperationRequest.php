<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportSizingOperationRequest extends FormRequest
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
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'employee_id' => ['nullable', 'integer'],
            'machine_type_id' => ['nullable', 'integer'],
            'machine_number' => ['nullable', 'string'],
            'task_id' => ['nullable', 'integer'],
            'plant_id' => ['nullable', 'integer'],
        ];
    }




    public function messages(): array
    {
        return [
            'date_from.required' => '開始日を選択してください。',
            'date_to.required' => '終了日を選択してください。',
            'date_to.after_or_equal' => '終了日は開始日以降である必要があります。',
            
        ];
    }
}
