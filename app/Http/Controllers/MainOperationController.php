<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainOperation;
use App\Http\Resources\MainOperationResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PlantResource;
use App\Http\Resources\MachineNumberResource;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Plant;
use App\Models\MachineType;
use App\Models\MachineNumber;
use Inertia\Inertia;
use App\Http\Requests\StoreMainOperationRequest;
use App\Http\Requests\UpdateMainOperationRequest;
use App\Exports\MainOperationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

class MainOperationController extends Controller
{
    

    
    public function index()
        {
            // $mainoperations = MainOperation::with(['machineType', 'task', 'employee'])
            //     ->where('status', 0)
            //     ->orderBy('updated_at', 'desc')
            //     ->take(1000)
            //     ->get();

            $mainoperations = MainOperation::with(['machineType', 'task', 'employee', 'machineNumber', 'plant', 'members'])
                ->where('status', 0)
                ->orderBy('updated_at', 'desc')
                ->take(1000)
                ->get();

            $machinetypes = MachineTypeResource::collection(MachineType::all());
            $tasks = TaskResource::collection(Task::all());
            $employees = EmployeeResource::collection(Employee::all());
            $plants = PlantResource::collection(Plant::all());
            $machinenumbers = MachineNumberResource::collection(MachineNumber::all());

            return Inertia::render('Home', [
                'mainoperations' => MainOperationResource::collection($mainoperations),
                'machinetypes' => $machinetypes,
                'tasks' => $tasks,
                'employees' => $employees,
                'plants' => $plants,
                'machinenumbers' => $machinenumbers,
            ]);
        }


    // public function store(StoreMainOperationRequest $request)
    //     {
    //         $modata = $request->validated();

    //         // 重複チェック
    //         $exists = MainOperation::where('machine_type_id', $modata['machine_type_id'])
    //             ->where('machine_number_id', $modata['machine_number_id'])
    //             ->where('task_id', $modata['task_id'])
    //             ->where('status', 0)
    //             ->exists();

    //         if ($exists) {
    //             return redirect()->back()
    //                 ->withErrors(['duplicate' => '同じ機台・番号・作業の未完了レコードが既に存在します。'])
    //                 ->withInput();
    //         }

    //         // Main Operation 作成
    //         $modata['start_time'] = now();
    //         $modata['status'] = 0;

    //         $mainOperation = MainOperation::create($modata);

    //         // ここでチームメンバーを attach
    //         if (!empty($modata['team_ids'])) {
    //             $mainOperation->members()->attach($modata['team_ids']);
    //         }

    //         return redirect()->route('home')->with('success', '作業が登録されました。');
    //     }


   public function store(StoreMainOperationRequest $request)
{
    $modata = $request->validated();

    // 🔍 Duplicate Check
    $existing = MainOperation::where('plant_id', $modata['plant_id'])
        ->where('machine_type_id', $modata['machine_type_id'])
        ->where('machine_number_id', $modata['machine_number_id'])
        ->where('task_id', $modata['task_id'])
        ->where('status', 0)
        ->with('employee')
        ->first();

    if ($existing) {
        return back()->withErrors([
            'duplicate' => '同じ工場・機台・番号・作業の未完了レコードが既に存在します。（担当者：' . ($existing->employee->name ?? '不明') . '）'
        ])->withInput();
    }

    // 🆕 新規登録
    $modata['start_time'] = now();
    $modata['status'] = 0;

    $mainOperation = MainOperation::create($modata);

    if (!empty($modata['team_ids'])) {
        $mainOperation->members()->attach($modata['team_ids']);
    }

    // ✅ 一覧ページへリダイレクト
    return redirect()->route('home')
        ->with('success', '作業が登録されました。');
}







    public function complete($id)
        {
            $operation = MainOperation::findOrFail($id);

            $operation->end_time = Carbon::now('Asia/Tokyo');

            $start = Carbon::parse($operation->start_time, 'Asia/Tokyo');
            $end = Carbon::parse($operation->end_time, 'Asia/Tokyo');

            if ($end->lt($start)) {
                return redirect()->back()->with('error', '終了時間は開始時間より後でなければなりません。');
            }

            $diff = $start->diff($end);
            $operation->total_time = sprintf('%02d:%02d:%02d', $diff->h + ($diff->d * 24), $diff->i, $diff->s);

            $operation->status = 1;
            $operation->save();

            return redirect()->back()->with('success', '作業を完了しました。');
        }


        


    public function uncomplete($id)
        {
            $operation = MainOperation::findOrFail($id);
            $operation->end_time = null;
        
            $operation->total_time = '00:00:00';
            $operation->status = 0;
            $operation->save();

            return redirect()->back()->with('success', '作業を未完了にしました。');
        }




  

    public function export()
        {
        $mainoperations = MainOperation::with(['machineType', 'task', 'employee'])
                    ->paginate(1500);
                    $machinetypes = MachineTypeResource::collection(MachineType::all());
                    $tasks = TaskResource::collection(Task::all());
                    $employees = EmployeeResource::collection(Employee::all());

                    return Inertia::render('Report/Export', [
                        'mainoperations' => MainOperationResource::collection($mainoperations),
                        'machinetypes' => $machinetypes,
                        'tasks' => $tasks,
                        'employees' => $employees,
                    ]);
        }





