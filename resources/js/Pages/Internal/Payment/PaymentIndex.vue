<script setup>
import { ref, computed } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, TableCellsIcon, Squares2X2Icon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import {router } from '@inertiajs/vue3'

const props = defineProps({
    payments: {
        type: Array,
        required: true
    },
    paymentAccounts: {
        type: Array,
        required: true
    },
    statuses: {
        type: Array,
        required: true
    }
});

const viewMode = ref('list');
const deleteModal = ref(false);
const paymentToDelete = ref(null);
const filters = ref({
    status: '',
    paymentAccount: '',
    dateRange: {
        start: '',
        end: ''
    }
});

const headers = [
    { key: 'ref_number', label: 'Reference' },
    { key: 'payable.type', label: 'Type' },
    { key: 'total_amount', label: 'Amount' },
    { key: 'payment_account.provider', label: 'Provider' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Date' },
    { key: 'actions', label: 'Actions' }
];

const filteredPayments = computed(() => {
    let result = [...props.payments];

    if (filters.value.status) {
        result = result.filter(p => p.status === filters.value.status);
    }

    if (filters.value.paymentAccount) {
        result = result.filter(p => p.payment_account.id === filters.value.paymentAccount);
    }

    if (filters.value.dateRange.start && filters.value.dateRange.end) {
        result = result.filter(p => {
            const date = new Date(p.created_at);
            return date >= new Date(filters.value.dateRange.start) &&
                   date <= new Date(filters.value.dateRange.end);
        });
    }

    return result;
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
};

const getStatusColor = (status) => {
    return {
        'pending': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
        'refunded': 'bg-gray-100 text-gray-800'
    }[status] || 'bg-gray-100 text-gray-800';
};

const confirmDelete = (payment) => {
    paymentToDelete.value = payment;
    deleteModal.value = true;
};

const handleDelete = () => {
    router.delete(route('internal.payments.destroy', paymentToDelete.value.id), {
        onSuccess: () => {
            deleteModal.value = false;
            paymentToDelete.value = null;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Payments" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Payments</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage payment transactions</p>
                    </div>
                    <div class="flex items-center space-x-4">
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
                            :href="route('internal.payments.create')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" />
                            New Payment
                        </Link>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6 bg-white rounded-lg shadow p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select
                                v-model="filters.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option value="">All Statuses</option>
                                <option v-for="status in statuses" :key="status" :value="status">
                                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Account</label>
                            <select
                                v-model="filters.paymentAccount"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option value="">All Accounts</option>
                                <option v-for="account in paymentAccounts" :key="account.id" :value="account.id">
                                    {{ account.account_name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input
                                type="date"
                                v-model="filters.dateRange.start"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input
                                type="date"
                                v-model="filters.dateRange.end"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div v-if="viewMode === 'list'" class="bg-white rounded-lg shadow">
                    <DataTable
                        :headers="headers"
                        :items="filteredPayments"
                    >
                        <template #default="{ item }">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.ref_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.payable?.type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(item.total_amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.payment_account.provider }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        getStatusColor(item.status),
                                        'px-3 py-1 rounded-full text-xs font-semibold'
                                    ]">
                                        {{ item.status.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(item.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <Link
                                            :href="route('internal.payments.show', item.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="route('internal.payments.edit', item.id)"
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
                    <div v-for="payment in filteredPayments"
                        :key="payment.id"
                        class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">
                                        {{ payment.ref_number }}
                                    </p>
                                    <p class="mt-1 text-xl font-semibold text-gray-900">
                                        {{ formatCurrency(payment.total_amount) }}
                                    </p>
                                </div>
                                <span :class="[
                                    getStatusColor(payment.status),
                                    'px-3 py-1 rounded-full text-xs font-semibold'
                                ]">
                                    {{ payment.status.toUpperCase() }}
                                </span>
                            </div>

                            <div class="mt-4 space-y-2">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Type:</span>
                                    {{ payment.payable?.type }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Provider:</span>
                                    {{ payment.payment_account.provider }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Date:</span>
                                    {{ new Date(payment.created_at).toLocaleDateString() }}
                                </p>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <Link
                                    :href="route('internal.payments.show', payment.id)"
                                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                                >
                                    View
                                </Link>
                                <Link
                                    :href="route('internal.payments.edit', payment.id)"
                                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="confirmDelete(payment)"
                                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal v-model="deleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Delete Payment
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete this payment? This action cannot be undone.
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
    </InternalLayout>
</template>
