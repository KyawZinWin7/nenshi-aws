<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMainOperationRequest extends FormRequest
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
            'machine_type_id' => 'required|integer|exists:machine_types,id',
            'machine_number' => 'required|integer|min:1|max:50',
            'task_id' => 'required|integer|exists:tasks,id',
            'employee_id' => 'required|integer|exists:employees,id',
        ];
    }

    public function messages(): array
{
    return [
        'machine_type_id.required' => '機台を選択してください。',
        'machine_type_id.integer' => '機台を選択してください。。',
        'machine_type_id.exists' => '機台を選択してください。',

        'machine_number.required' => '機台の番号を入力してください。',
        'machine_number.integer' => '機台の番号を入力してください。',
        'machine_number.min' => '機台の番号を入力してください。',
        'machine_number.max' => '機台の番号を入力してください。',

        'task_id.required' => '作業を選択してください。',
        'task_id.integer' => '作業を選択してください。',
        'task_id.exists' => '作業を選択してください。',

        'employee_id.required' => '担当者を選択してください。',
        'employee_id.integer' => '担当者を選択してください。。',
        'employee_id.exists' => '担当者を選択してください。',
    ];
}

}