    public function exportStore(Request $request)
        {
            try {
                $filters = $request->all();

                // အရင် data ရှိမရှိ စစ်ချင်ရင် ->exists() သုံးတာ ပိုသင့်တင့်
                $exists = MainOperation::query()
                    ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                    ->when(!empty($filters['employee_id']), fn($q) => $q->where('employee_id', $filters['employee_id']))
                    ->when(!empty($filters['machine_type_id']), fn($q) => $q->where('machine_type_id', $filters['machine_type_id']))
                    ->when(!empty($filters['machine_number']), fn($q) => $q->where('machine_number', $filters['machine_number']))
                    ->when(!empty($filters['task_id']), fn($q) => $q->where('task_id', $filters['task_id']))
                    ->exists();

                if (!$exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'エクスポートできるデータがありません。'
                    ], 404);
                }

                // ဒီနေရာမှာ get() မလုပ်ဘဲ တိုက်ရိုက် Export class ကိုပေးမယ်
                return Excel::download(new MainOperationsExport($filters), 'mainoperations.xlsx');

            } catch (\Exception $e) {
                \Log::error('Export Error: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }









    // public function completelist()
    //     {
    //         $machinetypes = MachineTypeResource::collection(MachineType::all());
    //         $tasks = TaskResource::collection(Task::all());
    //         $mainoperations = MainOperation::with(['machineType', 'task', 'employee', 'machineNumber', 'plant', 'members'])
    //         ->where('employee_id', auth()->id())
    //         ->latest('updated_at')
    //         ->take(1000)
    //         ->get();
    //         return Inertia::render('Complete/CompleteList', [
    //                     'mainoperations' => MainOperationResource::collection($mainoperations),
    //                     'machinetypes' => $machinetypes,
    //                     'tasks' => $tasks,
    //                 ]);
        
    //     }




    public function completelist()
        {
            $machinetypes = MachineTypeResource::collection(MachineType::all());
            $tasks = TaskResource::collection(Task::all());

            $mainoperations = MainOperation::with([
                    'machineType', 
                    'task', 
                    'employee', 
                    'machineNumber', 
                    'plant', 
                    'members'
                ])
                ->where('employee_id', auth()->id()) // owner
                ->orWhereHas('members', function ($query) {
                    $query->where('employee_id', auth()->id()); // member
                })
                ->latest('updated_at')
                ->take(1000)
                ->get();

            return Inertia::render('Complete/CompleteList', [
                'mainoperations' => MainOperationResource::collection($mainoperations),
                'machinetypes' => $machinetypes,
                'tasks' => $tasks,
            ]);
        }




    public function admincompletelist()

        {

            $user = auth()->user();

            if ($user->role !== 'admin') {
                abort(403, 'アクセス権限がありません'); // 403 Forbidden
            }

            $machinetypes = MachineTypeResource::collection(MachineType::all());
            $tasks = TaskResource::collection(Task::all());
            $mainoperations = MainOperation::with(['machineType', 'task', 'employee', 'machineNumber', 'plant', 'members'])
            ->latest('updated_at')
            ->take(1000)
            ->get();
            return Inertia::render('Complete/AdminCompleteList', [
                        'mainoperations' => MainOperationResource::collection($mainoperations),
                        'machinetypes' => $machinetypes,
                        'tasks' => $tasks,
                    ]);
        
        }



    public function getMachinesByPlant($plantId)
        {
            $machineTypes = MachineType::whereHas('plants', function ($q) use ($plantId) {
                $q->where('plants.id', $plantId);
            })->get();

            // related machine numbers
            $machineNumbers = MachineNumber::whereHas('machineTypePlant', function ($q) use ($plantId) {
                $q->where('plant_id', $plantId);
            })->get();

            return response()->json([
                'machineTypes' => $machineTypes,
                'machineNumbers' => $machineNumbers,
            ]);
        }


    public function getMachineNumbersByType(Request $request)
{
    $plantId = $request->plant_id;
    $typeId  = $request->machine_type_id;

    if (!$plantId || !$typeId) {
        return response()->json([]);
    }

    $machineNumbers = MachineNumber::whereHas('machineTypePlant', function ($q) use ($plantId, $typeId) {
        $q->where('plant_id', $plantId)
          ->where('machine_type_id', $typeId);
    })->get();

    return response()->json($machineNumbers);
}


// public function getTasksByMachineType(Request $request)
// {
//     $typeId = $request->machine_type_id;

//     if (!$typeId) {
//         return response()->json([], 400);
//     }

//     $tasks = Task::where('machine_type_id', $typeId)->get();

//     return response()->json($tasks);
// }

 



    public function destroy($id)
        {
            $operation = MainOperation::findOrFail($id);
            $operation->delete();

            return redirect()->back()->with('success', '作業を削除しました。');



            

        }

    }