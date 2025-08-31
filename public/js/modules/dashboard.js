document.addEventListener("DOMContentLoaded", () => {
  const usageChartCanvas = document.getElementById("usageChart");

  // Pastikan kita berada di halaman dashboard sebelum melanjutkan
  if (!usageChartCanvas) {
    return;
  }

  /**
   * Fungsi untuk memuat data dan merender grafik.
   */
  const renderUsageChart = async () => {
    try {
      // Di masa depan, Anda bisa mengganti ini dengan panggilan API:
      // const response = await apiCall('get', '/dashboard/api/getUsageData');
      // const chartData = response.data;

      // Untuk saat ini, kita gunakan data dummy
      const chartData = {
        labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"],
        values: [65, 59, 80, 81, 56, 55],
      };

      const ctx = usageChartCanvas.getContext("2d");

      // Pastikan Chart.js sudah dimuat sebelum digunakan
      if (typeof Chart === "undefined") {
        console.error(
          "Chart.js tidak dimuat. Pastikan sudah di-include di view."
        );
        usageChartCanvas.parentElement.innerHTML =
          '<p class="text-danger">Gagal memuat library grafik.</p>';
        return;
      }

      new Chart(ctx, {
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
            },
          },
        },
      });
    } catch (error) {
      console.error("Gagal merender grafik:", error);
      // Tampilkan pesan error di tempat grafik jika gagal
      usageChartCanvas.parentElement.innerHTML =
        '<p class="text-danger">Gagal memuat data grafik.</p>';
    }
  };

  // Panggil fungsi untuk merender grafik
  renderUsageChart();
});
