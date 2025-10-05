<script setup>
import { ref, computed } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';

// Rental Frequency by Vehicle Type
const rentalFrequencyData = {
    labels: ['Sport Bike', 'Cruiser', 'Touring', 'Standard', 'Dual Sport', 'Scooter'],
    datasets: [{
        label: 'Number of Rentals',
        data: [150, 120, 80, 95, 60, 45],
        backgroundColor: [
            'rgba(239, 68, 68, 0.6)',
            'rgba(59, 130, 246, 0.6)',
            'rgba(16, 185, 129, 0.6)',
            'rgba(251, 191, 36, 0.6)',
            'rgba(139, 92, 246, 0.6)',
            'rgba(236, 72, 153, 0.6)'
        ]
    }]
};

// Monthly Rental Trends
const monthlyRentalData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
        label: 'Number of Rentals 2023',
        data: [45, 52, 49, 60, 55, 65, 70, 68, 72, 75, 80, 85],
        backgroundColor: 'rgba(239, 68, 68, 0.5)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 1
    }]
};

// Daily Rental Distribution
const dailyRentalData = {
    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
    datasets: [{
        label: 'Average Daily Rentals',
        data: [15, 18, 20, 22, 35, 45, 40],
        fill: false,
        borderColor: 'rgba(239, 68, 68, 1)',
        tension: 0.1
    }]
};

// Vehicle Performance Data
const topVehicles = [
    { model: 'Ninja ZX-10R', type: 'Sport Bike', rentals: 150, utilization: '85%' },
    { model: 'Gold Wing', type: 'Touring', rentals: 120, utilization: '80%' },
    { model: 'MT-07', type: 'Standard', rentals: 110, utilization: '75%' },
    { model: 'Rebel 500', type: 'Cruiser', rentals: 105, utilization: '72%' },
    { model: 'Africa Twin', type: 'Dual Sport', rentals: 95, utilization: '70%' }
];

const lowPerformingVehicles = [
    { model: 'Vespa Primavera', type: 'Scooter', rentals: 30, utilization: '25%' },
    { model: 'PCX150', type: 'Scooter', rentals: 35, utilization: '30%' },
    { model: 'KLR650', type: 'Dual Sport', rentals: 40, utilization: '35%' }
];

const metrics = {
    totalRentals: 776,
    averageRentalDuration: 3.5,
    peakUtilization: '85%',
    popularDays: 'Weekends'
};
</script>

<template>
    <InternalLayout>
        <Head title="Rental Analytics" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Rental Analytics</h1>
                    <p class="mt-1 text-sm text-gray-600">Comprehensive rental performance analysis</p>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total Rentals</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ metrics.totalRentals }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Avg. Rental Duration</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ metrics.averageRentalDuration }} days</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Peak Utilization</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ metrics.peakUtilization }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Most Popular Days</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ metrics.popularDays }}</p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Rental Distribution by Vehicle Type -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Rental Distribution by Vehicle Type</h3>
                        <PieChart :chart-data="rentalFrequencyData" />
                    </div>

                    <!-- Monthly Rental Trends -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Rental Trends</h3>
                        <BarChart :chart-data="monthlyRentalData" />
                    </div>

                    <!-- Daily Rental Distribution -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Rental Distribution</h3>
                        <LineChart :chart-data="dailyRentalData" />
                    </div>

                    <!-- Top Performing Vehicles -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Vehicles</h3>
                        <div class="space-y-4">
                            <div v-for="(vehicle, index) in topVehicles" :key="index"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ vehicle.model }}</p>
                                    <p class="text-sm text-gray-500">{{ vehicle.type }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-red-600">{{ vehicle.rentals }} rentals</p>
                                    <p class="text-xs text-gray-500">{{ vehicle.utilization }} utilization</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Performing Vehicles -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Low Performing Vehicles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="(vehicle, index) in lowPerformingVehicles" :key="index"
                            class="p-4 bg-red-50 rounded-lg border border-red-100">
                            <p class="font-medium text-gray-900">{{ vehicle.model }}</p>
                            <p class="text-sm text-gray-500">{{ vehicle.type }}</p>
                            <p class="text-sm font-semibold text-red-600 mt-2">{{ vehicle.rentals }} rentals</p>
                            <p class="text-xs text-gray-500">{{ vehicle.utilization }} utilization</p>
                        </div>
                    </div>
                </div>

                <!-- Key Insights -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Key Insights</h3>
                    <div class="prose max-w-none">
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Sport bikes are the most popular category with 150 rentals</li>
                            <li>Weekend rentals account for 45% of total rentals</li>
                            <li>Scooters show lowest utilization rates at 25-30%</li>
                            <li>Average rental duration is 3.5 days</li>
                            <li>Peak rental periods occur during weekends and holidays</li>
                            <li>The Ninja ZX-10R is the most frequently rented vehicle</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
