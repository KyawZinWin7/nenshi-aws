<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MachineTypePlant;
use App\Models\MachineNumber;


class UpdateMachineNumberRequest extends FormRequest
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
            'plant_id' => ['required', 'exists:plants,id'],
            'machine_type_id' => ['required', 'exists:machine_types,id'],
            'start_number' => 'required|integer|min:1',
            // 'end_number' => 'required|integer|gte:start_number',
            'auto_stop_hours' => 'nullable|integer',
        ];
    }


     public function messages(): array
    {
        return [
            'plant_id.required' => '工場を選択してください。',
            'plant_id.exists' => '選択された工場は存在しません。',
            'machine_type_id.required' => '機台を選択してください。',
            'machine_type_id.exists' => '選択された機台は存在しません。',
            'start_number.required' => '開始番号を入力してください。',
            'end_number.required' => '終了番号を入力してください。',
            'end_number.gte' => '終了番号は開始番号以上である必要があります。',

        ];

    }


    public function after(): array
{
    return [
        function ($validator) {
            $plantId = $this->input('plant_id');
            $machineTypeId = $this->input('machine_type_id');
            $start = $this->input('start_number');
            $end = $this->input('end_number');

            // check connection pivot 
            $relation = MachineTypePlant::where('plant_id', $plantId)
                ->where('machine_type_id', $machineTypeId)
                ->first();

            if ($relation) {
                // 🔹 Store request နဲ့မတူ — update လုပ်တဲ့အခါは skip
                if ($this->isMethod('put') || $this->isMethod('patch')) {
                    // update の場合は skip する（重複チェックしない）
                    return;
                }

                $exists = MachineNumber::where('machine_type_plant_id', $relation->id)
                    ->whereBetween('number', [$start, $end])
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'number',
                        'この工場と機台の番号範囲は既に登録されています。'
                    );
                }
            }
        }
    ];
}




}
