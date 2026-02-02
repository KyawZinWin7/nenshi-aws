<script setup>

import { Head, useForm } from '@inertiajs/vue3'
import Container from '../../Components/Container.vue'
import PrimaryBtn from '../../Components/PrimaryBtn.vue'
import { ref, onMounted, onUnmounted, watch, h } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import {
    CircleCheck,   // 完了
    VideoPause,   // 止
    VideoPlay,    // 再開
    Edit,         // 編集
    Delete,        // 削除
    Plus,         // 追加
} from '@element-plus/icons-vue'




/* ================= PROPS ================= */
const props = defineProps({
    employees: Object,
    plants: Object,
    machinetypes: Object,
    tasks: Object,
    machinenumbers: Object,
    sizingoperations: Object,
    smalltasks: Object,
})


/* ================= FORM ================= */
const form = useForm({
    team_ids: [],
    plant_id: '',
    machine_type_id: '',
    machine_number_id: '',
    task_id: '',
    small_task_id: '',
})

/* ================= TEAM ================= */
const teamMembers = ref(props.employees.data || [])



/* ================= MACHINE FILTER ================= */
//For Small task filter by machine type 
const machinenumbers = ref([])
const machinetypes = ref([])
const tasks = ref([])
const smalltasks = ref([]);

// watch(
//     () => form.machine_type_id,
//     async (newTypeId) => {
//         if (!newTypeId) {
//             machinenumbers.value = []
//             tasks.value = []
//             form.machine_number_id = ''
//             form.task_id = ''
//             return
//         }

//         const numbersResponse = await axios.get('/machines/by-type', {
//             params: {
//                 plant_id: form.plant_id,
//                 machine_type_id: newTypeId,
//             },
//         })
//         machinenumbers.value = numbersResponse.data

//         const tasksResponse = await axios.get('/tasks/by-machine-type', {
//             params: { machine_type_id: newTypeId },
//         })
//         tasks.value = tasksResponse.data
//     }
// )
watch(() => form.machine_type_id, async (newTypeId) => {
    if (!newTypeId) {
        machinenumbers.value = []
        tasks.value = []
        smalltasks.value = []
        form.machine_number_id = ''
        form.task_id = ''
        if (!isEditing.value) form.small_task_id = ''
        return
    }

    const [numbersRes, tasksRes, smallRes] = await Promise.all([
        axios.get('/machines/by-type', {
            params: { plant_id: form.plant_id, machine_type_id: newTypeId }
        }),
        axios.get('/tasks/by-machine-type', {
            params: { machine_type_id: newTypeId }
        }),
        axios.get('/smalltasks/by-machine-type', {
            params: { machine_type_id: newTypeId }
        })
    ])

    machinenumbers.value = numbersRes.data
    tasks.value = tasksRes.data
    smalltasks.value = smallRes.data
})

watch(
    () => form.plant_id,
    async (newPlantId) => {
        if (!newPlantId) {
            machinetypes.value = []
            machinenumbers.value = []
            form.machine_type_id = ''
            form.machine_number_id = ''
            return
        }

        const { data } = await axios.get(`/machines/by-plant/${newPlantId}`)
        machinetypes.value = data.machineTypes
        machinenumbers.value = data.machineNumbers
    }
)

//For Small task filter by machine type
// watch(
//     () => form.machine_type_id,
//     async (newTypeId) => {
//         if (!newTypeId) {
//             smalltasks.value = [];
//             if (!isEditing.value) form.small_task_id = "";
//             return;
//         }

//         try {
//             const response = await axios.get(`/smalltasks/by-machine-type`, {
//                 params: { machine_type_id: newTypeId }
//             });
//             smalltasks.value = response.data;

//             if (!isEditing.value) form.small_task_id = "";
//         } catch (error) {
//             console.error("Error fetching small tasks:", error);
//         }
//     }
// );



const sizingoperations = ref([])

watch(
    () => props.sizingoperations.data,
    (newData) => {
        sizingoperations.value = newData.map(op => ({
            ...op,
            show: false,
            menu: false,
        }))
    },
    { immediate: true }
)


/* ================= SUBMIT FORM ================= */
// GLOBAL CLICK (MENU CLOSE) 
// onMounted(() => {
//     window.addEventListener('click', () => {
//         sizingoperations.value.forEach(op => {
//             op.menu = false
//         })
//     })
// })



const closeMenus = () => {
    sizingoperations.value.forEach(op => {
        op.menu = false
    })
}

