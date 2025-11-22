<script setup>
import { ref } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import { router } from "@inertiajs/vue3";
import KioskLayout from "@/Layouts/KioskLayout.vue";
import Flash from "@/Components/Flash.vue";

defineProps({
    rentalOrders: Array,
    default: () => {
        return [
            {
                id: 1,
                order_number: "RO-2023-001",
                customer_name: "John Doe",
                vehicle: "Ninja ZX-10R",
                plate_no: "ABC-123",
                start_date: "2023-10-14",
                end_date: "2023-10-16",
                status: "pending",
            },
            {
                id: 2,
                order_number: "RO-2023-002",
                customer_name: "Jane Smith",
                vehicle: "MT-07",
                plate_no: "XYZ-789",
                start_date: "2023-10-14",
                end_date: "2023-10-15",
                status: "pending",
            },
        ];
    },
});

const page = usePage();
const pinModal = ref(false);
const selectedOrder = ref(null);
const pinInput = ref("");
const pinError = ref(page.props?.flash?.error);

const startSession = (order) => {
    selectedOrder.value = order;
    pinModal.value = true;
    pinInput.value = "";
    pinError.value = "";
};

const verifyPin = () => {
    router.post(route("kiosk.start-session"), {
        order_number: selectedOrder.value.order_number,
        pin: pinInput.value,
    });
};

const getStatusColor = (status) => {
    return (
        {
            pending: "bg-yellow-100 text-yellow-800",
            active: "bg-green-100 text-green-800",
            completed: "bg-blue-100 text-blue-800",
            cancelled: "bg-red-100 text-red-800",
        }[status] || "bg-gray-100 text-gray-800"
    );
};

// Add clock
const currentTime = ref(new Date().toLocaleTimeString());
const currentDate = ref(
    new Date().toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    })
);

// Update clock every second
setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString();
}, 1000);
</script>

<template>
    <KioskLayout
        :activeSessions="rentalOrders.filter((o) => o.status === 'active')"
    >
        <Flash />

        <main class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Status Summary -->
                <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                    <!-- <div class="p-6 text-white bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold opacity-90">Active Sessions</h3>
                        <p class="mt-2 text-3xl font-bold">
                            {{ rentalOrders.filter(o => o.status === 'active').length }}
                        </p>
                    </div>
                    <div class="p-6 text-white bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold opacity-90">Pending Orders</h3>
                        <p class="mt-2 text-3xl font-bold">
                            {{ rentalOrders.filter(o => o.status === 'pending').length }}
                        </p>
                    </div>
                    <div class="p-6 text-white bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold opacity-90">Completed Today</h3>
                        <p class="mt-2 text-3xl font-bold">
                            {{ rentalOrders.filter(o => o.status === 'completed').length }}
                        </p>
                    </div> -->
                </div>

                <!-- Orders Grid -->
                <div class="p-6 bg-white rounded-lg shadow-lg">
                    <h2 class="mb-6 text-xl font-bold text-gray-900">
                        Rental Orders
                    </h2>
                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="order in rentalOrders"
                            :key="order.id"
                            class="rounded-lg border border-gray-200 transition-shadow duration-200 hover:shadow-md"
                        >
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-600"
                                        >
                                            Order #{{ order.order_number }}
                                        </p>
                                        <h3
                                            class="mt-1 text-xl font-semibold text-gray-900"
                                        >
                                            {{ order.vehicle }}
                                        </h3>
                                    </div>
                                    <span
                                        :class="[
                                            getStatusColor(order.status),
                                            'px-3 py-1 text-xs font-semibold rounded-full',
                                        ]"
                                    >
                                        {{ order.status.toUpperCase() }}
                                    </span>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div
                                        class="flex items-center text-sm text-gray-600"
                                    >
                                        <svg
                                            class="mr-2 w-5 h-5 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        {{ order.customer_name }}
                                    </div>
                                    <div
                                        class="flex items-center text-sm text-gray-600"
                                    >
                                        <svg
                                            class="mr-2 w-5 h-5 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"
                                            />
                                        </svg>
                                        {{ order.plate_no }}
                                    </div>
                                    <div
                                        class="flex items-center text-sm text-gray-600"
                                    >
                                        <svg
                                            class="mr-2 w-5 h-5 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        {{ order.start_date }} to
                                        {{ order.end_date }}
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <template
                                        v-if="
                                            !order.sessions ||
                                            order.sessions.status !== 'active'
                                        "
                                    >
                                        <button
                                            @click="startSession(order)"
                                            :disabled="order.status == 'active'"
                                            class="inline-flex justify-center items-center px-4 py-2 w-full text-sm font-medium text-white bg-red-600 rounded-md border border-transparent transition-colors duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <svg
                                                class="mr-2 w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            Start Session
                                        </button>
                                    </template>
                                    <template
                                        v-else
                                    >
                                     <Link
                                        :href="route('kiosk.session', { sessionId: order.sessions.session_token, rentalOrderId: order.id })"
                                            class="inline-flex justify-center items-center px-4 py-2 w-full text-sm font-medium text-white bg-red-600 rounded-md border border-transparent transition-colors duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <svg
                                                class="mr-2 w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                           View Session
                                     </Link>
                                </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- PIN Modal -->
        <Modal v-model="pinModal" :closeable="true">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Enter PIN to Start Session
                </h2>

                <div class="mt-4">
                    <input
                        type="password"
                        v-model="pinInput"
                        maxlength="4"
                        class="block mt-1 w-full text-2xl tracking-widest text-center rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="****"
                        @keyup.enter="verifyPin"
                    />
                    <p v-if="pinError" class="mt-2 text-sm text-red-600">
                        {{ pinError }}
                    </p>
                </div>

                <div class="flex justify-end mt-6 space-x-3">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        @click="pinModal = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md border border-transparent hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        @click="verifyPin"
                    >
                        Verify & Start
                    </button>
                </div>
            </div>
        </Modal>
    </KioskLayout>
</template>

<style scoped>
.from-red-500 {
    --tw-gradient-from: #ef4444;
    --tw-gradient-stops: var(--tw-gradient-from),
        var(--tw-gradient-to, rgb(239 68 68 / 0));
}

.to-red-600 {
    --tw-gradient-to: #dc2626;
}
</style>
