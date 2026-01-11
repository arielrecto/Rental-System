<script setup>
import { ref } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    rentalOrders: {
        type: Object,
        required: true
    }
});

const headers = [
    { key: 'id', label: 'Order ID' },
    { key: 'customer', label: 'Customer' },
    { key: 'vehicle', label: 'Vehicle' },
    { key: 'dates', label: 'Rental Period' },
    { key: 'total_amount', label: 'Total Amount' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Actions' }
];

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'active': 'bg-green-100 text-green-800',
        'completed': 'bg-blue-100 text-blue-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <InternalLayout>
        <Head title="Rental Orders" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Rental Orders</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage rental orders</p>
                    </div>
                    <Link
                        :href="route('internal.rental-orders.create')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        <PlusIcon class="w-5 h-5 mr-2" />
                        New Rental Order
                    </Link>
                </div>

                <!-- Table -->
                <DataTable
                    :headers="headers"
                    :items="rentalOrders.data"
                    class="mb-6"
                >
                    <template #default="{ item }">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ item.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ item.user.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ item.vehicle.brand }} {{ item.vehicle.model }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(item.rental_date).toLocaleDateString() }} -
                                {{ new Date(item.return_date).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₱{{ item.total_amount.toLocaleString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="getStatusColor(item.status)"
                                >
                                    {{ item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <Link
                                        :href="route('internal.rental-orders.show', item.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('internal.rental-orders.edit', item.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Edit
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </template>
                </DataTable>
            </div>
        </div>
    </InternalLayout>
</template>