onMounted(() => {
    window.addEventListener('click', closeMenus)
})

onUnmounted(() => {
    window.removeEventListener('click', closeMenus)
})



//For Create and Edit 

const isEditing = ref(false);
const editingId = ref(null);

const submitForm = () => {
    if (isEditing.value) {
        updateSizingOperation();
    } else {
        createSizingOperation()
    }
}



/* ================= CREATE ================= */
const createSizingOperation = () => {
    form.post(route('nenshioperations.store'), {
        onSuccess: () => {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'スタートしました',
                showConfirmButton: false,
                timer: 1500,
            })
            form.reset()
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'エラーが発生しました',
                text: 'もう一度試してください。',
            })
        },
    })
}

//For Edit Operation



const editSizingOperation = async (op) => {
    // console.log("Editing Sizing Operation:", op.sizinglogs);

    isEditing.value = true;
    editingId.value = op.id;

    // team ids (from logs, unique)
    form.team_ids = [
        ...new Set(
            op.sizinglogs
                ?.map(log => log.employee?.id)
                .filter(Boolean) // remove null/undefined
        )
    ];



    // plant
    form.plant_id = op.plant?.id ?? '';

    await new Promise(resolve => setTimeout(resolve, 300));

    // machine type
    form.machine_type_id = op.machine_type?.id ?? '';

    await new Promise(resolve => setTimeout(resolve, 300));

    form.small_task_id = op.small_task?.id ?? '';
    await new Promise(resolve => setTimeout(resolve, 300));
    // rest
    form.machine_number_id = op.machine_number?.id ?? '';
    form.task_id = op.task?.id ?? '';
};


/* ================= COMPLETE LOG ================= */

const completeForm = useForm({});
const completeSMO = async (opId) => {
    //Confirm Dialog
    const confirmResult = await Swal.fire({
        title: "この作業を完了してもよろしいですか？",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "はい、完了します。",
        cancelButtonText: "キャンセル",

    });

    if (!confirmResult.isConfirmed) return;

    //Complete sizing operation directly

    completeForm.post(route('sizingoperations.complete', { id: opId }), {
        onSuccess: () => {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '作業が完了しました',
                showConfirmButton: false,
                timer: 1500,
            })
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'エラーが発生しました',
                text: 'もう一度試してください。',
            })
        },
    })

}


/* ================= DELETE Sizing Operation ================= */
const deleteForm = useForm({});
const deleteSizingOperation = async (opId) => {
    const result = await Swal.fire({
        title: 'この作業を削除してもよろしいですか？',
        text: 'この操作は元に戻せません。',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'はい、削除します。',
        cancelButtonText: 'キャンセル',
    })

    if (!result.isConfirmed) return

    deleteForm.delete(route("sizingoperations.destroy", opId), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "削除されました！",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "削除に失敗しました",
                text: "もう一度お試しください。",
            });
        }
    });
}

const cancelEdit = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
};


//For add Employees



const addEmployeeDialog = ref(false)
const selectedEmployeeIds = ref([])
// const editingId = ref(null)

const openAddEmployeeModal = (op) => {
    editingId.value = op.id



    addEmployeeDialog.value = true
}



const addEmployeeForm = useForm({
    employee_ids: [],
})


const confirmAddEmployees = () => {
    addEmployeeForm.employee_ids = selectedEmployeeIds.value

    addEmployeeForm.post(
        route('sizingoperations.addEmployees', { id: editingId.value }),
        {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: '担当者が追加されました',
                    showConfirmButton: false,
                    timer: 1200,
                })

                addEmployeeDialog.value = false
                selectedEmployeeIds.value = []
                addEmployeeForm.reset()
            },
            onError: (errors) => {
                console.log(errors)
                Swal.fire({
                    icon: 'error',
                    title: 'エラーが発生しました',
                    text: 'もう一度試してください。',
                })
            },
        }
    )
}


//For Stop Operation

const stopSizingOperation = async (opId) => {
    const confirm = await Swal.fire({
        title: '作業を停止しますか？',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル',
    })

    if (!confirm.isConfirmed) return

    axios.post(route('sizingoperations.stop', { operation: opId }))
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: '作業を停止しました',
                timer: 1200,
                showConfirmButton: false,
            })

            location.reload()
        })
}

// Stop SizingLog
const stopSizingLog = async (logId) => {
    const result = await Swal.fire({
        title: '自分の作業を停止してもよろしいですか？',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル',
    })

    if (!result.isConfirmed) return
    axios.post(route('sizinglogs.stop', { log: logId }))
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: '1200',
                showConfirmButton: false,
            })

            location.reload()
        })
}



