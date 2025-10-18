<script setup>
import { ref } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, TableCellsIcon, Squares2X2Icon } from '@heroicons/vue/24/outline';
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

const headers = [
    { key: 'account_number', label: 'Account Number' },
    { key: 'account_name', label: 'Account Name' },
    { key: 'provider', label: 'Provider' },
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

const getProviderIcon = (provider) => {
    const icons = {
        'gcash': '/icons/gcash.png',
        'maya': '/icons/maya.png',
        'bank': '/icons/bank.png',
        // Add more provider icons
    };
    return icons[provider.toLowerCase()] || '/icons/default.png';
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
                                        <img :src="getProviderIcon(item.provider)"
                                            :alt="item.provider"
                                            class="h-6 w-6 mr-2">
                                        {{ item.provider }}
                                    </div>
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
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <img :src="getProviderIcon(account.provider)"
                                        :alt="account.provider"
                                        class="h-10 w-10 mr-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ account.account_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ account.provider }}</p>
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

                            <div class="mt-4">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Account Number:</span>
                                    {{ account.account_number }}
                                </p>
                                <p v-if="account.descriptions" class="mt-2 text-sm text-gray-500">
                                    {{ account.descriptions }}
                                </p>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <Link
                                    :href="route('internal.payment-accounts.edit', account.id)"
                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-700"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="confirmDelete(account)"
                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-700"
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
    </InternalLayout>
</template>
