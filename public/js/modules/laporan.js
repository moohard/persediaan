document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("laporan-stok-body");
  const btnPrint = document.getElementById("btn-print-stok");
  let reportData = []; // Simpan data laporan untuk dicetak

  const loadStockReport = async () => {
    if (!tableBody) return;
    try {
      const response = await apiCall("get", "/laporan/api/getStokBarang");
      reportData = response.data; // Simpan data
      let html = "";
      if (reportData.length > 0) {
        reportData.forEach((item, index) => {
          html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${e(item.kode_barang)}</td>
                            <td>${e(item.nama_barang)}</td>
                            <td>${e(item.nama_kategori) || "-"}</td>
                            <td>${e(item.nama_satuan) || "-"}</td>
                            <td class="text-center">${e(item.stok_umum)}</td>
                            <td class="text-center">${e(item.stok_perkara)}</td>
                            <td class="text-center">${e(item.stok_total)}</td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="8" class="text-center">Tidak ada data stok barang.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const printStockReport = () => {
    if (reportData.length === 0) {
      showToast("warning", "Tidak ada data untuk dicetak.");
      return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Judul Laporan
    doc.setFontSize(16);
    doc.text("Laporan Stok Barang ATK", 14, 22);
    doc.setFontSize(10);
    doc.text(
      `Pengadilan Agama Penajam - Per Tanggal: ${new Date().toLocaleDateString(
        "id-ID"
      )}`,
      14,
      30
    );

    // Buat tabel
    doc.autoTable({
      startY: 40,
      head: [
        [
          "No.",
          "Kode Barang",
          "Nama Barang",
          "Kategori",
          "Satuan",
          "Stok Umum",
          "Stok Perkara",
          "Total",
        ],
      ],
      body: reportData.map((item, index) => [
        index + 1,
        item.kode_barang,
        item.nama_barang,
        item.nama_kategori || "-",
        item.nama_satuan || "-",
        item.stok_umum,
        item.stok_perkara,
        item.stok_total,
      ]),
      headStyles: { fillColor: [41, 128, 185] }, // Warna biru
      styles: { fontSize: 8 },
      columnStyles: {
        0: { cellWidth: 10 },
        5: { halign: "center" },
        6: { halign: "center" },
        7: { halign: "center" },
      },
    });

    // Simpan PDF
    doc.save(
      `laporan-stok-barang-${new Date().toISOString().slice(0, 10)}.pdf`
    );
  };

  btnPrint?.addEventListener("click", printStockReport);

  loadStockReport();
  const barangSelect = document.getElementById("barang-select");
  const kartuStokTableBody = document.getElementById("kartu-stok-body");
  const btnPrintKartuStok = document.getElementById("btn-print-kartu-stok");
  let kartuStokReportData = [];
  async function loadKartuStok(idBarang) {
    try {
      const response = await apiCall(
        "get",
        `/laporan/api/getKartuStok?id_barang=${idBarang}`
      );
      console.log("Response Kartu Stok:", response); // Debug log
      renderKartuStok(response.data);
    } catch (error) {
      console.error("Error loading kartu stok:", error);
      document.getElementById("kartu-stok-body").innerHTML =
        '<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>';
    }
  }
  function renderKartuStok(data) {
    let html = "";
    if (data && data.length > 0) {
      data.forEach((item) => {
        html += `
                    <tr>
                        <td>${formatDate(item.tanggal_log)}</td>
                        <td>${e(item.jenis_transaksi)}</td>
                        <td>${e(item.keterangan)}</td>
                        <td class="text-center">${item.jumlah_ubah}</td>
                        <td class="text-center">${item.stok_awal}</td>
                        <td class="text-center">${item.stok_akhir}</td>
                        <td>${e(item.nama_pengguna)}</td>
                    </tr>
                `;
      });
    } else {
      html =
        '<tr><td colspan="7" class="text-center">Tidak ada data transaksi.</td></tr>';
    }
    document.getElementById("kartu-stok-body").innerHTML = html;
  }

  function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID");
  }
  const printStockCard = () => {
    if (kartuStokReportData.length === 0) {
      showToast("warning", "Tidak ada data untuk dicetak.");
      return;
    }

    const selectedBarangText =
      barangSelect.options[barangSelect.selectedIndex].text;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(16);
    doc.text(`Laporan Kartu Stok: ${selectedBarangText}`, 14, 22);
    doc.setFontSize(10);
    doc.text(`Per Tanggal: ${new Date().toLocaleDateString("id-ID")}`, 14, 30);

    doc.autoTable({
      startY: 40,
      head: [
        [
          "Tanggal",
          "Transaksi",
          "Keterangan",
          "Jumlah",
          "Awal",
          "Akhir",
          "Pengguna",
        ],
      ],
      body: kartuStokReportData.map((item) => [
        new Date(item.tanggal_log).toLocaleString("id-ID"),
        item.jenis_transaksi,
        item.keterangan || "-",
        item.jumlah_ubah,
        item.stok_sebelum_total,
        item.stok_sesudah_total,
        item.nama_pengguna || "Sistem",
      ]),
      headStyles: { fillColor: [41, 128, 185] },
      styles: { fontSize: 8 },
    });

    doc.save(
      `kartu-stok-${selectedBarangText.replace(/\s+/g, "-")}-${new Date()
        .toISOString()
        .slice(0, 10)}.pdf`
    );
  };

  barangSelect?.addEventListener("change", (e) => {
    const idBarang = e.target.value;
    console.log("Barang dipilih:", idBarang); // Debug log

    if (idBarang) {
      loadKartuStok(idBarang);
    } else {
      // Kosongkan tabel jika tidak ada pilihan
      document.getElementById("kartu-stok-body").innerHTML =
        '<tr><td colspan="7" class="text-center">Silakan pilih barang untuk melihat riwayat.</td></tr>';
    }
  });
  btnPrintKartuStok?.addEventListener("click", printStockCard);
});
