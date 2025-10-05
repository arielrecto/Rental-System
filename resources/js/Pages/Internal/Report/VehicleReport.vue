<script setup>
import { ref, computed } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';

// Vehicle Value Distribution by Type
const vehicleValueData = {
    labels: ['Sport Bike', 'Cruiser', 'Touring', 'Standard', 'Dual Sport', 'Scooter'],
    datasets: [{
        label: 'Total Value by Type',
        data: [2500000, 1800000, 3200000, 1200000, 900000, 500000],
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

// Vehicle Age Distribution
const vehicleAgeData = {
    labels: ['0-1 year', '1-2 years', '2-3 years', '3-4 years', '4-5 years', '5+ years'],
    datasets: [{
        label: 'Number of Vehicles',
        data: [12, 15, 8, 6, 4, 2],
        backgroundColor: 'rgba(239, 68, 68, 0.5)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 1
    }]
};

// Maintenance Cost Trends
const maintenanceCostData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
        label: 'Monthly Maintenance Costs',
        data: [25000, 30000, 22000, 28000, 35000, 32000, 40000, 38000, 42000, 45000, 48000, 50000],
        fill: false,
        borderColor: 'rgba(239, 68, 68, 1)',
        tension: 0.1
    }]
};

// Most Valuable Vehicles
const topVehicles = [
    { model: 'Gold Wing Tour DCT', type: 'Touring', value: 1500000, year: 2023 },
    { model: 'BMW K 1600 GTL', type: 'Touring', value: 1300000, year: 2023 },
    { model: 'Ducati Panigale V4', type: 'Sport Bike', value: 1200000, year: 2023 },
    { model: 'Harley-Davidson CVO', type: 'Cruiser', value: 1100000, year: 2022 },
    { model: 'Kawasaki Ninja H2', type: 'Sport Bike', value: 950000, year: 2023 }
];

const metrics = {
    totalAssetValue: 10100000,
    averageVehicleValue: 215000,
    totalVehicles: 47,
    maintenanceBudget: 435000
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(value);
};
</script>

<template>
    <InternalLayout>
        <Head title="Vehicle Report" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Vehicle Asset Report</h1>
                    <p class="mt-1 text-sm text-gray-600">Comprehensive overview of vehicle assets and valuations</p>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total Asset Value</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ formatCurrency(metrics.totalAssetValue) }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Average Vehicle Value</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ formatCurrency(metrics.averageVehicleValue) }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total Vehicles</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ metrics.totalVehicles }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Annual Maintenance Budget</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ formatCurrency(metrics.maintenanceBudget) }}
                        </p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Value Distribution -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Value Distribution by Vehicle Type</h3>
                        <PieChart :chart-data="vehicleValueData" />
                    </div>

                    <!-- Age Distribution -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Age Distribution</h3>
                        <BarChart :chart-data="vehicleAgeData" />
                    </div>

                    <!-- Maintenance Costs -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Maintenance Cost Trends</h3>
                        <LineChart :chart-data="maintenanceCostData" />
                    </div>

                    <!-- Most Valuable Vehicles -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Most Valuable Vehicles</h3>
                        <div class="space-y-4">
                            <div v-for="(vehicle, index) in topVehicles" :key="index"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ vehicle.model }}</p>
                                    <p class="text-sm text-gray-500">{{ vehicle.type }} ({{ vehicle.year }})</p>
                                </div>
                                <p class="text-lg font-semibold text-red-600">
                                    {{ formatCurrency(vehicle.value) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Management Insights -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Asset Management Insights</h3>
                    <div class="prose max-w-none">
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Touring bikes represent the highest value segment at {{ formatCurrency(3200000) }}</li>
                            <li>57% of the fleet is less than 2 years old</li>
                            <li>Monthly maintenance costs show an upward trend, averaging {{ formatCurrency(35000) }}</li>
                            <li>Top 5 vehicles account for 60% of total fleet value</li>
                            <li>Premium touring and sport bikes maintain the highest resale value</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
