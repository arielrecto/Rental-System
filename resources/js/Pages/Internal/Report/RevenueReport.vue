<script setup>
import { ref, computed } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';

// Monthly Revenue Data
const monthlyRevenueData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
        label: 'Monthly Revenue 2023',
        data: [45000, 52000, 49000, 60000, 55000, 65000, 70000, 68000, 72000, 75000, 80000, 85000],
        backgroundColor: 'rgba(239, 68, 68, 0.5)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 1
    }]
};

// Daily Revenue for Current Month
const dailyRevenueData = {
    labels: Array.from({length: 30}, (_, i) => `Day ${i + 1}`),
    datasets: [{
        label: 'Daily Revenue',
        data: Array.from({length: 30}, () => Math.floor(Math.random() * 3000) + 1000),
        fill: false,
        borderColor: 'rgba(239, 68, 68, 1)',
        tension: 0.1
    }]
};

// Revenue by Vehicle Type
const vehicleTypeRevenueData = {
    labels: ['Sport Bike', 'Cruiser', 'Touring', 'Standard', 'Dual Sport', 'Scooter'],
    datasets: [{
        label: 'Revenue by Vehicle Type',
        data: [150000, 120000, 180000, 90000, 70000, 45000],
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

// Revenue Metrics
const metrics = {
    totalRevenue: 755000,
    averageDaily: 2516.67,
    monthlyGrowth: 12.5,
    yearlyProjection: 900000
};

const topPerformers = [
    { type: 'Sport Bike', model: 'Ninja ZX-10R', revenue: 45000 },
    { type: 'Cruiser', model: 'Rebel 1100', revenue: 38000 },
    { type: 'Touring', model: 'Gold Wing', revenue: 52000 },
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(value);
};
</script>

<template>
    <InternalLayout>
        <Head title="Revenue Report" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Revenue Analytics</h1>
                    <p class="mt-1 text-sm text-gray-600">Detailed revenue analysis and insights</p>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ formatCurrency(metrics.totalRevenue) }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Average Daily Revenue</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ formatCurrency(metrics.averageDaily) }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Monthly Growth</h3>
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            +{{ metrics.monthlyGrowth }}%
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-medium text-gray-500">Yearly Projection</h3>
                        <p class="mt-2 text-3xl font-bold text-blue-600">
                            {{ formatCurrency(metrics.yearlyProjection) }}
                        </p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Monthly Revenue Trend -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Revenue Trend</h3>
                        <BarChart :chart-data="monthlyRevenueData" />
                    </div>

                    <!-- Daily Revenue -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Revenue (Current Month)</h3>
                        <LineChart :chart-data="dailyRevenueData" />
                    </div>

                    <!-- Revenue by Vehicle Type -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Distribution by Vehicle Type</h3>
                        <PieChart :chart-data="vehicleTypeRevenueData" />
                    </div>

                    <!-- Top Performing Vehicles -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Vehicles</h3>
                        <div class="space-y-4">
                            <div v-for="(vehicle, index) in topPerformers" :key="index"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ vehicle.model }}</p>
                                    <p class="text-sm text-gray-500">{{ vehicle.type }}</p>
                                </div>
                                <p class="text-lg font-semibold text-red-600">
                                    {{ formatCurrency(vehicle.revenue) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Insights -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Insights</h3>
                    <div class="prose max-w-none">
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Monthly revenue shows a consistent upward trend with 12.5% average growth</li>
                            <li>Sport bikes and touring motorcycles generate the highest revenue</li>
                            <li>Weekend rentals contribute to 60% of weekly revenue</li>
                            <li>Premium models show higher profit margins despite lower rental frequency</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
