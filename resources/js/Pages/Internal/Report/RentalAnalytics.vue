<script setup>
import { toRefs, ref, computed, watch, nextTick } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';

const props = defineProps({
    analytics: {
        type: Object,
        required: true
    }
});

const { analytics: a } = toRefs(props);

// keep selectedYear in sync with incoming analytics
const selectedYear = ref(a.value?.selectedYear || new Date().getFullYear());

// editable state for year control
const isEditingYear = ref(false);
const yearInput = ref(selectedYear.value);
const yearInputRef = ref(null);

// keep selectedYear updated if props.analytics changes
watch(() => a.value?.selectedYear, (v) => {
    if (v) {
        selectedYear.value = v;
        if (!isEditingYear.value) yearInput.value = v;
    }
});

// build a small range of years for filter (selectedYear down to -4)
const years = computed(() => {
    const y = Number(selectedYear.value) || new Date().getFullYear();
    const out = [];
    for (let i = 0; i < 6; i++) out.push(y - i);
    return out;
});

const onYearChange = () => {
    // keep route name you use in web.php. This matches the existing page route.
    router.get(route('internal.report.rental-analytics'), { year: selectedYear.value }, { preserveState: true, replace: true });
};

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
        // invalid -> revert and close edit
        yearInput.value = selectedYear.value;
        isEditingYear.value = false;
        return;
    }
    selectedYear.value = parsed;
    isEditingYear.value = false;
    onYearChange();
};

// make all chart payloads and metrics reactive — computed so they update when props.analytics changes
const metrics = computed(() => {
    const m = a.value?.metrics || {};
    return {
        totalRentals: m.totalRentals ?? 0,
        averageRentalDuration: m.averageRentalDuration ?? 0,
        peakUtilization: m.peakUtilization ?? '0%',
        popularDays: m.popularDays ?? 'N/A'
    };
});

const rentalFrequencyData = computed(() => {
    const rf = a.value?.rentalFrequency || { labels: [], data: [] };
    return {
        labels: rf.labels || [],
        datasets: [{
            label: 'Number of Rentals',
            data: rf.data || [],
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
});

const monthlyRentalData = computed(() => {
    const mon = a.value?.monthly || { labels: [], data: [] };
    return {
        labels: mon.labels || [],
        datasets: [{
            label: `Number of Rentals ${selectedYear.value}`,
            data: mon.data || [],
            backgroundColor: 'rgba(239, 68, 68, 0.5)',
            borderColor: 'rgba(239, 68, 68, 1)',
            borderWidth: 1
        }]
    };
});

const dailyRentalData = computed(() => {
    const d = a.value?.daily || { labels: [], data: [] };
    return {
        labels: d.labels || [],
        datasets: [{
            label: 'Average Daily Rentals',
            data: d.data || [],
            fill: false,
            borderColor: 'rgba(239, 68, 68, 1)',
            tension: 0.1
        }]
    };
});

const topVehicles = computed(() => a.value?.topVehicles || []);
const lowPerformingVehicles = computed(() => a.value?.lowPerformingVehicles || []);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
};
</script>

<template>
    <InternalLayout>
        <Head title="Rental Analytics" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Rental Analytics</h1>
                        <p class="mt-1 text-sm text-gray-600">Comprehensive rental performance analysis</p>
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
                    </div>
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
                            <li v-if="topVehicles.length">Top vehicle: {{ topVehicles[0].model }} with {{ topVehicles[0].rentals }} rentals.</li>
                            <li>Average rental duration is {{ metrics.averageRentalDuration }} days.</li>
                            <li>Peak utilization: {{ metrics.peakUtilization }}.</li>
                            <li>Most popular day(s): {{ metrics.popularDays }}.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
