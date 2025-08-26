<template>
  <el-dialog v-model="visible" title="作業完了確認" width="30%" @close="reset">
    <span>担当者コードを入力してください：</span>
    <el-input v-model="codeInput" placeholder="担当者コード" />
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="visible = false">キャンセル</el-button>
        <el-button type="primary" @click="submit">完了</el-button>
      </span>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  moId: Number,
  employeeCode: String,
  modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const visible = ref(props.modelValue);
watch(() => props.modelValue, (val) => (visible.value = val));
watch(visible, (val) => emit('update:modelValue', val));

const codeInput = ref('');

const reset = () => {
  codeInput.value = '';
};

const submit = () => {
  if (codeInput.value.trim() === '') {
    ElMessage.warning('コードを入力してください。');
    return;
  }

  if (codeInput.value === props.employeeCode) {
    router.post(route('mainoperations.complete', props.moId));
    visible.value = false;
  } else {
    ElMessage.error('コードが一致しません。');
  }
};
</script>
