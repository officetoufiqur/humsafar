<script setup lang="ts">
import Chart, { type ChartConfiguration } from 'chart.js/auto';
import { onMounted, onBeforeUnmount, ref } from 'vue';

const chart = ref<HTMLCanvasElement | null>(null);
let chartInstance: Chart | null = null;

const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const data = {
  labels: labels,
  datasets: [
    {
      label: 'Income',
      data: [15000, 18000, 22000, 5000, 30000, 35000, 40000],
      borderColor: 'rgb(75, 192, 192)',
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      tension: 0.4,
      fill: true
    },
    {
      label: 'Expense',
      data: [10000, 12000, 18000, 15000, 20000, 22000, 23120],
      borderColor: 'rgb(255, 99, 132)',
      backgroundColor: 'rgba(255, 99, 132, 0.2)',
      tension: 0.4,
      fill: true
    }
  ]
};

const config: ChartConfiguration<'line'> = {
  type: 'line',
  data: data,
  options: {
    responsive: true,
    plugins: {
      title: {
        display: true,
        text: 'Revenue Statistics',
        position: 'top' as const,
        align: 'start' as const,
        font: {
          size: 18
        }
      },
      legend: {
        position: 'top' as const,
        align: 'end' as const,
        labels: {
          usePointStyle: true,
          boxWidth: 20
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
    elements: {
      point: {
        radius: 0
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
    <div style="width: 100%; margin: 0 auto;">
        <canvas ref="chart"></canvas>
    </div>
</template>