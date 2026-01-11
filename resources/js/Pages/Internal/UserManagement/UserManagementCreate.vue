<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    pin: '',
    pin_confirmation: '',
    // Profile fields
    first_name: '',
    last_name: '',
    phone_number: '',
    address: '',
    gender: '',
    birth_date: ''
});

const errors = ref({});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' }
];

const submit = () => {
    router.post(route('internal.user-management.store'), form.value, {
        onSuccess: () => {
            form.value = {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                pin: '',
                pin_confirmation: '',
                first_name: '',
                last_name: '',
                phone_number: '',
                address: '',
                gender: '',
                birth_date: ''
            };
        },
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Create User" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Create User</h1>
                    <p class="mt-1 text-sm text-gray-600">Add a new internal user with profile information</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Account Information Section -->
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Account Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                    <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                        {{ errors.name }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                    <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                        {{ errors.email }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                    <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                                        {{ errors.password }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Confirm Password <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        PIN (4-6 digits) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.pin"
                                        maxlength="6"
                                        pattern="[0-9]{4,6}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Enter 4-6 digit PIN"
                                        required
                                    >
                                    <div v-if="errors.pin" class="mt-1 text-sm text-red-600">
                                        {{ errors.pin }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Confirm PIN <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.pin_confirmation"
                                        maxlength="6"
                                        pattern="[0-9]{4,6}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Confirm PIN"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Profile Information Section -->
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Profile Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        First Name
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.first_name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                                        {{ errors.first_name }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Last Name
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.last_name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                                        {{ errors.last_name }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Phone Number
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.phone_number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="+63 XXX XXX XXXX"
                                    >
                                    <div v-if="errors.phone_number" class="mt-1 text-sm text-red-600">
                                        {{ errors.phone_number }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Gender
                                    </label>
                                    <select
                                        v-model="form.gender"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                        <option value="">Select Gender</option>
                                        <option v-for="option in genderOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </option>
                                    </select>
                                    <div v-if="errors.gender" class="mt-1 text-sm text-red-600">
                                        {{ errors.gender }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Birth Date
                                    </label>
                                    <input
                                        type="date"
                                        v-model="form.birth_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        :max="new Date().toISOString().split('T')[0]"
                                    >
                                    <div v-if="errors.birth_date" class="mt-1 text-sm text-red-600">
                                        {{ errors.birth_date }}
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Address
                                    </label>
                                    <textarea
                                        v-model="form.address"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Complete address"
                                    ></textarea>
                                    <div v-if="errors.address" class="mt-1 text-sm text-red-600">
                                        {{ errors.address }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button
                                type="button"
                                @click="router.visit(route('internal.user-management.index'))"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                            >
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
