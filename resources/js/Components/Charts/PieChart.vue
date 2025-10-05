<script setup>
import { onMounted, onUnmounted, ref, watch } from "vue";
import {
    Chart,
    PieController,
    ArcElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";

Chart.register(PieController, ArcElement, Title, Tooltip, Legend);

const props = defineProps({
    chartData: {
        type: Object,
        default: null,
    },
});

const chartCanvas = ref(null);
let chartInstance = null;

// Demo data for the pie chart
const demoData = {
    labels: ["Red", "Blue", "Yellow"],
    datasets: [
        {
            label: "Sample Distribution",
            data: [300, 50, 100],
            backgroundColor: [
                "rgba(255, 99, 132, 0.5)",
                "rgba(54, 162, 235, 0.5)",
                "rgba(255, 206, 86, 0.5)",
            ],
            hoverOffset: 4,
        },
    ],
};

const renderChart = () => {
    if (chartInstance) chartInstance.destroy();

    const dataToRender = props.chartData || demoData;

    chartInstance = new Chart(chartCanvas.value.getContext("2d"), {
        type: "pie",
        data: dataToRender,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Pie Chart",
                },
            },
        },
    });
};

watch(() => props.chartData, renderChart);
onMounted(renderChart);
onUnmounted(() => {
    if (chartInstance) chartInstance.destroy();
});
</script>

<template>
    <div class="relative h-96 w-full">
        <div
            v-if="!chartData"
            class="absolute inset-0 z-10 flex items-center justify-center bg-gray-200/70 backdrop-blur-sm rounded-lg"
        >
            <span class="text-gray-700 font-semibold">No Record Found</span>
        </div>
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>
