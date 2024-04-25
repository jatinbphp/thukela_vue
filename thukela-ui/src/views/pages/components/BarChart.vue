<template>
  <canvas :id="id" class="chart-canvas" :height="height"></canvas>
</template>

<script>
import Chart from "chart.js/auto";
export default {
  name: "BarChart",
  props: {
    id: {
      type: String,
      default: "bar-chart",
    },
    height: {
      type: [String, Number],
      default: "742",
    },
    chart: {
      type: Object,
      required: true,
      labels: Array,
      datasets: {
        type: Object,
        required: true,
        label: String,
        data: Array,
      },
    },
  },
  mounted() {
    // Bar chart
    var ctx = document.getElementById(this.id).getContext("2d");

    let chartStatus = Chart.getChart(this.id);
    if (chartStatus != undefined) {
      chartStatus.destroy();
    }

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: this.chart.labels,
        datasets: this.chart.datasets,
      },
      options: {
        responsive: true,
        legend: {
          position: "right",
          display: false
        },
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        title: {
          display: true,
          text: "Account Id: ",
          fontColor: "#050505",
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
            },
            ticks: {
              display: true,
              padding: 10,
              color: "#9ca2b7",
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: true,
              drawTicks: true,
            },
            ticks: {
              display: true,
              color: "#9ca2b7",
              padding: 10,
            },
          },
        },
      },
    });
  },
};
</script>
