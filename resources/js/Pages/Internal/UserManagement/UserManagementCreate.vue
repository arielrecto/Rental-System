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
    phone: '',
    address: ''
});

const errors = ref({});

const submit = () => {
    router.post(route('internal.users.store'), form.value, {
        onSuccess: () => {
            form.value = {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                phone: '',
                address: ''
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
                    <p class="mt-1 text-sm text-gray-600">Add a new internal user</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                        {{ errors.name }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                        {{ errors.email }}
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input
                                        type="text"
                                        v-model="form.phone"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.phone" class="mt-1 text-sm text-red-600">
                                        {{ errors.phone }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea
                                        v-model="form.address"
                                        rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    ></textarea>
                                    <div v-if="errors.address" class="mt-1 text-sm text-red-600">
                                        {{ errors.address }}
                                    </div>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="space-y-4 md:col-span-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Password</label>
                                    <input
                                        type="password"
                                        v-model="form.password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                                        {{ errors.password }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input
                                        type="password"
                                        v-model="form.password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="$router.go(-1)"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
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
