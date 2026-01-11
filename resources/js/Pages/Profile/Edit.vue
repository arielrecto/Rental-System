<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-semibold text-gray-900">
                Profile Settings
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Manage your account settings and preferences
            </p>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="space-y-6">
                    <!-- Profile Information Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                        <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-white px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
                            <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address</p>
                        </div>
                        <div class="p-6">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                class="max-w-2xl"
                            />
                        </div>
                    </div>

                    <!-- Update Password Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                        <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-white px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900">Update Password</h3>
                            <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure</p>
                        </div>
                        <div class="p-6">
                            <UpdatePasswordForm class="max-w-2xl" />
                        </div>
                    </div>

                    <!-- Delete Account Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-red-200 overflow-hidden">
                        <div class="border-b border-red-200 bg-gradient-to-r from-red-50 to-white px-6 py-4">
                            <h3 class="text-lg font-semibold text-red-900">Delete Account</h3>
                            <p class="mt-1 text-sm text-red-600">Permanently delete your account and all associated data</p>
                        </div>
                        <div class="p-6">
                            <DeleteUserForm class="max-w-2xl" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Red theme accent styles */
:deep(.primary-button) {
    @apply bg-red-600 hover:bg-red-700 focus:ring-red-500 transition-colors duration-200;
}

:deep(.text-input) {
    @apply focus:border-red-500 focus:ring-red-500 border-gray-300 rounded-md shadow-sm;
}

:deep(.danger-button) {
    @apply bg-red-600 hover:bg-red-700 focus:ring-red-500 transition-colors duration-200;
}

:deep(.secondary-button) {
    @apply border-gray-300 hover:bg-gray-50 focus:ring-red-500 transition-colors duration-200;
}
</style>
