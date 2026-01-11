<script setup>
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { QrCodeIcon, MagnifyingGlassIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    paymentAccounts: {
        type: Array,
        required: true
    },
    payableItems: {
        type: Object,
        required: true
    }
});

const page = usePage();

const form = ref({
    payment_account_id: '',
    ref_number: '',
    total_amount: '',
    memo: '',
    status: 'pending',
    payable_id: '',
    payable_type: '',
    paid_by: null,
    proof_of_payment: null
});

const proofPreview = ref(null);
const orderIdParam = ref(page.props?.requests?.orderId);
const orderTypeParam = ref(page.props?.requests?.payableType);

const errors = ref({});
const selectedPayableType = ref('rental_orders');
const showPayableModal = ref(false);
const qrPreviewModal = ref(false);

const statuses = [
    { id: 'pending', name: 'Pending' },
];

const selectedAccount = computed(() => {
    return props.paymentAccounts.find(acc => acc.id === form.value.payment_account_id);
});

const handleProofUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.value.proof_of_payment = file;
        proofPreview.value = URL.createObjectURL(file);
    }
};

const removeProof = () => {
    form.value.proof_of_payment = null;
    proofPreview.value = null;
};

const openQrPreview = () => {
    if (selectedAccount.value?.qr_code) {
        qrPreviewModal.value = true;
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
};

const selectPayable = (item) => {
    form.value.payable_id = item.id;
    form.value.payable_type = item.type;
    form.value.total_amount = item.amount;
    form.value.paid_by = item.customer_id;
    form.value.memo = `Payment for Rental Order #${item.ref_number}, Amount: ${formatCurrency(item.amount)}`;
    showPayableModal.value = false;
};

const submit = () => {
    router.post(route('customer.payments.store'), form.value, {
        forceFormData: true,
        onSuccess: () => {
            form.value = {
                payment_account_id: '',
                ref_number: '',
                total_amount: '',
                memo: '',
                status: 'pending',
                payable_id: '',
                payable_type: '',
                paid_by: null,
                proof_of_payment: null
            };
            proofPreview.value = null;
        },
        onError: (e) => {
            errors.value = e;
        }
    });
};

const selectPayableById = (id, type) => {
    const payable = props.payableItems?.rental_orders?.find(item => item.id == id && item.type == type);
    if (payable) {
        selectPayable(payable);
    }
};

watch(orderIdParam, () => {
    selectPayableById(orderIdParam.value, orderTypeParam.value);
}, { immediate: true });

</script>

<template>
    <CustomerLayout>
        <Head title="Create Payment" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Create Payment</h1>
                    <p class="mt-1 text-sm text-gray-600">Record a new payment transaction</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow rounded-lg">
                            <form @submit.prevent="submit" class="space-y-6 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Payment Account -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Payment Account <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.payment_account_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            <option value="">Select Account</option>
                                            <option v-for="account in paymentAccounts" :key="account.id" :value="account.id">
                                                {{ account.account_name }} ({{ account.provider }})
                                            </option>
                                        </select>
                                        <p v-if="errors.payment_account_id" class="mt-1 text-sm text-red-600">
                                            {{ errors.payment_account_id }}
                                        </p>
                                        
                                        <!-- Selected Account Info -->
                                        <div v-if="selectedAccount" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <h3 class="text-sm font-medium text-blue-800">
                                                        Account Details
                                                    </h3>
                                                    <div class="mt-2 text-sm text-blue-700">
                                                        <p><span class="font-medium">Account Name:</span> {{ selectedAccount.account_name }}</p>
                                                        <p><span class="font-medium">Account Number:</span> {{ selectedAccount.account_number }}</p>
                                                        <p><span class="font-medium">Provider:</span> {{ selectedAccount.provider }}</p>
                                                        <p v-if="selectedAccount.qr_code" class="mt-2 text-xs">
                                                            <QrCodeIcon class="h-4 w-4 inline mr-1" />
                                                            QR Code available - Check the sidebar to scan
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payable Selection -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Payment For <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1 flex items-center space-x-3">
                                            <button type="button" @click="showPayableModal = true"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                Select Item
                                            </button>
                                            <div v-if="form.payable_id" class="text-sm text-gray-600">
                                                Selected: Rental Order #{{ form.payable_id }}
                                            </div>
                                        </div>
                                        <p v-if="errors.payable_id" class="mt-1 text-sm text-red-600">
                                            {{ errors.payable_id }}
                                        </p>
                                    </div>

                                    <!-- Amount -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Amount <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" step="0.01" v-model="form.total_amount"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <p v-if="errors.total_amount" class="mt-1 text-sm text-red-600">
                                            {{ errors.total_amount }}
                                        </p>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Status
                                        </label>
                                        <select v-model="form.status" disabled
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50">
                                            <option v-for="status in statuses" :key="status.id" :value="status.id">
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
                                        <textarea v-model="form.memo" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                                        <p v-if="errors.memo" class="mt-1 text-sm text-red-600">
                                            {{ errors.memo }}
                                        </p>
                                    </div>

                                    <!-- Proof of Payment Upload -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Proof of Payment <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex items-start space-x-4">
                                            <div v-if="proofPreview" class="relative flex-shrink-0">
                                                <img :src="proofPreview" class="w-40 h-40 object-cover rounded-lg border-2 border-gray-300" />
                                                <button
                                                    type="button"
                                                    @click="removeProof"
                                                    class="absolute -top-2 -right-2 p-1 bg-red-600 text-white rounded-full hover:bg-red-700"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div v-else class="w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center hover:border-red-300 transition-colors">
                                                <label class="cursor-pointer flex flex-col items-center">
                                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    <span class="mt-2 text-xs text-gray-500">Upload</span>
                                                    <input type="file" class="sr-only" @change="handleProofUpload" accept="image/*">
                                                </label>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p class="font-medium">Upload proof of payment</p>
                                                <p class="mt-1">JPG, PNG or GIF up to 10MB</p>
                                                <p class="mt-1 text-xs text-gray-500">This will help us verify your payment faster</p>
                                            </div>
                                        </div>
                                        <p v-if="errors.proof_of_payment" class="mt-1 text-sm text-red-600">
                                            {{ errors.proof_of_payment }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="flex justify-end space-x-3 pt-4 border-t">
                                    <Link :href="route('customer.payments.history')"
                                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancel
                                    </Link>
                                    <button type="submit"
                                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Create Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- QR Code Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <QrCodeIcon class="h-5 w-5 mr-2 text-red-600" />
                                Scan to Pay
                            </h2>

                            <div v-if="selectedAccount?.qr_code">
                                <!-- QR Code Display -->
                                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-lg p-4 border-2 border-red-200">
                                    <div class="relative group cursor-pointer" @click="openQrPreview">
                                        <img 
                                            :src="selectedAccount.qr_code.file_path" 
                                            :alt="selectedAccount.qr_code.file_name"
                                            class="w-full h-auto object-contain bg-white rounded-lg shadow-md"
                                        >
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg flex items-center justify-center transition-all">
                                            <MagnifyingGlassIcon class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openQrPreview"
                                        class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors"
                                    >
                                        <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                                        View Full Size
                                    </button>
                                </div>

                                <!-- Instructions -->
                                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h3 class="text-sm font-medium text-green-800 mb-2">
                                        📱 How to Pay:
                                    </h3>
                                    <ol class="text-sm text-green-700 space-y-1 list-decimal list-inside">
                                        <li>Open your {{ selectedAccount.provider }} app</li>
                                        <li>Scan the QR code above</li>
                                        <li>Enter the payment amount</li>
                                        <li>Complete the transaction</li>
                                        <li>Upload proof of payment below</li>
                                    </ol>
                                </div>

                                <!-- Account Info -->
                                <div class="mt-4 bg-gray-50 rounded-lg p-3 text-sm">
                                    <p class="text-gray-700">
                                        <span class="font-medium">Account Name:</span><br>
                                        {{ selectedAccount.account_name }}
                                    </p>
                                    <p class="text-gray-700 mt-2">
                                        <span class="font-medium">Account Number:</span><br>
                                        {{ selectedAccount.account_number }}
                                    </p>
                                </div>
                            </div>

                            <!-- No Account Selected -->
                            <div v-else-if="!form.payment_account_id" class="text-center py-8">
                                <QrCodeIcon class="mx-auto h-16 w-16 text-gray-400" />
                                <p class="mt-4 text-sm text-gray-600">
                                    Select a payment account above to view QR code
                                </p>
                            </div>

                            <!-- No QR Code Available -->
                            <div v-else class="text-center py-8">
                                <QrCodeIcon class="mx-auto h-16 w-16 text-gray-400" />
                                <p class="mt-4 text-sm text-gray-600">
                                    No QR code available for this account
                                </p>
                                <p class="mt-2 text-xs text-gray-500">
                                    You can still make manual payment using the account details
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payable Selection Modal -->
        <Modal v-model="showPayableModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Select Payment Item
                </h3>

                <div class="space-y-4">
                    <!-- Item Type Selection -->
                    <div>
                        <select v-model="selectedPayableType"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="rental_orders">Rental Orders</option>
                        </select>
                    </div>

                    <!-- Items List -->
                    <div class="mt-4 space-y-2 max-h-96 overflow-y-auto">
                        <div v-if="selectedPayableType === 'rental_orders'">
                            <div v-for="item in payableItems.rental_orders" :key="item.id"
                                class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer border border-gray-200 transition-colors"
                                @click="selectPayable(item)">
                                <div>
                                    <p class="font-medium text-gray-900">Order #{{ item.ref_number }}</p>
                                    <p class="text-sm text-gray-500">{{ item.customer }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ item.date }}</p>
                                </div>
                                <p class="font-medium text-gray-900">
                                    {{ formatCurrency(item.amount) }}
                                </p>
                            </div>
                            <div v-if="payableItems.rental_orders.length === 0" class="text-center py-8 text-gray-500">
                                <p>No unpaid rental orders found</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="showPayableModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </div>
        </Modal>

        <!-- QR Code Preview Modal -->
        <Modal v-model="qrPreviewModal" max-width="3xl">
            <div class="p-6 bg-white" v-if="selectedAccount?.qr_code">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">QR Code - {{ selectedAccount.account_name }}</h3>
                    <button
                        @click="qrPreviewModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-lg p-8 flex justify-center items-center">
                    <img
                        :src="selectedAccount.qr_code.file_path"
                        :alt="selectedAccount.qr_code.file_name"
                        class="max-w-full max-h-[500px] object-contain shadow-2xl rounded-lg"
                    />
                </div>

                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 font-medium">Account Name:</p>
                            <p class="text-gray-900">{{ selectedAccount.account_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Account Number:</p>
                            <p class="text-gray-900">{{ selectedAccount.account_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Provider:</p>
                            <p class="text-gray-900">{{ selectedAccount.provider }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">File Size:</p>
                            <p class="text-gray-900">{{ (selectedAccount.qr_code.file_size / 1024).toFixed(2) }} KB</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end space-x-3">
                    <a
                        :href="selectedAccount.qr_code.file_path"
                        download
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                    >
                        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                        Download QR Code
                    </a>
                </div>
            </div>
        </Modal>
    </CustomerLayout>
</template>
