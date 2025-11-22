<script setup>
import { ref, computed, toRefs, watch } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';

const props = defineProps({
    vehiclesReport: {
        type: Object,
        required: false,
        default: () => ({})
    }
});

const { vehiclesReport: vr } = toRefs(props);

// new: filter state (year + purchase date range)
const selectedYear = ref(vr.value.selectedYear ?? '');
const purchaseStart = ref(vr.value.purchase_start ?? '');
const purchaseEnd = ref(vr.value.purchase_end ?? '');

const years = computed(() => {
    const current = new Date().getFullYear();
    const out = [];
    for (let i = 0; i < 6; i++) out.push(current - i);
    return out;
});

const applyFilters = () => {
    router.get(
        route('internal.report.vehicle'),
        {
            year: selectedYear.value || undefined,
            purchase_start: purchaseStart.value || undefined,
            purchase_end: purchaseEnd.value || undefined
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    selectedYear.value = '';
    purchaseStart.value = '';
    purchaseEnd.value = '';
    applyFilters();
};

// keep local filters in sync when backend returns selected values
watch(() => vr.value.selectedYear, (v) => { if (v !== undefined) selectedYear.value = v; });
watch(() => vr.value.purchase_start, (v) => { if (v !== undefined) purchaseStart.value = v; });
watch(() => vr.value.purchase_end, (v) => { if (v !== undefined) purchaseEnd.value = v; });

// Charts and metrics derived from controller payload (fallbacks provided)
const vehicleValueData = computed(() => ({
    labels: vr.value.byType?.labels || [],
    datasets: [{
        label: 'Total Value by Type',
        data: vr.value.byType?.data || [],
        backgroundColor: [
            'rgba(239, 68, 68, 0.6)',
            'rgba(59, 130, 246, 0.6)',
            'rgba(16, 185, 129, 0.6)',
            'rgba(251, 191, 36, 0.6)',
            'rgba(139, 92, 246, 0.6)',
            'rgba(236, 72, 153, 0.6)'
        ]
    }]
}));

const vehicleAgeData = computed(() => ({
    labels: vr.value.ageDistribution?.labels || ['0-1 year', '1-2 years', '2-3 years', '3-4 years', '4-5 years', '5+ years'],
    datasets: [{
        label: 'Number of Vehicles',
        data: vr.value.ageDistribution?.data || [0,0,0,0,0,0],
        backgroundColor: 'rgba(239, 68, 68, 0.5)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 1
    }]
}));

const maintenanceCostData = computed(() => ({
    labels: vr.value.maintenance?.labels || ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    datasets: [{
        label: 'Monthly Maintenance Costs',
        data: vr.value.maintenance?.data || Array(12).fill(0),
        fill: false,
        borderColor: 'rgba(239, 68, 68, 1)',
        tension: 0.1
    }]
}));

const topVehicles = computed(() => vr.value.topVehicles || []);

const metrics = computed(() => vr.value.metrics || {
    totalAssetValue: 0,
    averageVehicleValue: 0,
    totalVehicles: 0,
    maintenanceBudget: 0
});

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

                <!-- new: Filters -->
                <!-- <div class="mb-6 flex flex-col md:flex-row md:items-end md:space-x-4 space-y-3 md:space-y-0">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Year</label>
                        <select v-model="selectedYear" class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">All</option>
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Purchase Start</label>
                        <input type="date" v-model="purchaseStart" class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Purchase End</label>
                        <input type="date" v-model="purchaseEnd" class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button @click="applyFilters" class="px-3 py-1 bg-red-600 text-white rounded-md text-sm">Apply</button>
                        <button @click="resetFilters" class="px-3 py-1 border rounded-md text-sm">Reset</button>
                    </div>
                </div> -->

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
                                    <p class="text-sm text-gray-500">{{ vehicle.type }} {{ vehicle.year ? '(' + vehicle.year + ')' : '' }}</p>
                                </div>
                                <p class="text-lg font-semibold text-red-600">
                                    {{ formatCurrency(vehicle.value || 0) }}
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
                            <li>Total asset value: {{ formatCurrency(metrics.totalAssetValue) }}</li>
                            <li>Average vehicle value: {{ formatCurrency(metrics.averageVehicleValue) }}</li>
                            <li>Maintenance budget estimate: {{ formatCurrency(metrics.maintenanceBudget) }}</li>
                            <li>Top vehicle: {{ topVehicles.length ? topVehicles[0].model : 'N/A' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
