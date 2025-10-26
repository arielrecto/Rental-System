<script setup>
import { ref } from "vue";
import InternalLayout from "@/Layouts/InternalLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { PlusIcon } from "@heroicons/vue/24/outline";
import DataTable from "@/Components/DataTable.vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    requests: Array,
});

const deleteModal = ref(false);
const requestToDelete = ref(null);
const searchQuery = ref("");

const headers = [
    { key: "ref_number", label: "Reference" },
    { key: "vehicle_name", label: "Vehicle" },
    { key: "vehicle_plate", label: "Plate No." },
    { key: "requested_date", label: "Requested Date" },
    { key: "maintainance_date", label: "Maintenance Date" },
    { key: "cost", label: "Cost" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions" },
];

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(amount);
};

const confirmDelete = (request) => {
    requestToDelete.value = request;
    deleteModal.value = true;
};

const handleDelete = () => {
    router.delete(
        route("internal.maintenance.destroy", requestToDelete.value.id),
        {
            onSuccess: () => {
                deleteModal.value = false;
                requestToDelete.value = null;
            },
        }
    );
};
</script>

<template>
    <InternalLayout>
        <Head title="Maintenance Requests" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Maintenance Requests
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Manage vehicle maintenance requests
                        </p>
                    </div>
                    <Link
                        :href="route('internal.maintenance.create')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        <PlusIcon class="w-5 h-5 mr-2" />
                        New Request
                    </Link>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <DataTable :headers="headers" :items="requests">
                        <template #default="{ item }">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.ref_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.vehicle_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.vehicle_plate }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.requested_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.maintainance_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(item.cost) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <span 
                                        :class="[
                                            'px-3 py-1 rounded-full text-xs font-semibold',
                                            item.is_completed
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800'
                                        ]"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex justify-end space-x-3">
                                        <Link
                                            :href="route('internal.maintenance.show', item.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="route('internal.maintenance.edit', item.id)"
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
                        <!-- <template #item-cost="{ item }">
                            {{ formatCurrency(item.cost) }}
                        </template>

                        <template #item-status="{ item }">
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-xs font-semibold',
                                    item.is_completed
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800'
                                ]"
                            >
                                {{ item.status }}
                            </span>
                        </template> -->

                        <!-- <template #item-actions="{ item }">
                            <div class="flex justify-end space-x-3">
                                <Link
                                    :href="route('maintenance.show', item.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    View
                                </Link>
                                <Link
                                    :href="route('maintenance.edit', item.id)"
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
                        </template> -->
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal v-model="deleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Delete Maintenance Request
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete this maintenance request?
                    This action cannot be undone.
                </p>
                <div class="mt-4 flex justify-end space-x-3">
                    <button
                        @click="deleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
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
