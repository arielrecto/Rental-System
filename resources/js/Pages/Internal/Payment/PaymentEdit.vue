<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { MagnifyingGlassIcon, ArrowDownTrayIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    payment: {
        type: Object,
        required: true
    },
    paymentAccounts: {
        type: Array,
        required: true
    }
});

const form = ref({
    payment_account_id: props.payment.payment_account.id,
    ref_number: props.payment.ref_number,
    total_amount: props.payment.total_amount,
    memo: props.payment.memo,
    status: props.payment.status,
    proof_of_payment: null,
    remove_proof: false
});

const errors = ref({});
const imagePreviewModal = ref(false);
const previewImage = ref(null);
const newProofPreview = ref(null);

const statuses = [
    { id: 'pending', name: 'Pending' },
    { id: 'completed', name: 'Completed' },
    { id: 'failed', name: 'Failed' },
    { id: 'refunded', name: 'Refunded' }
];

const openImagePreview = (imagePath, imageName) => {
    previewImage.value = {
        path: imagePath,
        name: imageName
    };
    imagePreviewModal.value = true;
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.proof_of_payment = file;
        form.value.remove_proof = false;
        // Create preview URL
        newProofPreview.value = URL.createObjectURL(file);
    }
};

const removeNewProof = () => {
    form.value.proof_of_payment = null;
    newProofPreview.value = null;
    // Reset file input
    const fileInput = document.getElementById('proof-upload');
    if (fileInput) fileInput.value = '';
};

const removeExistingProof = () => {
    form.value.remove_proof = true;
};

const restoreExistingProof = () => {
    form.value.remove_proof = false;
};

