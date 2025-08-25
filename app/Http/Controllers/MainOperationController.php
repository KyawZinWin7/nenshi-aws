<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainOperation;
use App\Http\Resources\MainOperationResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\Task;
use App\Models\MachineType;
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
            $mainoperations = MainOperation::with(['machineType', 'task', 'employee'])
            ->paginate(1500);
            $machinetypes = MachineTypeResource::collection(MachineType::all());
            $tasks = TaskResource::collection(Task::all());
            $employees = EmployeeResource::collection(Employee::all());

            return Inertia::render('Home', [
                'mainoperations' => MainOperationResource::collection($mainoperations),
                'machinetypes' => $machinetypes,
                'tasks' => $tasks,
                'employees' => $employees,
            ]);
        }


    
    public function store(StoreMainOperationRequest $request)
        {
            $modata = $request->validated();

            // 既に未完了の同じ作業が存在するか確認（重複チェック）
            $exists = MainOperation::where('machine_type_id', $modata['machine_type_id'])
                ->where('machine_number', $modata['machine_number'])
                ->where('task_id', $modata['task_id'])
                ->where('status', 0) // 未完了のみを対象
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['duplicate' => '同じ機台・番号・作業の未完了レコードが既に存在します。'])
                    ->withInput();
            }

            // データを追加
            $modata['start_time'] = now();
            $modata['status'] = 0; // 初期状態は未完了として保存

            MainOperation::create($modata);

            return redirect()->route('home')->with('success', '作業が登録されました。');
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
            $filters = $request->all();

            // Query build
            $query = MainOperation::query();

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }
            if (!empty($filters['employee_id'])) {
                $query->where('employee_id', $filters['employee_id']);
            }
            if (!empty($filters['machine_type_id'])) {
                $query->where('machine_type_id', $filters['machine_type_id']);
            }
            if (!empty($filters['machine_number'])) {
                $query->where('machine_number', $filters['machine_number']);
            }
            if (!empty($filters['task_id'])) {
                $query->where('task_id', $filters['task_id']);
            }

            $data = $query->get();

            // ✅ ဒီမှာ စစ်ပြီး JSON message ပြန်ပေးမယ်
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'エクスポートできるデータがありません。'
                ], 404);
            }

            // Data ရှိမှ Excel ထုတ်
            return Excel::download(new MainOperationsExport($filters), 'mainoperations.xlsx');
        }




    public function completelist()
        {
            $machinetypes = MachineTypeResource::collection(MachineType::all());
            $tasks = TaskResource::collection(Task::all());
            $mainoperations = MainOperation::with(['machineType', 'task', 'employee'])
            ->latest('updated_at')
            ->take(1000)
            ->get();
            return Inertia::render('Complete/CompleteList', [
                        'mainoperations' => MainOperationResource::collection($mainoperations),
                        'machinetypes' => $machinetypes,
                        'tasks' => $tasks,
                    ]);
        
        }


 public function destroy($id)
        {
            $operation = MainOperation::findOrFail($id);
            $operation->delete();

            return redirect()->back()->with('success', '作業を削除しました。');



            

        }

    }