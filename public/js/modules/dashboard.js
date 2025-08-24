$(document).ready(function () {
  console.log("Dashboard module JS loaded.");

  // Muat library Chart.js sebelum digunakan
  const chartJsScript = document.createElement("script");
  chartJsScript.src = "https://cdn.jsdelivr.net/npm/chart.js";
  chartJsScript.onload = function () {
    if ($("#usageChart").length) {
      const ctx = document.getElementById("usageChart").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"],
          datasets: [
            {
              label: "Jumlah Barang Keluar",
              data: [65, 59, 80, 81, 56, 55],
              backgroundColor: "rgba(54, 162, 235, 0.5)",
            },
          ],
        },
      });
    }
  };
  document.head.appendChild(chartJsScript);
});
