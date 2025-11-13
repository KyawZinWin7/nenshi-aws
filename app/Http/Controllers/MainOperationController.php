<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreMainOperationRequest;
use App\Http\Requests\UpdateMainOperationRequest;
use App\Http\Requests\ExportMainOperationRequest;
use App\Exports\MainOperationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

class MainOperationController extends Controller
{
    

    
    public function index()
        {
           
            
            if (auth()->user()->role === 'superadmin') {
                // Super Admin: 
                $mainoperations = MainOperation::with([
                    'machineType',
                    'task',
                    'employee',
                    'machineNumber',
                    'plant',
                    'members'
                ])
                ->where('status', 0)
                ->orderBy('updated_at', 'desc')
                ->take(200)
                ->get();


            } else {
                // User and Admin
                $mainoperations = MainOperation::with([
                    'machineType',
                    'task',
                    'employee',
                    'machineNumber',
                    'plant',
                    'members'
                ])
                ->where('status', 0)
                // ->where('employee_id', Auth::id())
                // ->orWhereHas('members', function ($query) {
                //     $query->where('employee_id', Auth::id());
                // })
                ->where('department_id',Auth::user()->department_id)
                ->latest('updated_at')
                ->take(200)
                ->get();
            }

            

          

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



    public function store(StoreMainOperationRequest $request)
    {
        $modata = $request->validated();

        // Duplicate Check
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

        //  新規登録
        $modata['start_time'] = now();
        $modata['status'] = 0;
        $modata['department_id'] = Auth::user()->department_id;

        $mainOperation = MainOperation::create($modata);

        if (!empty($modata['team_ids'])) {
            $mainOperation->members()->attach($modata['team_ids']);
        }

        
        return redirect()->route('home')
            ->with('success', '作業が登録されました。');
    }





    public function edit(MainOperation $mainoperation)
    {
        return Inertia::render('Home', [
            'mainoperation' => new MainOperationResource($mainoperation->load('machineType')),
        ]);
    }





   public function update(UpdateMainOperationRequest $request, MainOperation $mainoperation)
{
    \Log::info('Update reached', [
        'user_id' => auth()->id(),
        'mainOperation_id' => $mainoperation->id,
        'request' => $request->all()
    ]);

    $modata = $request->validated();

    $existing = MainOperation::where('plant_id', $modata['plant_id'])
        ->where('machine_type_id', $modata['machine_type_id'])
        ->where('machine_number_id', $modata['machine_number_id'])
        ->where('task_id', $modata['task_id'])
        ->where('status', 0)
        ->where('id', '!=', $mainoperation->id)
        ->with('employee')
        ->first();

    if ($existing) {
        return back()->withErrors([
            'duplicate' => '同じ工場・機台・番号・作業の未完了レコードが既に存在します。（担当者：' . ($existing->employee->name ?? '不明') . '）'
        ])->withInput();
    }

    $mainoperation->update($modata);

    if (!empty($modata['team_ids'])) {
        $mainoperation->members()->sync($modata['team_ids']);
    } else {
        $mainoperation->members()->detach();
    }

    return redirect()->route('home')
        ->with('success', '作業が更新されました。');
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
            $operation->completed_by = auth()->id();
            $operation->uncompleted_by = null;
            $operation->save();

            return redirect()->back()->with('success', '作業を完了しました。');
        }


        


    public function uncomplete($id)
        {
            $operation = MainOperation::findOrFail($id);
            $operation->end_time = null;
        
            $operation->total_time = '00:00:00';
            $operation->status = 0;
            $operation->uncompleted_by = auth()->id();
            $operation->completed_by = null; 
            $operation->save();

            return redirect()->back()->with('success', '作業を未完了にしました。');
        }




  

    public function export()
        {
        $mainoperations = MainOperation::with(['machineType', 'task', 'employee','plant'])
                    ->paginate(1500);
                    $machinetypes = MachineTypeResource::collection(MachineType::all());
                    $tasks = TaskResource::collection(Task::all());
                    $employees = EmployeeResource::collection(Employee::all());
                    $plants = PlantResource::collection(Plant::all());

                    return Inertia::render('Report/Export', [
                        'mainoperations' => MainOperationResource::collection($mainoperations),
                        'machinetypes' => $machinetypes,
                        'tasks' => $tasks,
                        'employees' => $employees,
                        'plants' => $plants,
                    ]);
        }





    public function exportStore(ExportMainOperationRequest $request)
    {
        try {
            $filters = $request->validated();

            $exists = MainOperation::query()
                ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                ->when(!empty($filters['employee_id']), fn($q) => $q->where('employee_id', $filters['employee_id']))
                ->when(!empty($filters['machine_type_id']), fn($q) => $q->where('machine_type_id', $filters['machine_type_id']))
                ->when(!empty($filters['task_id']), fn($q) => $q->where('task_id', $filters['task_id']))
                ->when(!empty($filters['plant_id']), fn($q) => $q->where('plant_id', $filters['plant_id']))
                ->when(!empty($filters['machine_number']), function ($q) use ($filters) {
                    $q->whereHas('machineNumber', function ($q) use ($filters) {
                        $q->where('number', $filters['machine_number']);
                    });
                })
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'エクスポートできるデータがありません。',
                ], 404);
            }

            return Excel::download(new MainOperationsExport($filters), 'mainoperations.xlsx');
        } catch (\Exception $e) {
            \Log::error('Export Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



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
                ->take(500)
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

           

            if (!in_array($user->role, ['superadmin', 'admin'])) {
                abort(403, 'アクセス権限がありません'); // 403 Forbidden
            }

            if(auth()->user()->role === 'superadmin'){


                $machinetypes = MachineTypeResource::collection(MachineType::all());
                $tasks = TaskResource::collection(Task::all());
                $mainoperations = MainOperation::with(['machineType', 'task', 'employee', 'machineNumber', 'plant', 'members'])
                ->latest('updated_at')
                ->take(500)
                ->get();
                return Inertia::render('Complete/AdminCompleteList', [
                            'mainoperations' => MainOperationResource::collection($mainoperations),
                            'machinetypes' => $machinetypes,
                            'tasks' => $tasks,
                        ]);
            }else{
                
                $machinetypes = MachineTypeResource::collection(
                    MachineType::where('department_id', Auth::user()->department_id)->get()
                );

                $tasks = TaskResource::collection(
                    Task::where('department_id', Auth::user()->department_id)->get()
                );
                $mainoperations = MainOperation::with(['machineType', 'task', 'employee', 'machineNumber', 'plant', 'members'])
                ->where('department_id',Auth::user()->department_id)
                ->latest('updated_at')
                ->take(500)
                ->get();
                return Inertia::render('Complete/AdminCompleteList', [
                            'mainoperations' => MainOperationResource::collection($mainoperations),
                            'machinetypes' => $machinetypes,
                            'tasks' => $tasks,
                ]);
            }
           
        
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






    public function destroy($id)
        {
            $operation = MainOperation::findOrFail($id);
            $operation->delete();

            return redirect()->back()->with('success', '作業を削除しました。');



            

        }

    }