//For Resume Operation
const editResumeEmployee = async (op) => {
    // console.log("Editing Sizing Operation:", op.sizinglogs);

    editingId.value = op.id;

    // team ids (from logs, unique)
    form.team_ids = [
        ...new Set(
            op.sizinglogs
                ?.map(log => log.employee?.id)
                .filter(Boolean) // remove null/undefined
        )
    ];




};
const resumeDialog = ref(false)
const openResumeModal = (op) => {
    editingId.value = op.id
    resumeDialog.value = true
    //editResumeEmployee(op)
     form.team_ids = []
}


const resumeSizingOperation = async (opId) => {
    
    axios.post(route('sizingoperations.resume', { operation: opId }), { team_ids: form.team_ids })

        .then(() => {
            Swal.fire({
                icon: 'success',
                title: '作業を再開しました',
                timer: 1200,
                showConfirmButton: false,
            })

            location.reload()
        })
}


// Resume Sizing Log

const resumeSizingLog = async (logId) => {
    const result = await Swal.fire({
        title: '自分の作業を再開してもよろしいですか？',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル',
    })
    if (!result.isConfirmed) return
    
    axios.post(route('sizinglogs.resume', { log: logId }))
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: '作業を再開しました',
                timer: 1200,
                showConfirmButton: false,
            })

            location.reload()
        })
}


/*============= DELETE Sizing Log ========*/


const deleteSizingLog = async (logId) => {
    const result = await Swal.fire({
        title: '自分の作業を削除してもよろしいですか？',
        text: 'この操作は元に戻せません。',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'はい、削除します。',
        cancelButtonText: 'キャンセル',
    })
    if (!result.isConfirmed) return
    deleteForm.delete(route("sizinglogs.destroy", logId), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "削除されました！",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "削除に失敗しました",
                text: "もう一度お試しください。",
            });
        }
    });
}


/* ================= COMPLETE Sizing LOG ================= */

const completeSizingLog = async (logId) => {
    const result = await Swal.fire({
        title: 'この作業を完了してもよろしいですか？',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'はい、完了します。',
        cancelButtonText: 'キャンセル',
    })

    if (!result.isConfirmed) return

    axios.post(route('sizinglogs.complete', logId))
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: '完了しました',
                timer: 1200,
                showConfirmButton: false,
            })
            location.reload()
        })
}


</script>


