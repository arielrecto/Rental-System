<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
} from 'chart.js';

// Register the necessary components for Chart.js
Chart.register(
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
);

const props = defineProps({
  chartData: {
    type: Object,
    default: null
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

// Demo data to display when no real data is provided
const demoData = {
  labels: ['January', 'February', 'March', 'April', 'May'],
  datasets: [{
    label: 'Sample Revenue',
    data: [65, 59, 80, 81, 56],
    backgroundColor: 'rgba(54, 162, 235, 0.5)',
    borderColor: 'rgba(54, 162, 235, 1)',
    borderWidth: 1
  }]
};

const renderChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
  }

  const dataToRender = props.chartData || demoData;

  chartInstance = new Chart(chartCanvas.value.getContext('2d'), {
    type: 'bar',
    data: dataToRender,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Bar Chart'
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
};

// Watch for changes in the prop to re-render the chart
watch(() => props.chartData, renderChart);

onMounted(renderChart);

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>

<template>
  <div class="relative h-96 w-full">
    <div
      v-if="!chartData"
      class="absolute inset-0 z-10 flex items-center justify-center bg-gray-200/70 backdrop-blur-sm rounded-lg"
    >
      <span class="text-gray-700 font-semibold">No Data Available</span>
    </div>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>
