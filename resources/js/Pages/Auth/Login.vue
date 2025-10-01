<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800">Welcome Back!</h2>
            <p class="text-gray-600 mt-2">Please sign in to your account</p>
        </div>

        <div v-if="status" class="mb-4 p-4 rounded-lg bg-green-50 text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" class="text-gray-700" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-gray-700" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-lg"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 hover:text-red-600 transition duration-300"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-3 bg-red-600 hover:bg-red-700 focus:ring-red-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Sign In
                </PrimaryButton>
            </div>

            <div class="text-center mt-6">
                <span class="text-gray-600 text-sm">Don't have an account?</span>
                <Link
                    :href="route('register')"
                    class="text-sm text-red-600 hover:text-red-700 ml-2 font-semibold"
                >
                    Sign up now
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
:deep(.primary-button) {
    @apply bg-red-600 hover:bg-red-700 transition-colors duration-300 rounded-lg shadow-sm;
}

:deep(.text-input) {
    @apply focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm;
}

:deep(.checkbox) {
    @apply focus:ring-red-500 text-red-600 border-gray-300 rounded transition-colors duration-300;
}
</style>
