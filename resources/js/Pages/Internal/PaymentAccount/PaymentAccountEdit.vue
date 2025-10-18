<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    account: {
        type: Object,
        required: true
    }
});

const form = ref({
    account_number: props.account.account_number,
    account_name: props.account.account_name,
    provider: props.account.provider,
    descriptions: props.account.descriptions,
    is_active: props.account.is_active
});

const errors = ref({});

const providers = [
    { id: 'gcash', name: 'GCash' },
    { id: 'maya', name: 'Maya' },
    { id: 'bank', name: 'Bank Transfer' }
];

const submit = () => {
    router.put(route('internal.payment-accounts.update', props.account.id), form.value, {
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Edit Payment Account" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Payment Account</h1>
                    <p class="mt-1 text-sm text-gray-600">Update payment account information</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Form fields same as create page -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Account Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Account Number
                                </label>
                                <input
                                    type="text"
                                    v-model="form.account_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                <p v-if="errors.account_number" class="mt-1 text-sm text-red-600">
                                    {{ errors.account_number }}
                                </p>
                            </div>

                            <!-- Account Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Account Name
                                </label>
                                <input
                                    type="text"
                                    v-model="form.account_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                <p v-if="errors.account_name" class="mt-1 text-sm text-red-600">
                                    {{ errors.account_name }}
                                </p>
                            </div>

                            <!-- Provider -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Provider
                                </label>
                                <select
                                    v-model="form.provider"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option value="">Select Provider</option>
                                    <option v-for="provider in providers"
                                        :key="provider.id"
                                        :value="provider.id"
                                    >
                                        {{ provider.name }}
                                    </option>
                                </select>
                                <p v-if="errors.provider" class="mt-1 text-sm text-red-600">
                                    {{ errors.provider }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.is_active"
                                            class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-600">Active</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.descriptions"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                ></textarea>
                                <p v-if="errors.descriptions" class="mt-1 text-sm text-red-600">
                                    {{ errors.descriptions }}
                                </p>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <Link
                                :href="route('internal.payment-accounts.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                Update Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
