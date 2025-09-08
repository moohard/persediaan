document.addEventListener("DOMContentLoaded", () => {
  const usageChartCanvas = document.getElementById("usageChart");

  if (!usageChartCanvas) {
    return; // Keluar jika tidak di halaman dashboard admin/pimpinan
  }

  let myChart; // Variabel untuk menyimpan instance Chart

  const renderChart = (chartData) => {
    const ctx = usageChartCanvas.getContext("2d");

    // Hancurkan chart lama jika ada, untuk pembaruan data
    if (myChart) {
      myChart.destroy();
    }

    myChart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: chartData.labels,
        datasets: [
          {
            label: "Jumlah Barang Keluar",
            data: chartData.values,
            backgroundColor: "rgba(54, 162, 235, 0.5)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 10, // Atur skala Y
            },
          },
        },
      },
    });
  };

  const loadDashboardData = async () => {
    try {
      const response = await apiCall("get", "/dashboard/api/getStats");
      const stats = response.data;
console.log(stats);
      // Update kartu ringkasan
      document.getElementById("total-barang").textContent =
        stats.summary.total_barang;
      document.getElementById("permintaan-bulan-ini").textContent =
        stats.summary.permintaan_bulan_ini;
      document.getElementById("stok-kritis").textContent =
        stats.summary.stok_kritis;

      // Render grafik dengan data dinamis
      renderChart(stats.chart);
    } catch (error) {
      console.error("Gagal memuat data dashboard:", error);
      document.getElementById("summary-cards").innerHTML =
        '<p class="text-danger">Gagal memuat data ringkasan.</p>';
      usageChartCanvas.parentElement.innerHTML =
        '<p class="text-danger">Gagal memuat data grafik.</p>';
    }
  };

  // Panggil fungsi untuk memuat data saat halaman dimuat
  loadDashboardData();
});
