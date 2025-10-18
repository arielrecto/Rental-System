<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Head } from '@inertiajs/vue3';

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
    status: props.payment.status
});

const errors = ref({});

const statuses = [
    { id: 'pending', name: 'Pending' },
    { id: 'completed', name: 'Completed' },
    { id: 'failed', name: 'Failed' },
    { id: 'refunded', name: 'Refunded' }
];

const submit = () => {
    router.put(route('internal.payments.update', props.payment.id), form.value, {
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <CustomerLayout>
        <Head title="Edit Payment" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Payment</h1>
                    <p class="mt-1 text-sm text-gray-600">Update payment information</p>
                </div>

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
                        <div class="flex justify-end space-x-3">
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
        </div>
    </CustomerLayout>
</template>
