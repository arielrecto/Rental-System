<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import LeafletMap from '@/Components/LeafletMap.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    activeRentals: {
        type: Array,
        required: true
        // Expected format:
        // [{
        //   id: 1,
        //   session_token: 'xxx',
        //   vehicle: { brand: '...', model: '...', plate_no: '...' },
        //   rental_order: { user: { name: '...' } },
        //   started_at: '2023-10-25 10:00:00',
        //   latest_location: { latitude: 14.5995, longitude: 120.9842 }
        // }]
    }
});

const locations = ref([]);

// Transform rental sessions into map locations
const updateLocations = () => {
    locations.value = props.activeRentals.map(rental => ({
        id: rental.id,
        lat: rental.latest_location?.latitude ?? 14.5995, // Default to Manila if no location
        lng: rental.latest_location?.longitude ?? 120.9842,
        title: `${rental.vehicle.brand} ${rental.vehicle.model} - ${rental.rental_order.user.name}`,
        status: rental.status,
        details: {
            plate: rental.vehicle.plate_no,
            startTime: new Date(rental.started_at).toLocaleTimeString(),
            duration: computeDuration(rental.started_at)
        }
    }));
};

// Compute duration since rental started
const computeDuration = (startTime) => {
    const start = new Date(startTime);
    const now = new Date();
    const diff = Math.floor((now - start) / 1000 / 60); // minutes
    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;
    return `${hours}h ${minutes}m`;
};

// Auto refresh data
let refreshInterval;

const refreshData = () => {
    router.get(route('internal.rental-orders.active'), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            updateLocations();
        }
    });
};

onMounted(() => {
    updateLocations();
    refreshInterval = setInterval(refreshData, 5000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// Computed stats
const stats = {
    totalActive: computed(() => locations.value.length),
    inMetroManila: computed(() => locations.value.filter(loc =>
        loc.lat >= 14.4 && loc.lat <= 14.8 &&
        loc.lng >= 120.9 && loc.lng <= 121.1
    ).length),
    avgRentalDuration: computed(() => {
        if (locations.value.length === 0) return "0h 0m";
        const totalMinutes = locations.value.reduce((acc, loc) => {
            const start = new Date(props.activeRentals.find(r => r.id === loc.id).started_at);
            const now = new Date();
            return acc + Math.floor((now - start) / 1000 / 60);
        }, 0);
        const avgMinutes = Math.floor(totalMinutes / locations.value.length);
        return `${Math.floor(avgMinutes / 60)}h ${avgMinutes % 60}m`;
    })
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
                    <LeafletMap :locations="locations" class="h-[500px]" />
                </div>

                <!-- Active Rentals List -->
                <div class="mt-6 bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Active Rental Details</h3>
                        <div class="space-y-4">
                            <div v-for="rental in locations" :key="rental.id"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <p class="font-medium text-gray-900">{{ rental.title }}</p>
                                    <p class="text-sm text-gray-500">Plate: {{ rental.details.plate }}</p>
                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                        <span>Started: {{ rental.details.startTime }}</span>
                                        <span>Duration: {{ rental.details.duration }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
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
