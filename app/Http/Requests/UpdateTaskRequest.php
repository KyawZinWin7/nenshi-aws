<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'name'=>'required|string|max:255',
            'machine_type_id' => 'required|exists:machine_types,id',
            'department_id' => 'required|exists:departments,id'
        ];
    }


     public function messages(): array
    {
        return [
            'name.required' => '名前を入力してください。',
            'name.string' => '名前は文字列である必要があります。',
            'name.max' => '名前は255文字以内で入力してください。',
            'machine_type_id.required' => '機台は必須です。',
            'machine_type_id.exists' => '選択された機台は無効です。',
            'department_id.required' => '部門を入力してください。',
        ];
    }
}
