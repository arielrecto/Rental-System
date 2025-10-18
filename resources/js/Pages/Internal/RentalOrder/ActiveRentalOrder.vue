<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import LeafletMap from '@/Components/LeafletMap.vue';

const props = defineProps({
    activeRentals: {
        type: Array,
        required: true
    }
});

// Hardcoded locations for demo
const locations = ref([
    {
        id: 1,
        lat: 14.5995,
        lng: 120.9842,
        title: "Ninja ZX-10R - John Doe",
        status: "Active"
    },
    {
        id: 2,
        lat: 14.6037,
        lng: 121.0214,
        title: "MT-07 - Jane Smith",
        status: "Active"
    },
    {
        id: 3,
        lat: 14.5547,
        lng: 121.0244,
        title: "Gold Wing - Mike Johnson",
        status: "Active"
    }
]);

// Auto refresh map data
let refreshInterval;

const refreshLocations = () => {
    // In production, this would fetch real data
    // For demo, we'll just slightly modify coordinates
    locations.value = locations.value.map(location => ({
        ...location,
        lat: location.lat + (Math.random() - 0.5) * 0.001,
        lng: location.lng + (Math.random() - 0.5) * 0.001
    }));
};

onMounted(() => {
    refreshInterval = setInterval(refreshLocations, 5000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

const stats = {
    totalActive: locations.value.length,
    inMetroManila: locations.value.length,
    avgRentalDuration: "4.5 hours"
};
</script>

<template>
    <InternalLayout>
        <Head title="Active Rentals" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Active Rentals Tracking</h1>
                    <p class="mt-1 text-sm text-gray-600">Real-time location of active rentals</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total Active Rentals</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ stats.totalActive }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">In Metro Manila</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ stats.inMetroManila }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Average Rental Duration</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ stats.avgRentalDuration }}</p>
                    </div>
                </div>

                <!-- Map -->
                <div class="bg-white rounded-lg shadow p-6">
                    <LeafletMap :locations="locations" />
                </div>

                <!-- Active Rentals List -->
                <div class="mt-6 bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Active Rental Details</h3>
                        <div class="space-y-4">
                            <div v-for="rental in locations" :key="rental.id"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ rental.title }}</p>
                                    <p class="text-sm text-gray-500">
                                        Location: ({{ rental.lat.toFixed(4) }}, {{ rental.lng.toFixed(4) }})
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ rental.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
