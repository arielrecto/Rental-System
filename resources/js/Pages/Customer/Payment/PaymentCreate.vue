<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

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
});



const orderIdParam = ref(page.props?.requests?.orderId);
const orderTypeParam = ref(page.props?.requests?.payableType);

const errors = ref({});
const selectedPayableType = ref('rental_orders');
const showPayableModal = ref(false);

const statuses = [
    { id: 'pending', name: 'Pending' },
    // { id: 'completed', name: 'Completed' },
    // { id: 'failed', name: 'Failed' },
    // { id: 'refunded', name: 'Refunded' }
];





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
        onSuccess: () => {
            form.value = {
                payment_account_id: '',
                ref_number: '',
                total_amount: '',
                memo: '',
                status: 'pending',
                payable_id: '',
                payable_type: ''
            };
        },
        onError: (e) => {
            errors.value = e;
        }
    });
};



const selectPayableById = (id, type) => {


    const payable = props.payableItems?.rental_orders?.find(item => item.id == id && item.type == type);

    console.log(payable, 'payable');
     if (payable) {
        selectPayable(payable);
    }
};


watch( orderIdParam, () => {
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

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Account -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Payment Account
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
                            </div>

                            <!-- Reference Number -->
                            <!-- <div>
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
                            </div> -->

                            <!-- Payable Selection -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Payment For
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
                                    Amount
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
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
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <Link :href="route('internal.payments.index')"
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
                            <!-- Add more payable types here -->
                        </select>
                    </div>

                    <!-- Items List -->
                    <div class="mt-4 space-y-2">
                        <div v-if="selectedPayableType === 'rental_orders'">
                            <div v-for="item in payableItems.rental_orders" :key="item.id"
                                class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer"
                                @click="selectPayable(item)">
                                <div>
                                    <p class="font-medium text-gray-900">Order #{{ item.ref_number }}</p>
                                    <p class="text-sm text-gray-500">{{ item.customer }}</p>
                                </div>
                                <p class="font-medium text-gray-900">
                                    {{ formatCurrency(item.amount) }}
                                </p>
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
    </CustomerLayout>
</template>
