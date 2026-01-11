<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const form = ref({
    account_number: '',
    account_name: '',
    provider: '',
    descriptions: '',
    is_active: true,
    qr_code: null
});

const errors = ref({});
const qrPreview = ref(null);

const providers = [
    { id: 'gcash', name: 'GCash' },
    { id: 'maya', name: 'Maya' },
    { id: 'bank', name: 'Bank Transfer' }
];

const handleQrUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.qr_code = file;
        qrPreview.value = URL.createObjectURL(file);
    }
};

const removeQr = () => {
    form.value.qr_code = null;
    qrPreview.value = null;
    const fileInput = document.getElementById('qr-upload');
    if (fileInput) fileInput.value = '';
};

const submit = () => {
    const formData = new FormData();
    formData.append('account_number', form.value.account_number);
    formData.append('account_name', form.value.account_name);
    formData.append('provider', form.value.provider);
    formData.append('descriptions', form.value.descriptions || '');
    formData.append('is_active', form.value.is_active ? '1' : '0');
    
    if (form.value.qr_code) {
        formData.append('qr_code', form.value.qr_code);
    }

    router.post(route('internal.payment-accounts.store'), formData, {
        forceFormData: true,
        onSuccess: () => {
            form.value = {
                account_number: '',
                account_name: '',
                provider: '',
                descriptions: '',
                is_active: true,
                qr_code: null
            };
            qrPreview.value = null;
        },
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Create Payment Account" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Create Payment Account</h1>
                    <p class="mt-1 text-sm text-gray-600">Add a new payment account to the system</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow rounded-lg">
                            <form @submit.prevent="submit" class="space-y-6 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Account Number -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Account Number <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.account_number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                            required
                                        >
                                        <p v-if="errors.account_number" class="mt-1 text-sm text-red-600">
                                            {{ errors.account_number }}
                                        </p>
                                    </div>

                                    <!-- Account Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Account Name <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.account_name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                            required
                                        >
                                        <p v-if="errors.account_name" class="mt-1 text-sm text-red-600">
                                            {{ errors.account_name }}
                                        </p>
                                    </div>

                                    <!-- Provider -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Provider <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.provider"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                            required
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
                                            placeholder="Additional information about this payment account"
                                        ></textarea>
                                        <p v-if="errors.descriptions" class="mt-1 text-sm text-red-600">
                                            {{ errors.descriptions }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="flex justify-end space-x-3 pt-4 border-t">
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
                                        Create Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- QR Code Upload Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                QR Code
                            </h2>

                            <!-- Upload Area -->
                            <div v-if="!qrPreview">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload QR Code
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-500 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="qr-upload" class="relative cursor-pointer rounded-md font-medium text-red-600 hover:text-red-500">
                                                <span>Upload QR code</span>
                                                <input
                                                    id="qr-upload"
                                                    type="file"
                                                    class="sr-only"
                                                    accept="image/*"
                                                    @change="handleQrUpload"
                                                >
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                </div>
                                <p v-if="errors.qr_code" class="mt-1 text-sm text-red-600">
                                    {{ errors.qr_code }}
                                </p>
                                <p class="mt-2 text-xs text-gray-500">
                                    <span class="font-medium">Note:</span> QR code will be displayed to customers during payment
                                </p>
                            </div>

                            <!-- QR Preview -->
                            <div v-if="qrPreview">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    QR Code Preview
                                </label>
                                <div class="border-2 border-green-300 rounded-lg overflow-hidden relative group bg-white p-4">
                                    <img :src="qrPreview" alt="QR Code Preview" class="w-full h-auto object-contain">
                                    <button
                                        type="button"
                                        @click="removeQr"
                                        class="absolute top-2 right-2 p-2 bg-red-600 text-white rounded-full hover:bg-red-700 shadow-lg"
                                    >
                                        <XMarkIcon class="h-4 w-4" />
                                    </button>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-gray-600 bg-green-50 p-2 rounded">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        QR Code Ready
                                    </span>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">
                                            About QR Codes
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>Make sure QR code is clear and scannable</li>
                                                <li>Customers will scan this during payment</li>
                                                <li>Recommended size: 500x500 pixels minimum</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
