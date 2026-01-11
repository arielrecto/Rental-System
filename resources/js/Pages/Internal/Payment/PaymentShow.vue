<script setup>
import { ref } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import {
    BanknotesIcon,
    CalendarIcon,
    UserIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    MagnifyingGlassIcon,
    ArrowDownTrayIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    payment: {
        type: Object,
        required: true
    }
});

const imagePreviewModal = ref(false);
const previewImage = ref(null);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusColor = (status) => {
    return {
        'pending': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
        'refunded': 'bg-gray-100 text-gray-800'
    }[status] || 'bg-gray-100 text-gray-800';
};

const getStatusIcon = (status) => {
    switch(status) {
        case 'completed':
            return CheckCircleIcon;
        case 'failed':
            return XCircleIcon;
        case 'pending':
            return ClockIcon;
        default:
            return ClockIcon;
    }
};

const openImagePreview = (imagePath, imageName) => {
    previewImage.value = {
        path: imagePath,
        name: imageName
    };
    imagePreviewModal.value = true;
};
</script>

<template>
    <InternalLayout>
        <Head :title="`Payment - ${payment.ref_number}`" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Payment Details</h1>
                        <p class="mt-1 text-sm text-gray-600">{{ payment.ref_number }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('internal.payments.index')"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-red-300 focus:ring focus:ring-red-200"
                        >
                            Back to Payments
                        </Link>
                        <Link
                            v-if="payment.status === 'pending'"
                            :href="route('internal.payments.edit', payment.id)"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200"
                        >
                            Edit Payment
                        </Link>
                    </div>
                </div>

                <!-- Payment Status Header Card -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center">
                                    <BanknotesIcon class="h-8 w-8 text-red-600" />
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">{{ formatCurrency(payment.total_amount) }}</h2>
                                <p class="text-sm text-gray-600 mt-1">Total Payment Amount</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span :class="[
                                getStatusColor(payment.status),
                                'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold'
                            ]">
                                <component :is="getStatusIcon(payment.status)" class="h-5 w-5 mr-2" />
                                {{ payment.status.toUpperCase() }}
                            </span>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ formatDate(payment.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Payment Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Payment Account Information -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Payment Method
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <BanknotesIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Provider</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.payment_account.provider }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Account Name</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.payment_account.account_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Account Number</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.payment_account.account_number }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payable Information -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Payment For
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Type</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.payable?.type || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Reference Number</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.payable?.ref_number || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div v-if="payment.payable?.customer" class="flex items-start">
                                    <UserIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Customer</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.payable.customer }}</p>
                                    </div>
                                </div>
                                <div v-if="payment.payable?.amount" class="flex items-start">
                                    <BanknotesIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Order Amount</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ formatCurrency(payment.payable.amount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Paid By
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <UserIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Name</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ payment.paid_by.name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Memo -->
                        <div v-if="payment.memo" class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Memo / Notes
                            </h2>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ payment.memo }}</p>
                        </div>
                    </div>

                    <!-- Right Column - Proof of Payment -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Proof of Payment
                            </h2>
                            
                            <div v-if="payment.proof_of_payment" class="space-y-4">
                                <div class="border-2 border-gray-200 rounded-lg overflow-hidden relative group">
                                    <div class="relative">
                                        <img
                                            :src="payment.proof_of_payment.file_path"
                                            :alt="payment.proof_of_payment.file_name"
                                            class="w-full h-64 object-contain cursor-pointer bg-gray-50"
                                            @click="openImagePreview(payment.proof_of_payment.file_path, payment.proof_of_payment.file_name)"
                                        />
                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center cursor-pointer"
                                             @click="openImagePreview(payment.proof_of_payment.file_path, payment.proof_of_payment.file_name)">
                                            <MagnifyingGlassIcon class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
                                        </div>
                                    </div>
                                </div>

                                <!-- File Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-xs font-medium text-gray-900 truncate mb-2" :title="payment.proof_of_payment.file_name">
                                        {{ payment.proof_of_payment.file_name }}
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>{{ (payment.proof_of_payment.file_size / 1024).toFixed(2) }} KB</span>
                                        <span>{{ payment.proof_of_payment.file_type }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-2">
                                    <button
                                        type="button"
                                        @click="openImagePreview(payment.proof_of_payment.file_path, payment.proof_of_payment.file_name)"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                                        View Full Size
                                    </button>
                                    <a
                                        :href="payment.proof_of_payment.file_path"
                                        download
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                                    >
                                        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                                        Download
                                    </a>
                                </div>
                            </div>

                            <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center bg-gray-50">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">No proof of payment uploaded</p>
                            </div>
                        </div>

                        <!-- Payment Timeline -->
                        <div class="bg-white shadow rounded-lg p-6 mt-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Timeline
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <CalendarIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Created</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ formatDate(payment.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <CalendarIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ formatDate(payment.updated_at) }}</p>
                                    </div>
                                </div>
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
                        Download Image
                    </a>
                </div>
            </div>
        </Modal>
    </InternalLayout>
</template>