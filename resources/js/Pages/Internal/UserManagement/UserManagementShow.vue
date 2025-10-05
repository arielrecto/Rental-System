<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    CalendarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <InternalLayout>
        <Head :title="`User - ${user.name}`" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">User Profile</h1>
                        <p class="mt-1 text-sm text-gray-600">View user information</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('internal.user-management.edit', user.id)"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-red-300 focus:ring focus:ring-red-200 active:text-gray-800 active:bg-gray-50"
                        >
                            Edit User
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <UserIcon class="h-5 w-5 text-gray-400 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Name</p>
                                    <p class="text-sm text-gray-600">{{ user.name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Email</p>
                                    <p class="text-sm text-gray-600">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h2>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <PhoneIcon class="h-5 w-5 text-gray-400 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Phone</p>
                                    <p class="text-sm text-gray-600">{{ user.profile?.phone || 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <MapPinIcon class="h-5 w-5 text-gray-400 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Address</p>
                                    <p class="text-sm text-gray-600">{{ user.profile?.address || 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Details Card -->
                    <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Account Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center">
                                <CalendarIcon class="h-5 w-5 text-gray-400 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Member Since</p>
                                    <p class="text-sm text-gray-600">{{ formatDate(user.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="rounded-full bg-green-100 p-2 mr-3">
                                    <div class="h-2 w-2 rounded-full bg-green-600"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Account Status</p>
                                    <p class="text-sm text-green-600">Active</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
