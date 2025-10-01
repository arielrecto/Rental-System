<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800">Create Account</h2>
            <p class="text-gray-600 mt-2">Join Anaia's Motorcycle Rental today</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="name" value="Full Name" class="text-gray-700" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-lg"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email Address" class="text-gray-700" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="text-gray-700"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full rounded-lg"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-3 bg-red-600 hover:bg-red-700 focus:ring-red-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Create Account
                </PrimaryButton>
            </div>

            <div class="text-center mt-6">
                <span class="text-gray-600 text-sm">Already have an account?</span>
                <Link
                    :href="route('login')"
                    class="text-sm text-red-600 hover:text-red-700 ml-2 font-semibold"
                >
                    Sign in here
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
</style>
