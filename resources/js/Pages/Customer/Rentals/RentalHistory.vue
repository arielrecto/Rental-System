<script setup>
import { ref } from "vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Attachment from "@/Components/DisplayAttachment.vue";
import Drawer from "@/Components/Drawer.vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    rentalOrders: {
        type: Array,
        default: () => [],
    },
});

console.log(props.rentalOrders);

// Table headers configuration
const headers = [
    { key: "id", label: "Order ID" },
    { key: "vehicle", label: "Vehicle" },
    { key: "start_date", label: "Start Date" },
    { key: "end_date", label: "End Date" },
    { key: "total_amount", label: "Total Amount" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions" },
];

const selectedRentalOrder = ref(null);
const viewDetailState = ref(false);
// Hardcoded rental history data (replace with API data later)

// Statistics
const stats = ref({
    totalRentals: props.rentalOrders.data.length,
    activeRentals: props.rentalOrders.data.filter((r) => r.status === "active")
        .length,
    totalSpent: props.rentalOrders.data.reduce(
        (sum, rental) => sum + rental.total_amount,
        0
    ),
});

const viewDetailAction = (rentalOrder) => {
    viewDetailState.value = !viewDetailState.value;

    selectedRentalOrder.value = rentalOrder;
};
</script>

<template>
    <CustomerLayout>
        <Head title="Rental History" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Rental History
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        View all your past and current rentals
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                    <!-- Total Rentals -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-red-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Total Rentals
                                        </dt>
                                        <dd
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ stats.totalRentals }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Rentals -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-red-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Active Rentals
                                        </dt>
                                        <dd
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ stats.activeRentals }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Spent -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-red-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Total Spent
                                        </dt>
                                        <dd
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            ₱{{
                                                stats.totalSpent.toLocaleString()
                                            }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTable -->
                <DataTable
                    :headers="headers"
                    :items="rentalOrders.data"
                    :items-per-page="rentalOrders.per_page"
                    searchable
                >
                    <template #default="{ item }">
                        <tr class="hover:bg-gray-50">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                            >
                                {{ item.id }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ item.vehicle.brand }}
                                {{ item.vehicle.model }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ item.rental_date }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ item.return_date }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                ₱{{ item.total_amount.toLocaleString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800':
                                            item.status === 'completed',
                                        'bg-blue-100 text-blue-800':
                                            item.status === 'active',
                                        'bg-yellow-100 text-yellow-800':
                                            item.status === 'pending',
                                    }"
                                >
                                    {{
                                        item.status.charAt(0).toUpperCase() +
                                        item.status.slice(1)
                                    }}
                                </span>
                            </td>

                            <td>
                                <button
                                    @click="viewDetailAction(item)"
                                    class="text-green-500 hover:text-red-800 font-medium"
                                >
                                    View Details
                                </button>

                                <Link
                                    v-if="item.status === 'pending'"
                                    :href="
                                        route(
                                            'customer.rentals.cancel',
                                            item.id
                                        )
                                    "
                                    method="post"
                                    class="text-red-600 hover:text-red-800 font-medium ml-4"
                                >
                                    Cancel
                                </Link>
                            </td>
                        </tr>
                    </template>
                </DataTable>

                <Drawer
                    v-model="viewDetailState"
                    @close="viewDetailState = false"
                    position="right"
                    width="400px"
                >
                    <template #title>
                        <h1 class="text-3xl text-red-500">
                            {{ selectedRentalOrder?.vehicle?.brand }} -
                            {{ selectedRentalOrder?.vehicle?.model }}
                        </h1>
                    </template>

                    <template #default>
                        <div class="space-y-4 flex-1">
                            <div class="grid grid-cols-2 gap-2">
                                <h1 class="text-gray-600">Start Date</h1>
                                <p>
                                    {{ selectedRentalOrder?.rental_date }}
                                </p>
                                <h1 class="text-gray-600">end Date</h1>
                                <p>
                                    {{ selectedRentalOrder?.return_date }}
                                </p>
                            </div>

                            <div
                                class="flex p-2 rounded-lg bg-gray-50 min-h-96 w-full"
                            >
                                {{ selectedRentalOrder?.notes }}
                            </div>

                            <h1>Attachements</h1>
                            <div class="grid grid-cols-2 grid-flow-row">
                                <div
                                    v-for="file in selectedRentalOrder.attachments"
                                    :key="file.id"
                                >
                                    <Attachment :filePath="file.file_path" :fileExtension="file.file_extension" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <h1 class="text-lg font-bold">Total</h1>

                                <h1 class="text-lg font-bold text-red-500">
                                    ₱
                                    {{
                                        selectedRentalOrder?.total_amount.toLocaleString()
                                    }}
                                </h1>
                            </div>
                        </div>
                    </template>

                    <template #footer>
                        <div class="flex justify-end space-x-3">
                            <Link
                                v-if="selectedRentalOrder?.status === 'pending'"
                                :href="
                                    route(
                                        'customer.rentals.cancel',
                                        selectedRentalOrder?.id
                                    )
                                "
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                            >
                                Cancel
                            </Link>
                            <!-- <button
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                            >
                                Confirm
                            </button> -->
                        </div>
                    </template>
                </Drawer>
            </div>
        </div>
    </CustomerLayout>
</template>
