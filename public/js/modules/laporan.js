document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("laporan-stok-body");
  const btnPrint = document.getElementById("btn-print-stok");
  let reportData = [];

  const loadStockReport = async () => {
    if (!tableBody) return;
    try {
      const response = await apiCall("get", "/laporan/api/getStokBarang");
      reportData = response.data;
      let html = "";
      if (reportData.length > 0) {
        reportData.forEach((item, index) => {
          html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${e(item.kode_barang)}</td>
                            <td><a href="#" class="link-kartu-stok" data-id="${
                              item.id_barang
                            }">${e(item.nama_barang)}</a></td>
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
      headStyles: { fillColor: [41, 128, 185] },
      styles: { fontSize: 8 },
      columnStyles: {
        0: { cellWidth: 10 },
        5: { halign: "center" },
        6: { halign: "center" },
        7: { halign: "center" },
      },
    });
    doc.save(
      `laporan-stok-barang-${new Date().toISOString().slice(0, 10)}.pdf`
    );
  };

  btnPrint?.addEventListener("click", printStockReport);

  const barangSelect = document.getElementById("barang-select");
  const kartuStokTableBody = document.getElementById("kartu-stok-body");
  const btnPrintKartuStok = document.getElementById("btn-print-kartu-stok");
  let kartuStokReportData = [];

  const loadStockCard = async () => {
    if (!kartuStokTableBody || !barangSelect.value) {
      kartuStokTableBody.innerHTML =
        '<tr><td colspan="8" class="text-center">Silakan pilih barang untuk melihat riwayat.</td></tr>';
      btnPrintKartuStok.classList.add("d-none");
      return;
    }

    try {
      const response = await apiCall(
        "get",
        `/laporan/api/getKartuStok?id_barang=${barangSelect.value}`
      );
      kartuStokReportData = response.data;
      let html = "";
      if (kartuStokReportData.length > 0) {
        kartuStokReportData.forEach((item) => {
          const jumlahUbahClass =
            item.jumlah_ubah >= 0 ? "text-success" : "text-danger";
          const jumlahUbahText =
            item.jumlah_ubah > 0 ? `+${item.jumlah_ubah}` : item.jumlah_ubah;
          html += `
                        <tr>
                            <td>${new Date(item.tanggal_log).toLocaleString(
                              "id-ID"
                            )}</td>
                            <td>${e(item.keterangan) || "-"}</td>
                            <td class="text-center fw-bold ${jumlahUbahClass}">${jumlahUbahText}</td>
                            <td class="text-center">${e(
                              item.stok_sebelum_umum
                            )}</td>
                            <td class="text-center">${e(
                              item.stok_sebelum_perkara
                            )}</td>
                            <td class="text-center">${e(
                              item.stok_sesudah_umum
                            )}</td>
                            <td class="text-center">${e(
                              item.stok_sesudah_perkara
                            )}</td>
                            <td>${e(item.nama_pengguna) || "Sistem"}</td>
                        </tr>
                    `;
        });
        btnPrintKartuStok.classList.remove("d-none");
      } else {
        html =
          '<tr><td colspan="8" class="text-center">Tidak ada riwayat pergerakan untuk barang ini.</td></tr>';
        btnPrintKartuStok.classList.add("d-none");
      }
      kartuStokTableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const printStockCard = () => {
    if (kartuStokReportData.length === 0) {
      showToast("warning", "Tidak ada data untuk dicetak.");
      return;
    }

    const selectedBarangText =
      barangSelect.options[barangSelect.selectedIndex].text;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: "landscape" });

    doc.setFontSize(16);
    doc.text(`Laporan Kartu Stok: ${selectedBarangText}`, 14, 22);
    doc.setFontSize(10);
    doc.text(`Per Tanggal: ${new Date().toLocaleDateString("id-ID")}`, 14, 30);

    doc.autoTable({
      startY: 40,
      head: [
        [
          {
            content: "Tanggal",
            rowSpan: 2,
            styles: { halign: "center", valign: "middle" },
          },
          {
            content: "Keterangan",
            rowSpan: 2,
            styles: { halign: "center", valign: "middle" },
          },
          {
            content: "Jumlah Ubah",
            rowSpan: 2,
            styles: { halign: "center", valign: "middle" },
          },
          { content: "Stok Awal", colSpan: 2, styles: { halign: "center" } },
          { content: "Stok Akhir", colSpan: 2, styles: { halign: "center" } },
          {
            content: "Pengguna",
            rowSpan: 2,
            styles: { halign: "center", valign: "middle" },
          },
        ],
        [
          { content: "Umum", styles: { halign: "center" } },
          { content: "Perkara", styles: { halign: "center" } },
          { content: "Umum", styles: { halign: "center" } },
          { content: "Perkara", styles: { halign: "center" } },
        ],
      ],
      body: kartuStokReportData.map((item) => [
        new Date(item.tanggal_log).toLocaleString("id-ID"),
        item.keterangan || "-",
        {
          content: String(item.jumlah_ubah ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_sebelum_umum ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_sebelum_perkara ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_sesudah_umum ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_sesudah_perkara ?? 0),
          styles: { halign: "center" },
        },
        item.nama_pengguna || "Sistem",
      ]),
      headStyles: { fillColor: [41, 128, 185], halign: "center" },
      styles: { fontSize: 8, cellPadding: 2 },
    });

    doc.save(
      `kartu-stok-${selectedBarangText.replace(/\s+/g, "-")}-${new Date()
        .toISOString()
        .slice(0, 10)}.pdf`
    );
  };

  tableBody?.addEventListener("click", (e) => {
    const target = e.target.closest(".link-kartu-stok");
    if (target) {
      e.preventDefault();
      const barangId = target.dataset.id;

      const kartuStokTab = new bootstrap.Tab(
        document.getElementById("kartu-stok-tab")
      );
      kartuStokTab.show();

      barangSelect.value = barangId;
      loadStockCard();
    }
  });

  barangSelect?.addEventListener("change", loadStockCard);
  btnPrintKartuStok?.addEventListener("click", printStockCard);

  loadStockReport();
});
