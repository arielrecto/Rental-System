<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { XMarkIcon, MagnifyingGlassIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

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
    is_active: props.account.is_active,
    qr_code: null,
    remove_qr: false
});

const errors = ref({});
const qrPreview = ref(null);
const imagePreviewModal = ref(false);
const previewImage = ref(null);

const providers = [
    { id: 'gcash', name: 'GCash' },
    { id: 'maya', name: 'Maya' },
    { id: 'bank', name: 'Bank Transfer' }
];

const handleQrUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.qr_code = file;
        form.value.remove_qr = false;
        qrPreview.value = URL.createObjectURL(file);
    }
};

const removeNewQr = () => {
    form.value.qr_code = null;
    qrPreview.value = null;
    const fileInput = document.getElementById('qr-upload');
    if (fileInput) fileInput.value = '';
};

const removeExistingQr = () => {
    form.value.remove_qr = true;
};

const restoreExistingQr = () => {
    form.value.remove_qr = false;
};

const openImagePreview = (imagePath, imageName) => {
    previewImage.value = {
        path: imagePath,
        name: imageName
    };
    imagePreviewModal.value = true;
};

const submit = () => {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('account_number', form.value.account_number);
    formData.append('account_name', form.value.account_name);
    formData.append('provider', form.value.provider);
    formData.append('descriptions', form.value.descriptions || '');
    formData.append('is_active', form.value.is_active ? '1' : '0');
    
    if (form.value.qr_code) {
        formData.append('qr_code', form.value.qr_code);
    }
    
    if (form.value.remove_qr) {
        formData.append('remove_qr', '1');
    }

    router.post(route('internal.payment-accounts.update', props.account.id), formData, {
        forceFormData: true,
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
                                        Update Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- QR Code Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                QR Code
                            </h2>

                            <!-- New QR Upload -->
                            <div v-if="!qrPreview" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload New QR Code
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
                            </div>

                            <!-- New QR Preview -->
                            <div v-if="qrPreview" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    New QR Code (Not Saved Yet)
                                </label>
                                <div class="border-2 border-green-300 rounded-lg overflow-hidden relative group bg-white p-4">
                                    <img :src="qrPreview" alt="New QR Code" class="w-full h-auto object-contain">
                                    <button
                                        type="button"
                                        @click="removeNewQr"
                                        class="absolute top-2 right-2 p-2 bg-red-600 text-white rounded-full hover:bg-red-700 shadow-lg"
                                    >
                                        <XMarkIcon class="h-4 w-4" />
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-green-600 font-medium">✓ New QR code selected. Save to update.</p>
                            </div>

                            <!-- Existing QR Code -->
                            <div v-if="account.qr_code && !form.remove_qr">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Current QR Code
                                </label>
                                <div class="border-2 border-gray-200 rounded-lg overflow-hidden relative group bg-white p-4">
                                    <div class="relative">
                                        <img
                                            :src="account.qr_code.file_path"
                                            :alt="account.qr_code.file_name"
                                            class="w-full h-auto object-contain cursor-pointer"
                                            @click="openImagePreview(account.qr_code.file_path, account.qr_code.file_name)"
                                        />
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center cursor-pointer"
                                             @click="openImagePreview(account.qr_code.file_path, account.qr_code.file_name)">
                                            <MagnifyingGlassIcon class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-3 mt-2 rounded-lg">
                                    <p class="text-xs text-gray-600 truncate" :title="account.qr_code.file_name">
                                        {{ account.qr_code.file_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ (account.qr_code.file_size / 1024).toFixed(2) }} KB
                                    </p>
                                </div>

                                <div class="flex flex-col space-y-2 mt-3">
                                    <button
                                        type="button"
                                        @click="openImagePreview(account.qr_code.file_path, account.qr_code.file_name)"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                                        View Full Size
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeExistingQr"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                                    >
                                        <XMarkIcon class="h-5 w-5 mr-2" />
                                        Remove QR Code
                                    </button>
                                </div>
                            </div>

                            <!-- Removed QR Message -->
                            <div v-if="form.remove_qr" class="border-2 border-dashed border-red-300 rounded-lg p-4 bg-red-50">
                                <p class="text-sm text-red-600 font-medium mb-2">✗ QR code will be removed</p>
                                <button
                                    type="button"
                                    @click="restoreExistingQr"
                                    class="w-full px-3 py-2 text-sm bg-white border border-red-300 rounded-md text-red-700 hover:bg-red-50"
                                >
                                    Undo Remove
                                </button>
                            </div>

                            <!-- No QR Message -->
                            <div v-if="!account.qr_code && !qrPreview" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">No QR code uploaded</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <Modal v-model="imagePreviewModal" max-width="5xl">
            <div class="p-4 bg-gray-900" v-if="previewImage">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-white truncate mr-4">{{ previewImage.name }}</h3>
                    <button
                        @click="imagePreviewModal = false"
                        class="text-gray-400 hover:text-white flex-shrink-0"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex justify-center items-center bg-black rounded-lg overflow-hidden" style="max-height: 75vh;">
                    <img
                        :src="previewImage.path"
                        :alt="previewImage.name"
                        class="max-w-full max-h-full object-contain"
                    />
                </div>
                <div class="mt-4 flex justify-center">
                    <a
                        :href="previewImage.path"
                        download
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                    >
                        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                        Download QR Code
                    </a>
                </div>
            </div>
        </Modal>
    </InternalLayout>
</template>
