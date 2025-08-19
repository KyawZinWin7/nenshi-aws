<script setup>
import Container from '../../Components/Container.vue';
import TextLink from '../../Components/TextLink.vue';
import Title from '../../Components/Title.vue';
import InputField from '../../Components/InputField.vue';
import PrimaryBtn from '../../Components/PrimaryBtn.vue';
import ErrorMessages from '../../Components/ErrorMessages.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name:"",
    email:"",
    password:"",
    password_confirmation:"",
});

const submit = () =>{
    form.post(route("register"),{
        onFinish:()=> form.reset("password","password_confirmation"),
    });
}
</script>

<template>
    <Head title="- Register"/>
    <main class="mx-auto p-4 sm:p-6 max-w-screen-lg min-h-screen flex items-center justify-center">
        <Container class="w-full sm:w-2/3 md:w-1/2">
            <div class="mb-8 text-center">
                <Title class="text-xl sm:text-2xl md:text-3xl">新規アカウント登録</Title>
                <p class="mt-2 text-sm md:text-base text-slate-600">
                    すでにアカウントをお持ちですか？ 
                    <TextLink routeName="login" label="ログイン"/>
                </p>
            </div>

            <!--Errors messages-->
            <ErrorMessages :errors="form.errors"/>

            <form @submit.prevent="submit" class="space-y-6">
                <InputField label="名前" icon="id-badge" v-model="form.name"/>
                <InputField label="メールアドレス" icon="at" v-model="form.email"/>
                <InputField label="パスワード" type="password" icon="key" v-model="form.password"/>
                <InputField label="パスワード（確認用）" type="password" icon="key" v-model="form.password_confirmation"/>

                <p class="text-slate-500 text-xs sm:text-sm dark:text-slate-400 leading-relaxed">
                    アカウントを作成すると、利用規約とプライバシーポリシーに同意したものとみなされます。
                </p>

                <PrimaryBtn :disabled="form.processing" class="w-full py-2 text-sm md:text-base">
                    登録
                </PrimaryBtn>
            </form>
        </Container>
    </main>
</template>