<template>

    <Head title="-撚糸課" />

    <main class="p-4 sm:p-6 mx-auto min-h-screen text-xs sm:text-sm lg:text-base">

        <div class="flex flex-col items-center gap-4">

            <!-- LEFT FORM -->
            <div class="w-full lg:w-[30%] mb-8">
                <Container>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <h3 class="text-lg font-semibold mb-4 text-center">
                            撚糸課
                        </h3>
                        <div>
                            <label class="form-label">担当者</label>
                            <el-select v-model="form.team_ids" multiple placeholder="担当者を選択" class="select-uniform !p-0"
                                :disabled="isEditing">
                                <el-option v-for="member in teamMembers" :key="member.id" :label="member.name"
                                    :value="member.id" />
                            </el-select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">工場</label>
                                <select v-model="form.plant_id" class="select-uniform">
                                    <option value="">工場を選択</option>
                                    <option v-for="p in plants.data" :key="p.id" :value="p.id">
                                        {{ p.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">機台</label>
                                <select v-model="form.machine_type_id" class="select-uniform">
                                    <option value="">機台を選択</option>
                                    <option v-for="m in machinetypes" :key="m.id" :value="m.id">
                                        {{ m.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">機号</label>
                                <select v-model="form.machine_number_id" class="select-uniform">
                                    <option value="">機号</option>
                                    <option v-for="n in machinenumbers" :key="n.id" :value="n.id">
                                        {{ n.number }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">作業</label>
                                <select v-model="form.task_id" class="select-uniform">
                                    <option value="">作業を選択</option>
                                    <option v-for="t in tasks" :key="t.id" :value="t.id">
                                        {{ t.name }}
                                    </option>
                                </select>
                            </div>
                        </div>



                        <div>

                            <label class="form-label">小作業</label>
                            <select v-model="form.small_task_id" class="select-uniform">
                                <option value="">小作業を選択</option>
                                <option v-for="s in smalltasks" :key="s.id" :value="s.id">
                                    {{ s.name }}
                                </option>
                            </select>
                        </div>


                        <!-- Submit -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <!-- Submit button -->
                            <PrimaryBtn class="w-full sm:flex-1 py-2 sm:py-2.5 text-xs sm:text-sm">
                                {{ isEditing ? '更新' : 'スタート' }}
                            </PrimaryBtn>

                            <!-- Cancel button (only in edit mode) -->
                            <button v-if="isEditing" type="button" @click="cancelEdit"
                                class="w-full sm:flex-1 py-2 sm:py-2.5 text-xs sm:text-sm bg-gray-400 text-white rounded hover:bg-gray-500">
                                キャンセル
                            </button>
                        </div>


                    </form>
                </Container>
            </div>

            <!-- TABLE -->
            <div class="p-2 sm:p-4 w-full overflow-x-auto">
                <table class="w-full border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-200 text-center">
                            <th class="border p-2">日付</th>
                            <th class="border p-2">工場</th>
                            <th class="border p-2">機台</th>
                            <th class="border p-2">機号</th>
                            <th class="border p-2">作業</th>
                            <th class="border p-2">小作業</th>
                            <th class="border p-2">操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="op in sizingoperations" :key="op.id">

                            <!-- MAIN ROW -->
                            <tr class="text-center hover:bg-gray-50 cursor-pointer" @click="op.show = !op.show">
                                <td class="border p-2 flex items-center gap-2">
                                    <span class="text-lg">
                                        <span v-if="op.show">▼</span>
                                        <span v-else>►</span>
                                    </span>
                                    {{ op.created_at }}
                                </td>

                                <td class="border p-2">{{ op.plant.name }}</td>
                                <td class="border p-2">{{ op.machine_type.name }}</td>
                                <td class="border p-2">{{ op.machine_number.number }}</td>
                                <td class="border p-2">{{ op.task.name }}</td>
                                <td class="border p-2">{{ op.small_task?.name ?? '未設定' }}</td>
                                <td class="border p-2">
                                    <div class="flex items-center  gap-2">

                                        <!-- 完了 -->
                                        <button v-if="op.status === 'running' || op.status === 'paused'"
                                            @click.stop="completeSMO(op.id)"
                                            class="bg-green-500 text-white p-1 rounded hover:bg-green-600" title="完了">
                                            <el-icon>
                                                <CircleCheck />
                                            </el-icon>
                                        </button>

                                        <!-- 追加 -->
                                        <button v-if="op.status !== 'paused'" @click.stop="openAddEmployeeModal(op)"
                                            class="bg-blue-500 text-white p-1 rounded hover:bg-blue-600" title="担当者を追加">
                                            <el-icon>
                                                <Plus />
                                            </el-icon>
                                        </button>

                                        <!--Add Employee Dialog-->
                                        <el-dialog v-model="addEmployeeDialog" title="担当者を追加" width="400px">
                                            <el-select v-model="selectedEmployeeIds" multiple placeholder="担当者を選択"
                                                style="width: 100%">
                                                <el-option v-for="member in teamMembers" :key="member.id"
                                                    :label="member.name" :value="member.id" />
                                            </el-select>

                                            <template #footer>
                                                <el-button @click="addEmployeeDialog = false">
                                                    キャンセル
                                                </el-button>

                                                <el-button type="primary" @click="confirmAddEmployees">
                                                    追加
                                                </el-button>
                                            </template>
                                        </el-dialog>

                                        <!-- 止 -->
                                        <button v-if="op.status === 'running'" @click.stop="stopSizingOperation(op.id)"
                                            class="bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600" title="停止">
                                            <el-icon>
                                                <VideoPause />
                                            </el-icon>
                                        </button>

                                        <!-- 再開 -->
                                        <button v-if="op.status === 'paused'" @click.stop="openResumeModal(op)"
                                            class="bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600" title="再開">
                                            <el-icon>
                                                <VideoPlay />
                                            </el-icon>
                                        </button>
                                        <!-- Resume Employee Dialog -->
                                        <el-dialog v-model="resumeDialog" title="担当者" width="400px">
                                            <el-select v-model="form.team_ids" multiple placeholder="担当者を選択"
                                                style="width: 100%">
                                                <el-option v-for="member in teamMembers" :key="member.id"
                                                    :label="member.name" :value="member.id" />
                                            </el-select>

                                            <template #footer>
                                                <el-button @click="resumeDialog = false">
                                                    キャンセル
                                                </el-button>

                                                <el-button type="primary" @click.stop="resumeSizingOperation(op.id)">
                                                    再開
                                                </el-button>
                                            </template>
                                        </el-dialog>
                                        <!-- 編集 -->
                                        <button @click.stop="editSizingOperation(op)"
                                            class="bg-pink-500 text-white p-1 rounded hover:bg-pink-600" title="編集">
                                            <el-icon>
                                                <Edit />
                                            </el-icon>
                                        </button>

                                        <!-- 削除 -->
                                        <button v-if="op.status !== 'completed'"
                                            @click.stop="deleteSizingOperation(op.id)"
                                            class="bg-red-500 text-white p-1 rounded hover:bg-red-600" title="削除">
                                            <el-icon>
                                                <Delete />
                                            </el-icon>
                                        </button>

                                    </div>
                                </td>



                                <!-- <td>
                                    <button @click.stop="op.menu = !op.menu" class="px-2 py-1">
                                        ⋯
                                    </button>

                                    <div v-if="op.menu" @click.stop
                                        class="absolute right-0 mt-1 bg-white border rounded shadow-md w-28 z-20">
                                        <button @click="completeSMO(op.id)"
                                            class="block w-full px-3 py-2 hover:bg-gray-100">完了</button>
                                        <button @click="editSizingOperation(op)"
                                            class="block w-full px-3 py-2 hover:bg-gray-100">編集
                                        </button>
                                        <button class="block w-full px-3 py-2 hover:bg-gray-100">
                                            追加
                                        </button>
                                        <button
                                            class="block w-full px-3 py-2 text-pink-600 hover:bg-gray-100">止</button>
                                        <button class="block w-full px-3 py-2 text-red-600 hover:bg-gray-100">
                                            削除
                                        </button>
                                    </div>
                                </td> -->
                            </tr>

                            <!-- SUB TABLE -->
                            <tr v-if="op.show" class="bg-blue-50">
                                <td colspan="7" class="p-0 border">
                                    <table class="w-full text-center">
                                        <thead class="bg-blue-200">
                                            <tr>

                                                <th class="border text-sm p-1">開始</th>
                                                <th class="border text-sm p-1">終了</th>
                                                <th class="border text-sm p-1">合計時間</th>
                                                <th class="border text-sm p-1">担当者</th>
                                                <th class="border text-sm p-1">操作</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="log in op.sizinglogs" :key="log.id">

                                                <td class="border text-sm p-1">{{ log.start_time }}</td>
                                                <td class="border text-sm p-1">
                                                    {{ log.end_time ?? log.paused_time ?? '-' }}
                                                </td>

                                                <td class="border text-sm p-1">
                                                    {{ (log.end_time || log.paused_time) ? log.duration_per_employee :
                                                        '-' }}
                                                </td>


                                                <td class="border text-sm p-1">{{ log.employee.name ?? '-' }}</td>

                                                <td class="border text-sm  p-1">
                                                    <div class="flex items-center  gap-2">
                                                        <button
                                                            class="bg-green-500 text-white p-1 rounded hover:bg-green-600"
                                                            title="完了" @click="completeSizingLog(log.id)"
                                                            v-if="!log.end_time && log.last_start_time">
                                                            <el-icon>
                                                                <CircleCheck />
                                                            </el-icon>
                                                        </button>

                                                        <button
                                                            v-if="op.status === 'running' && !log.paused_time && !log.end_time"
                                                            @click.stop="stopSizingLog(log.id)"
                                                            class="bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600"
                                                            title="停止">
                                                            <el-icon>
                                                                <VideoPause />
                                                            </el-icon>
                                                        </button>

                                                        <button v-if="log.paused_time && op.status === 'running'"
                                                            @click.stop="resumeSizingLog(log.id)"
                                                            class="bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600"
                                                            title="再開">
                                                            <el-icon>
                                                                <VideoPlay />
                                                            </el-icon>
                                                        </button>
                                                        <button v-if="!log.end_time" @click="deleteSizingLog(log.id)"
                                                            class="bg-red-500 text-white p-1 rounded hover:bg-red-600"
                                                            title="削除">
                                                            <el-icon>
                                                                <Delete />
                                                            </el-icon>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</template>

<style scoped>
.select-uniform {
    @apply mt-1 block w-full border border-gray-300 rounded-md py-1 px-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-white text-gray-500 text-sm sm:text-base;
}

.form-label {
    @apply block text-xs sm:text-sm font-medium text-gray-700;
}
</style>
