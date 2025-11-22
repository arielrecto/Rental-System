<script setup>
import { toRefs, ref, computed, watch, nextTick } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';

const props = defineProps({
    revenue: {
        type: Object,
        required: true
    }
});

const { revenue: r } = toRefs(props);

const selectedYear = ref(r.value.selectedYear || new Date().getFullYear());
const selectedMonth = ref(r.value.selectedMonth || new Date().getMonth() + 1);

// keep selectedYear in sync with incoming props
watch(() => r.value.selectedYear, (v) => {
    if (v) {
        selectedYear.value = v;
        if (!isEditingYear.value) yearInput.value = v;
    }
});

// editable year state (double-click -> input)
const isEditingYear = ref(false);
const yearInput = ref(selectedYear.value);
const yearInputRef = ref(null);

const startEditYear = async () => {
    isEditingYear.value = true;
    yearInput.value = selectedYear.value;
    await nextTick();
    if (yearInputRef.value && yearInputRef.value.focus) {
        yearInputRef.value.focus();
        yearInputRef.value.select();
    }
};

const cancelEdit = () => {
    isEditingYear.value = false;
    yearInput.value = selectedYear.value;
};

const saveYear = () => {
    const parsed = parseInt(yearInput.value, 10);
    const currentYear = new Date().getFullYear();
    if (isNaN(parsed) || parsed < 1900 || parsed > currentYear + 5) {
        yearInput.value = selectedYear.value;
        isEditingYear.value = false;
        return;
    }
    selectedYear.value = parsed;
    isEditingYear.value = false;
    onYearChange();
};

const years = computed(() => {
    const y = Number(selectedYear.value) || new Date().getFullYear();
    const out = [];
    for (let i = 0; i < 6; i++) out.push(y - i);
    return out;
});

const onYearChange = () => {
    router.get(route('internal.report.revenue'), { year: selectedYear.value }, { preserveState: true, replace: true });
};

const onMonthChange = () => {
    router.get(route('internal.report.revenue'), { year: selectedYear.value, month: selectedMonth.value }, { preserveState: true, replace: true });
};

const monthlyRevenueData = computed(() => ({
    labels: r.value.monthly?.labels || [],
    datasets: [{
        label: `Monthly Revenue ${selectedYear.value}`,
        data: r.value.monthly?.data || [],
        backgroundColor: 'rgba(239, 68, 68, 0.5)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 1
    }]
}));

const dailyRevenueData = computed(() => ({
    labels: r.value.daily?.labels || [],
    datasets: [{
        label: `Daily Revenue ${selectedYear.value}`,
        data: r.value.daily?.data || [],
        fill: false,
        borderColor: 'rgba(239, 68, 68, 1)',
        tension: 0.1
    }]
}));

const vehicleTypeRevenueData = computed(() => ({
    labels: r.value.byType?.labels || [],
    datasets: [{
        label: 'Revenue by Vehicle Type',
        data: r.value.byType?.data || [],
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

const metrics = computed(() => r.value.metrics || { totalRevenue: 0, averageDaily: 0, monthlyGrowth: 0, yearlyProjection: 0 });
const topPerformers = computed(() => r.value.topPerformers || []);

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
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Revenue Analytics</h1>
                        <p class="mt-1 text-sm text-gray-600">Detailed revenue analysis and insights</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <label class="text-sm text-gray-600">Year</label>

                        <!-- wrapper captures double-click to enable edit -->
                        <div @dblclick="startEditYear" class="flex items-center">
                            <!-- show select when not editing -->
                            <select v-if="!isEditingYear" v-model="selectedYear" @change="onYearChange"
                                class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>

                            <!-- show input when editing -->
                            <input
                                v-else
                                ref="yearInputRef"
                                v-model="yearInput"
                                type="number"
                                min="1900"
                                max="2100"
                                @keydown.enter.prevent="saveYear"
                                @keydown.esc.prevent="cancelEdit"
                                @blur="saveYear"
                                class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 w-24 text-center"
                                placeholder="YYYY"
                            />
                        </div>

                        <label class="text-sm text-gray-600">Month</label>
                        <select v-model="selectedMonth" @change="onMonthChange"
                            class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option v-for="m in 12" :key="m" :value="m">{{ new Date(0, m-1).toLocaleString('en-US', { month: 'short' }) }}</option>
                        </select>
                    </div>
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Revenue Trend</h3>
                        <BarChart :chart-data="monthlyRevenueData" />
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Revenue (Selected Month)</h3>
                        <LineChart :chart-data="dailyRevenueData" />
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Distribution by Vehicle Type</h3>
                        <PieChart :chart-data="vehicleTypeRevenueData" />
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Vehicles</h3>
                        <div class="space-y-4">
                            <div v-for="(vehicle, index) in topPerformers" :key="index"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ vehicle.model }}</p>
                                    <p class="text-sm text-gray-500">Rentals: {{ vehicle.rentals }}</p>
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
                            <li>Selected year: {{ selectedYear }}</li>
                            <li>Total revenue for the year: {{ formatCurrency(metrics.totalRevenue) }}</li>
                            <li>Average daily revenue (days with payments): {{ formatCurrency(metrics.averageDaily) }}</li>
                            <li>Top vehicle: {{ topPerformers.length ? topPerformers[0].model : 'N/A' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
