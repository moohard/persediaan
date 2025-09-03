document.addEventListener("DOMContentLoaded", () => {
  // Fungsi generik untuk menangani filter, load data, dan print
  const setupReportTab = (config) => {
    const form = document.getElementById(config.formId);
    const tableBody = document.getElementById(config.tableBodyId);
    const btnPrint = document.getElementById(config.btnPrintId);
    let reportData = [];

    const loadReport = async () => {
      if (!tableBody) return;
      const params = new URLSearchParams({
        start_date: document.getElementById(config.startDateId)?.value || "",
        end_date: document.getElementById(config.endDateId)?.value || "",
        status: document.getElementById(config.statusId)?.value || "semua",
      }).toString();

      try {
        const response = await apiCall("get", `${config.apiUrl}?${params}`);
        reportData = response.data;
        tableBody.innerHTML = config.renderTableRows(reportData);
      } catch (error) {
        /* Ditangani oleh apiCall */
      }
    };

    const printReport = () => {
      if (reportData.length === 0) {
        showToast("warning", "Tidak ada data untuk dicetak.");
        return;
      }
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      doc.setFontSize(16);
      doc.text(config.reportTitle, 14, 22);
      doc.setFontSize(10);
      doc.text(
        `Per Tanggal: ${new Date().toLocaleDateString("id-ID")}`,
        14,
        30
      );

      doc.autoTable({
        startY: 40,
        head: [config.tableHeaders],
        body: config.renderPdfRows(reportData),
        headStyles: { fillColor: [41, 128, 185] },
        styles: { fontSize: 8 },
        didDrawPage: function (data) {
          // [FITUR BARU] Tambahkan bagian tanda tangan di akhir halaman
          const signatureData =
            document.getElementById("signature-data").dataset;
          const pageCount = doc.internal.getNumberOfPages();

          // Hanya tambahkan di halaman terakhir
          if (data.pageNumber === pageCount) {
            const finalY = data.cursor.y + 15; // Posisi Y setelah tabel
            doc.setFontSize(10);
            doc.text("Mengetahui,", 140, finalY);
            doc.text("Kasubbag Umum dan Keuangan,", 140, finalY + 5);

            doc.text(signatureData.nama, 140, finalY + 25);
            doc.setLineWidth(0.1);
            doc.line(140, finalY + 26, 200, finalY + 26);
            doc.text(`NIP. ${signatureData.nip}`, 140, finalY + 30);
          }
        },
      });
      doc.save(
        `${config.fileNamePrefix}-${new Date().toISOString().slice(0, 10)}.pdf`
      );
    };

    form?.addEventListener("submit", (e) => {
      e.preventDefault();
      loadReport();
    });
    btnPrint?.addEventListener("click", printReport);
    loadReport();
  };

  // Konfigurasi untuk setiap tab laporan
  setupReportTab({
    formId: "filter-permintaan-form",
    tableBodyId: "laporan-permintaan-body",
    btnPrintId: "btn-print-permintaan",
    startDateId: "start-date-permintaan",
    endDateId: "end-date-permintaan",
    statusId: "status-permintaan",
    apiUrl: "/laporan/api/getPermintaanReport",
    reportTitle: "Laporan Semua Permintaan",
    fileNamePrefix: "laporan-permintaan",
    tableHeaders: ["Kode", "Tanggal", "Pemohon", "Tipe", "Status", "Penyetuju"],
    renderTableRows: (data) => {
      if (data.length === 0)
        return '<tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>';
      return data
        .map(
          (item) => `
                <tr>
                    <td>${e(item.kode_permintaan)}</td>
                    <td>${new Date(item.tanggal_permintaan).toLocaleDateString(
                      "id-ID"
                    )}</td>
                    <td>${e(item.nama_pemohon)}</td>
                    <td>${e(item.tipe_permintaan)}</td>
                    <td>${e(item.status_permintaan)}</td>
                    <td>${e(item.nama_penyetuju) || "-"}</td>
                </tr>
            `
        )
        .join("");
    },
    renderPdfRows: (data) =>
      data.map((item) => [
        item.kode_permintaan,
        new Date(item.tanggal_permintaan).toLocaleDateString("id-ID"),
        item.nama_pemohon,
        item.tipe_permintaan,
        item.status_permintaan,
        item.nama_penyetuju || "-",
      ]),
  });

  setupReportTab({
    formId: "filter-pembelian-form",
    tableBodyId: "laporan-pembelian-body",
    btnPrintId: "btn-print-pembelian",
    startDateId: "start-date-pembelian",
    endDateId: "end-date-pembelian",
    statusId: "status-pembelian",
    apiUrl: "/laporan/api/getPembelianReport",
    reportTitle: "Laporan Permintaan Pembelian",
    fileNamePrefix: "laporan-pembelian",
    tableHeaders: ["Kode", "Tanggal", "Pemohon", "Status", "Penyetuju"],
    renderTableRows: (data) => {
      if (data.length === 0)
        return '<tr><td colspan="5" class="text-center">Tidak ada data.</td></tr>';
      return data
        .map(
          (item) => `
                <tr>
                    <td>${e(item.kode_permintaan)}</td>
                    <td>${new Date(item.tanggal_permintaan).toLocaleDateString(
                      "id-ID"
                    )}</td>
                    <td>${e(item.nama_pemohon)}</td>
                    <td>${e(item.status_permintaan)}</td>
                    <td>${e(item.nama_penyetuju) || "-"}</td>
                </tr>
            `
        )
        .join("");
    },
    renderPdfRows: (data) =>
      data.map((item) => [
        item.kode_permintaan,
        new Date(item.tanggal_permintaan).toLocaleDateString("id-ID"),
        item.nama_pemohon,
        item.status_permintaan,
        item.nama_penyetuju || "-",
      ]),
  });
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
      didDrawPage: function (data) {
        // [FITUR BARU] Tambahkan bagian tanda tangan di akhir halaman
        const signatureData = document.getElementById("signature-data").dataset;
        const pageCount = doc.internal.getNumberOfPages();
        // Hanya tambahkan di halaman terakhir
        if (data.pageNumber === pageCount) {
          const finalY = data.cursor.y + 15; // Posisi Y setelah tabel
          doc.setFontSize(10);
          doc.text("Mengetahui,", 140, finalY);
          doc.text("Kasubbag Umum dan Keuangan,", 140, finalY + 5);

          doc.text(signatureData.nama, 140, finalY + 25);
          doc.setLineWidth(0.1);
          doc.line(140, finalY + 26, 200, finalY + 26);
          doc.text(`NIP. ${signatureData.nip}`, 140, finalY + 30);
        }
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
      didDrawPage: function (data) {
        // [FITUR BARU] Tambahkan bagian tanda tangan di akhir halaman
        const signatureData = document.getElementById("signature-data").dataset;
        const pageCount = doc.internal.getNumberOfPages();

        // Hanya tambahkan di halaman terakhir
        if (data.pageNumber === pageCount) {
          const finalY = data.cursor.y + 15; // Posisi Y setelah tabel
          doc.setFontSize(10);
          doc.text("Mengetahui,", 140, finalY);
          doc.text("Kasubbag Umum dan Keuangan,", 140, finalY + 5);

          doc.text(signatureData.nama, 140, finalY + 25);
          doc.setLineWidth(0.1);
          doc.line(140, finalY + 26, 200, finalY + 26);
          doc.text(`NIP. ${signatureData.nip}`, 140, finalY + 30);
        }
      },
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
