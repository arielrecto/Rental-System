<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, TableCellsIcon, Squares2X2Icon, QrCodeIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    accounts: {
        type: Array,
        required: true
    }
});

const viewMode = ref('list'); // 'list' or 'grid'
const deleteModal = ref(false);
const accountToDelete = ref(null);
const qrPreviewModal = ref(false);
const previewQrCode = ref(null);

const headers = [
    { key: 'account_number', label: 'Account Number' },
    { key: 'account_name', label: 'Account Name' },
    { key: 'provider', label: 'Provider' },
    { key: 'qr_code', label: 'QR Code' },
    { key: 'is_active', label: 'Status' },
    { key: 'actions', label: 'Actions' }
];

const confirmDelete = (account) => {
    accountToDelete.value = account;
    deleteModal.value = true;
};

const handleDelete = () => {
    router.delete(route('internal.payment-accounts.destroy', accountToDelete.value.id), {
        onSuccess: () => {
            deleteModal.value = false;
            accountToDelete.value = null;
        }
    });
};

const openQrPreview = (qrCode) => {
    previewQrCode.value = qrCode;
    qrPreviewModal.value = true;
};

const getProviderIcon = (provider) => {
    const icons = {
        'gcash': '/icons/gcash.png',
        'maya': '/icons/maya.png',
        'bank': '/icons/bank.png',
    };
    return icons[provider.toLowerCase()] || '/icons/default.png';
};

const getProviderDisplay = (provider) => {
    const displays = {
        'gcash': 'GCash',
        'maya': 'Maya',
        'bank': 'Bank Transfer',
    };
    return displays[provider.toLowerCase()] || provider;
};
</script>

<template>
    <InternalLayout>
        <Head title="Payment Accounts" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Payment Accounts</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage payment accounts and providers</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- View Toggle -->
                        <div class="flex items-center bg-white rounded-lg shadow p-1">
                            <button
                                @click="viewMode = 'list'"
                                :class="[
                                    'p-2 rounded-md',
                                    viewMode === 'list'
                                        ? 'bg-red-50 text-red-600'
                                        : 'text-gray-400 hover:text-gray-500'
                                ]"
                            >
                                <TableCellsIcon class="h-5 w-5" />
                            </button>
                            <button
                                @click="viewMode = 'grid'"
                                :class="[
                                    'p-2 rounded-md',
                                    viewMode === 'grid'
                                        ? 'bg-red-50 text-red-600'
                                        : 'text-gray-400 hover:text-gray-500'
                                ]"
                            >
                                <Squares2X2Icon class="h-5 w-5" />
                            </button>
                        </div>

                        <Link
                            :href="route('internal.payment-accounts.create')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Account
                        </Link>
                    </div>
                </div>

                <!-- List View -->
                <div v-if="viewMode === 'list'" class="bg-white rounded-lg shadow">
                    <DataTable
                        :headers="headers"
                        :items="accounts"
                    >
                        <template #default="{ item }">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.account_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.account_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        {{ getProviderDisplay(item.provider) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="item.qr_code" class="flex items-center space-x-2">
                                        <div class="relative group">
                                            <img 
                                                :src="item.qr_code.file_path" 
                                                :alt="item.qr_code.file_name"
                                                class="h-12 w-12 object-contain border border-gray-200 rounded cursor-pointer hover:border-red-300 transition-colors"
                                                @click="openQrPreview(item.qr_code)"
                                            >
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded flex items-center justify-center cursor-pointer transition-all"
                                                 @click="openQrPreview(item.qr_code)">
                                                <MagnifyingGlassIcon class="h-5 w-5 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                            </div>
                                        </div>
                                        <button
                                            @click="openQrPreview(item.qr_code)"
                                            class="text-xs text-red-600 hover:text-red-700"
                                        >
                                            View
                                        </button>
                                    </div>
                                    <span v-else class="text-sm text-gray-400 italic">No QR Code</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-xs font-semibold',
                                        item.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]">
                                        {{ item.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <Link
                                            :href="route('internal.payment-accounts.edit', item.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="confirmDelete(item)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </DataTable>
                </div>

                <!-- Grid View -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="account in accounts"
                        :key="account.id"
                        class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ account.account_name }}
                                            </h3>
                                            <p class="text-sm text-gray-500">{{ getProviderDisplay(account.provider) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-semibold',
                                    account.is_active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                ]">
                                    {{ account.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">
                                    <span class="font-medium">Account Number:</span>
                                    {{ account.account_number }}
                                </p>
                                <p v-if="account.descriptions" class="text-sm text-gray-500 line-clamp-2">
                                    {{ account.descriptions }}
                                </p>
                            </div>

                            <!-- QR Code Preview Card -->
                            <div v-if="account.qr_code" class="mb-4 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <QrCodeIcon class="h-4 w-4 mr-1" />
                                        <span class="font-medium">QR Code</span>
                                    </div>
                                </div>
                                <div class="relative group">
                                    <img 
                                        :src="account.qr_code.file_path" 
                                        :alt="account.qr_code.file_name"
                                        class="w-full h-32 object-contain bg-white rounded border border-gray-200 cursor-pointer hover:border-red-300 transition-colors"
                                        @click="openQrPreview(account.qr_code)"
                                    >
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded flex items-center justify-center cursor-pointer transition-all"
                                         @click="openQrPreview(account.qr_code)">
                                        <MagnifyingGlassIcon class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </div>
                                </div>
                                <button
                                    @click="openQrPreview(account.qr_code)"
                                    class="mt-2 w-full text-center text-xs text-red-600 hover:text-red-700 font-medium"
                                >
                                    Click to view full size
                                </button>
                            </div>
                            <div v-else class="mb-4 bg-gray-50 rounded-lg p-4 border border-dashed border-gray-300 text-center">
                                <QrCodeIcon class="h-8 w-8 text-gray-400 mx-auto mb-1" />
                                <p class="text-xs text-gray-500">No QR Code uploaded</p>
                            </div>

                            <div class="flex justify-end space-x-3 pt-3 border-t border-gray-200">
                                <Link
                                    :href="route('internal.payment-accounts.edit', account.id)"
                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-700 font-medium"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="confirmDelete(account)"
                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-700 font-medium"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="accounts.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
                    <QrCodeIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payment accounts</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new payment account.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('internal.payment-accounts.create')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Account
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal v-model="deleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Delete Payment Account
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete this payment account? This action cannot be undone.
                </p>
                <div class="mt-4 flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="deleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="handleDelete"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>

        <!-- QR Code Preview Modal -->
        <Modal v-model="qrPreviewModal" max-width="3xl">
            <div class="p-6 bg-white" v-if="previewQrCode">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">QR Code Preview</h3>
                    <button
                        @click="qrPreviewModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-8 flex justify-center items-center">
                    <img
                        :src="previewQrCode.file_path"
                        :alt="previewQrCode.file_name"
                        class="max-w-full max-h-96 object-contain"
                    />
                </div>

                <div class="mt-4 bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">File Name:</span> {{ previewQrCode.file_name }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-medium">File Size:</span> {{ (previewQrCode.file_size / 1024).toFixed(2) }} KB
                    </p>
                </div>

                <div class="mt-4 flex justify-end space-x-3">
                    <a
                        :href="previewQrCode.file_path"
                        download
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download QR Code
                    </a>
                </div>
            </div>
        </Modal>
    </InternalLayout>
</template>
