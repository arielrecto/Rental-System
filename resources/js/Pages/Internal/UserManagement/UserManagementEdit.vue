<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const form = ref({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    phone: props.user.profile?.phone || '',
    address: props.user.profile?.address || ''
});

const errors = ref({});

const submit = () => {
    router.put(route('internal.user-management.update', props.user.id), form.value, {
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Edit User" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit User</h1>
                    <p class="mt-1 text-sm text-gray-600">Update user information</p>
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
                                <p class="text-sm text-gray-600">Leave password fields empty to keep the current password</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">New Password</label>
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
                                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
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
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
