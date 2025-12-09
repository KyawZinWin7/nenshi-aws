<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMainOperationRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'plant_id' => 'required|integer|exists:plants,id',
            'machine_type_id' => 'required|integer|exists:machine_types,id',
            'machine_number_id' => 'required|integer|exists:machine_numbers,id',
            'task_id' => 'required|integer|exists:tasks,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'team_ids' => 'nullable|array',
            'team_ids.*' => 'integer|exists:employees,id',
            'small_task_id' => 'nullable|integer|exists:small_tasks,id',

        ];
    }



     public function messages(): array
        {
            return [
                    'plant_id.required' => '工場を選択してください。',
                    'plant_id.integer' => '工場を選択してください。',
                    'plant_id.exists' => '工場を選択してください。',

                    'machine_type_id.required' => '機台を選択してください。',
                    'machine_type_id.integer' => '機台を選択してください。',
                    'machine_type_id.exists' => '機台を選択してください。',

                    'machine_number_id.required' => '機台の番号を入力してください。',
                    'machine_number_id.integer' => '機台の番号を入力してください。',
                    'machine_number_id.exists' => '機台の番号を入力してください。',

                    'task_id.required' => '作業を選択してください。',
                    'task_id.integer' => '作業を選択してください。',
                    'task_id.exists' => '作業を選択してください。',

                    'employee_id.required' => '担当者を選択してください。',
                    'employee_id.integer' => '担当者を選択してください。',
                    'employee_id.exists' => '担当者を選択してください。',

                    'team_ids.array' => 'チームメンバーの選択が不正です。',
                    'team_ids.*.integer' => 'チームメンバーの選択が不正です。',
                    'team_ids.*.exists' => 'チームメンバーが存在しません。',
                    'small_task_id.integer' => '小作業の選択が不正です。',
                    'small_task_id.exists' => '小作業が存在しません。',
                ];
        }

}
