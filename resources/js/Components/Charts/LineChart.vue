<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
} from 'chart.js';

Chart.register(
  LineController,
  LineElement,
  PointElement,
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

// Demo data for the line chart
const demoData = {
  labels: ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'],
  datasets: [{
    label: 'Weekly Activity',
    data: [12, 19, 3, 5, 2, 3, 7],
    fill: false,
    borderColor: 'rgba(255, 99, 132, 1)',
    tension: 0.1
  }]
};

const renderChart = () => {
  if (chartInstance) chartInstance.destroy();

  const dataToRender = props.chartData || demoData;

  chartInstance = new Chart(chartCanvas.value.getContext('2d'), {
    type: 'line',
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
          text: 'Line Chart'
        }
      }
    }
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
      <span class="text-gray-700 font-semibold">Demo Data</span>
    </div>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>
