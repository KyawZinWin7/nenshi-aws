<script setup>
import Container from '../../Components/Container.vue';
import TextLink from '../../Components/TextLink.vue';
import Title from '../../Components/Title.vue';
import InputField from '../../Components/InputField.vue';
import PrimaryBtn from '../../Components/PrimaryBtn.vue';
import ErrorMessages from '../../Components/ErrorMessages.vue';
import CheckBox from '../../Components/CheckBox.vue';
import { useForm } from '@inertiajs/vue3';
import Footer from '../../Footer.vue';

const form = useForm({
    email: "",
    password: "",
    remember: null,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
}
</script>

<template>
    <Head title="- ログイン" />

    <main class="mx-auto p-4 sm:p-6 max-w-screen-lg min-h-screen flex items-center justify-center">
        <!-- responsive width -->
        <Container class="w-full sm:w-2/3 md:w-1/2">
            <div class="mb-8 text-center">
                <!-- responsive font size -->
                <Title class="text-xl sm:text-2xl md:text-3xl">アカウントにログイン</Title>
            </div>

            <!--Errors messages-->
            <ErrorMessages :errors="form.errors" />

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Input fields -->
                <InputField label="メールアドレス" icon="at" v-model="form.email" 
                    class="text-sm md:text-base" />
                <InputField label="パスワード" type="password" icon="key" v-model="form.password"
                    class="text-sm md:text-base" />

                <!-- remember / forgot password (အချိန်မရှိတာကြောင့် comment ထားခဲ့တာ) -->
                <!--
                <div class="flex items-center justify-between">
                    <CheckBox name="remember" v-model="form.remember" class="text-sm md:text-base">
                        Remember
                    </CheckBox>
                    <TextLink routeName="home" label="Forgot Password?" class="text-sm md:text-base"/>
                </div>
                -->

                <PrimaryBtn :disabled="form.processing" class="w-full py-2 text-sm md:text-base">
                    ログイン
                </PrimaryBtn>
            </form>
        </Container>
    </main>
    <Footer/>
</template>