const submit = () => {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('payment_account_id', form.value.payment_account_id);
    formData.append('ref_number', form.value.ref_number);
    formData.append('total_amount', form.value.total_amount);
    formData.append('status', form.value.status);
    
    if (form.value.memo) {
        formData.append('memo', form.value.memo);
    }
    
    if (form.value.proof_of_payment) {
        formData.append('proof_of_payment', form.value.proof_of_payment);
    }
    
    if (form.value.remove_proof) {
        formData.append('remove_proof', '1');
    }

    router.post(route('internal.payments.update', props.payment.id), formData, {
        forceFormData: true,
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Edit Payment" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Payment</h1>
                    <p class="mt-1 text-sm text-gray-600">Update payment information</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow rounded-lg">
                            <form @submit.prevent="submit" class="space-y-6 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Payment Account -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Payment Account
                                        </label>
                                        <select
                                            v-model="form.payment_account_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                            <option v-for="account in paymentAccounts"
                                                :key="account.id"
                                                :value="account.id"
                                            >
                                                {{ account.account_name }} ({{ account.provider }})
                                            </option>
                                        </select>
                                        <p v-if="errors.payment_account_id" class="mt-1 text-sm text-red-600">
                                            {{ errors.payment_account_id }}
                                        </p>
                                    </div>

                                    <!-- Reference Number -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Reference Number
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.ref_number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                        <p v-if="errors.ref_number" class="mt-1 text-sm text-red-600">
                                            {{ errors.ref_number }}
                                        </p>
                                    </div>

                                    <!-- Amount -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Amount
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            v-model="form.total_amount"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                        <p v-if="errors.total_amount" class="mt-1 text-sm text-red-600">
                                            {{ errors.total_amount }}
                                        </p>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Status
                                        </label>
                                        <select
                                            v-model="form.status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                            <option v-for="status in statuses"
                                                :key="status.id"
                                                :value="status.id"
                                            >
                                                {{ status.name }}
                                            </option>
                                        </select>
                                        <p v-if="errors.status" class="mt-1 text-sm text-red-600">
                                            {{ errors.status }}
                                        </p>
                                    </div>

                                    <!-- Memo -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Memo
                                        </label>
                                        <textarea
                                            v-model="form.memo"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        ></textarea>
                                        <p v-if="errors.memo" class="mt-1 text-sm text-red-600">
                                            {{ errors.memo }}
                                        </p>
                                    </div>

                                    <!-- Payable Info (Read-only) -->
                                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-900">Payment For</h4>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ payment.payable.type }} #{{ payment.payable.ref_number }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ payment.payable.customer }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="flex justify-end space-x-3 pt-4 border-t">
                                    <Link
                                        :href="route('internal.payments.index')"
                                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        Update Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Proof of Payment Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Proof of Payment
                            </h2>

                            <!-- New Proof Upload -->
                            <div v-if="!newProofPreview" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload New Proof
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-500 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="proof-upload" class="relative cursor-pointer rounded-md font-medium text-red-600 hover:text-red-500">
                                                <span>Upload a file</span>
                                                <input
                                                    id="proof-upload"
                                                    type="file"
                                                    class="sr-only"
                                                    accept="image/*"
                                                    @change="handleFileUpload"
                                                >
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                                <p v-if="errors.proof_of_payment" class="mt-1 text-sm text-red-600">
                                    {{ errors.proof_of_payment }}
                                </p>
                            </div>

                            <!-- New Proof Preview -->
                            <div v-if="newProofPreview" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    New Proof (Not Saved Yet)
                                </label>
                                <div class="border-2 border-green-300 rounded-lg overflow-hidden relative group">
                                    <img :src="newProofPreview" alt="New proof" class="w-full h-48 object-contain bg-gray-50">
                                    <button
                                        type="button"
                                        @click="removeNewProof"
                                        class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-full hover:bg-red-700"
                                    >
                                        <XMarkIcon class="h-4 w-4" />
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-green-600 font-medium">✓ New file selected. Save to update.</p>
                            </div>

                            <!-- Existing Proof -->
                            <div v-if="payment.proof_of_payment && !form.remove_proof">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Current Proof
                                </label>
                                <div class="border-2 border-gray-200 rounded-lg overflow-hidden relative group">
                                    <div class="relative">
                                        <img
                                            :src="payment.proof_of_payment.file_path"
                                            :alt="payment.proof_of_payment.file_name"
                                            class="w-full h-48 object-contain cursor-pointer bg-gray-50"
                                            @click="openImagePreview(payment.proof_of_payment.file_path, payment.proof_of_payment.file_name)"
                                        />
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center cursor-pointer"
                                             @click="openImagePreview(payment.proof_of_payment.file_path, payment.proof_of_payment.file_name)">
                                            <MagnifyingGlassIcon class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-3 mt-2 rounded-lg">
                                    <p class="text-xs text-gray-600 truncate" :title="payment.proof_of_payment.file_name">
                                        {{ payment.proof_of_payment.file_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ (payment.proof_of_payment.file_size / 1024).toFixed(2) }} KB
                                    </p>
                                </div>

                                <div class="flex flex-col space-y-2 mt-3">
                                    <button
                                        type="button"
                                        @click="openImagePreview(payment.proof_of_payment.file_path, payment.proof_of_payment.file_name)"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                                        View Full Size
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeExistingProof"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                                    >
                                        <XMarkIcon class="h-5 w-5 mr-2" />
                                        Remove Proof
                                    </button>
                                </div>
                            </div>

                            <!-- Removed Proof Message -->
                            <div v-if="form.remove_proof" class="border-2 border-dashed border-red-300 rounded-lg p-4 bg-red-50">
                                <p class="text-sm text-red-600 font-medium mb-2">✗ Proof will be removed</p>
                                <button
                                    type="button"
                                    @click="restoreExistingProof"
                                    class="w-full px-3 py-2 text-sm bg-white border border-red-300 rounded-md text-red-700 hover:bg-red-50"
                                >
                                    Undo Remove
                                </button>
                            </div>

                            <!-- No Proof Message -->
                            <div v-if="!payment.proof_of_payment && !newProofPreview" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">No proof uploaded</p>
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
