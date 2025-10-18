<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { router } from '@inertiajs/vue3';

const pinModal = ref(false);
const selectedOrder = ref(null);
const pinInput = ref('');
const pinError = ref('');

// Hardcoded rental orders
const rentalOrders = ref([
    {
        id: 1,
        order_number: 'RO-2023-001',
        customer_name: 'John Doe',
        vehicle: 'Ninja ZX-10R',
        plate_no: 'ABC-123',
        start_date: '2023-10-14',
        end_date: '2023-10-16',
        status: 'pending'
    },
    {
        id: 2,
        order_number: 'RO-2023-002',
        customer_name: 'Jane Smith',
        vehicle: 'MT-07',
        plate_no: 'XYZ-789',
        start_date: '2023-10-14',
        end_date: '2023-10-15',
        status: 'pending'
    }
]);

const startSession = (order) => {
    selectedOrder.value = order;
    pinModal.value = true;
    pinInput.value = '';
    pinError.value = '';
};

const verifyPin = () => {
    // Hardcoded PIN for demo (1234)
    if (pinInput.value === '1234') {
        // Start the session
        selectedOrder.value.status = 'active';
        pinModal.value = false;

        // Show success message
        alert('Session started successfully!');
    } else {
        pinError.value = 'Invalid PIN. Please try again.';
    }
};

const getStatusColor = (status) => {
    return {
        'pending': 'bg-yellow-100 text-yellow-800',
        'active': 'bg-green-100 text-green-800',
        'completed': 'bg-blue-100 text-blue-800',
        'cancelled': 'bg-red-100 text-red-800'
    }[status] || 'bg-gray-100 text-gray-800';
};

// Add clock
const currentTime = ref(new Date().toLocaleTimeString());
const currentDate = ref(new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
}));

// Update clock every second
setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString();
}, 1000);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Head title="Rental Kiosk | Anaia's Motorcycle Rental" />

        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <img src="/logo.png" alt="Anaia's Motorcycle Rental" class="h-12 w-auto mr-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Rental Kiosk</h1>
                            <p class="text-sm text-gray-500">Manage rental sessions</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-red-600">{{ currentTime }}</p>
                        <p class="text-sm text-gray-500">{{ currentDate }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Status Summary -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                        <h3 class="text-lg font-semibold opacity-90">Active Sessions</h3>
                        <p class="text-3xl font-bold mt-2">
                            {{ rentalOrders.filter(o => o.status === 'active').length }}
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
                        <h3 class="text-lg font-semibold opacity-90">Pending Orders</h3>
                        <p class="text-3xl font-bold mt-2">
                            {{ rentalOrders.filter(o => o.status === 'pending').length }}
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                        <h3 class="text-lg font-semibold opacity-90">Completed Today</h3>
                        <p class="text-3xl font-bold mt-2">
                            {{ rentalOrders.filter(o => o.status === 'completed').length }}
                        </p>
                    </div> -->
                </div>

                <!-- Orders Grid -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Rental Orders</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="order in rentalOrders"
                            :key="order.id"
                            class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">
                                            Order #{{ order.order_number }}
                                        </p>
                                        <h3 class="mt-1 text-xl font-semibold text-gray-900">
                                            {{ order.vehicle }}
                                        </h3>
                                    </div>
                                    <span :class="[
                                        getStatusColor(order.status),
                                        'px-3 py-1 text-xs font-semibold rounded-full'
                                    ]">
                                        {{ order.status.toUpperCase() }}
                                    </span>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ order.customer_name }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                        </svg>
                                        {{ order.plate_no }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ order.start_date }} to {{ order.end_date }}
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button
                                        @click="startSession(order)"
                                        :disabled="order.status !== 'pending'"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                                    >
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Start Session
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- PIN Modal -->
        <Modal
            v-model="pinModal"
            :closeable="true"
        >
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Enter PIN to Start Session
                </h2>

                <div class="mt-4">
                    <input
                        type="password"
                        v-model="pinInput"
                        maxlength="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-center text-2xl tracking-widest"
                        placeholder="****"
                        @keyup.enter="verifyPin"
                    />
                    <p v-if="pinError" class="mt-2 text-sm text-red-600">
                        {{ pinError }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        @click="pinModal = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        @click="verifyPin"
                    >
                        Verify & Start
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
.from-red-500 {
    --tw-gradient-from: #ef4444;
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(239 68 68 / 0));
}
.to-red-600 {
    --tw-gradient-to: #dc2626;
}
</style>
