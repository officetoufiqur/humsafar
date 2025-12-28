<script setup lang="ts">
import Chart, { type ChartConfiguration } from 'chart.js/auto';
import { onMounted, onBeforeUnmount, ref } from 'vue';

const chart = ref<HTMLCanvasElement | null>(null);
let chartInstance: Chart | null = null;

const labels = ['Grocery', 'Fashion', 'Sports', 'Electronics'];
const data = {
  labels: labels,
  datasets: [
    {
      label: 'Revenue',
      data: [35000, 40000, 25000, 30000],
      backgroundColor: '#4bc0c0',
      borderColor: '#4bc0c0',
      borderWidth: 1,
      borderRadius: 4
    },
    {
      label: 'Profit',
      data: [15000, 22000, 12000, 18000],
      backgroundColor: '#ff6384',
      borderColor: '#ff6384',
      borderWidth: 1,
      borderRadius: 4
    }
  ]
};

const config: ChartConfiguration<'bar'> = {
  type: 'bar',
  data: data,
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      title: {
        display: true,
        text: 'Products Revenue Breakdown',
        align: 'start' as const,
        font: {
          size: 18
        },
        padding: {
          top: 10,
          bottom: 20
        }
      },
      legend: {
        position: 'top' as const,
        align: 'end' as const,
        labels: {
          usePointStyle: true,
          boxWidth: 6,
          padding: 20
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        max: 40000,
        ticks: {
          callback: function(value: number | string) {
            if (typeof value === 'number') {
              return '$' + value/1000 + 'k';
            }
            return value;
          },
          stepSize: 10000
        }
      },
      x: {
        grid: {
          display: false
        }
      }
    },
    datasets: {
      bar: {
        categoryPercentage: 0.6,
        barPercentage: 0.9
      }
    }
  }
};

onMounted(() => {
    if (chart.value) {
        chartInstance = new Chart(chart.value, config);
    }
});

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>

<template>
    <div style="width: 100%; height: 250px;">
        <canvas ref="chart"></canvas>
    </div>
</template>