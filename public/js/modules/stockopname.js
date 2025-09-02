document.addEventListener("DOMContentLoaded", () => {
  const modal = initModal("opname-modal");
  const detailModal = initModal("detail-opname-modal");
  if (!modal) return;

  const historyTableBody = document.getElementById("history-table-body");
  const opnameItemList = document.getElementById("opname-item-list");
  let detailOpnameData = {};

  const loadHistory = async () => {
    try {
      const response = await apiCall("get", "/stockopname/api/getHistory");
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((item) => {
          html += `
                        <tr>
                            <td>${e(item.kode_opname)}</td>
                            <td>${new Date(
                              item.tanggal_opname
                            ).toLocaleDateString("id-ID")}</td>
                            <td>${e(item.nama_penanggung_jawab)}</td>
                            <td>${e(item.keterangan) || "-"}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info btn-detail" data-id="${e(
                                  item.id_opname_encrypted
                                )}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="5" class="text-center">Belum ada riwayat stock opname.</td></tr>';
      }
      historyTableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const showOpnameModal = async () => {
    try {
      const response = await apiCall("get", "/stockopname/api/getLatestStock");
      if (!response.success) {
        showToast("warning", response.message);
        return;
      }
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((item) => {
          html += `
                <tr class="opname-item-row" data-id-barang="${
                  item.id_barang
                }" data-stok-sistem-umum="${
            item.stok_umum
          }" data-stok-sistem-perkara="${item.stok_perkara}">
                    <td>${e(item.nama_barang)}</td>
                    <td class="text-center align-middle">${item.stok_umum}</td>
                    <td class="text-center align-middle">${
                      item.stok_perkara
                    }</td>
                    <td><input type="number" class="form-control form-control-sm item-fisik-umum" value="${
                      item.stok_umum
                    }" min="0"></td>
                    <td><input type="number" class="form-control form-control-sm item-fisik-perkara" value="${
                      item.stok_perkara
                    }" min="0"></td>
                    <td><input type="text" class="form-control form-control-sm item-catatan" placeholder="Catatan..."></td>
                </tr>
            `;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">Tidak ada barang aktif untuk dihitung.</td></tr>';
      }
      opnameItemList.innerHTML = html;
      document.getElementById("opname-form").reset();
      modal.show();
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const showDetailModal = async (id) => {
    try {
      const response = await apiCall("post", "/stockopname/api/getDetail", {
        id,
      });
      detailOpnameData = response.data;
      const { main, items } = detailOpnameData;

      const infoHtml = `
            <p><strong>Kode Opname:</strong> ${e(main.kode_opname)}</p>
            <p><strong>Tanggal:</strong> ${new Date(
              main.tanggal_opname
            ).toLocaleDateString("id-ID")}</p>
            <p><strong>Penanggung Jawab:</strong> ${e(
              main.nama_penanggung_jawab
            )}</p>
            <p><strong>Keterangan:</strong> ${e(main.keterangan) || "-"}</p>
        `;
      document.getElementById("detail-opname-info").innerHTML = infoHtml;

      let itemsHtml = "";
      items.forEach((item) => {
        const selisihUmum = parseInt(item.selisih_umum);
        const selisihPerkara = parseInt(item.selisih_perkara);
        const selisihUmumClass =
          selisihUmum === 0
            ? ""
            : selisihUmum > 0
            ? "text-success"
            : "text-danger";
        const selisihPerkaraClass =
          selisihPerkara === 0
            ? ""
            : selisihPerkara > 0
            ? "text-success"
            : "text-danger";

        itemsHtml += `
                <tr>
                    <td>${e(item.nama_barang)}</td>
                    <td class="text-center">${item.stok_sistem_umum}</td>
                    <td class="text-center">${item.stok_sistem_perkara}</td>
                    <td class="text-center">${item.stok_fisik_umum}</td>
                    <td class="text-center">${item.stok_fisik_perkara}</td>
                    <td class="text-center fw-bold ${selisihUmumClass}">${
          selisihUmum > 0 ? "+" : ""
        }${selisihUmum}</td>
                    <td class="text-center fw-bold ${selisihPerkaraClass}">${
          selisihPerkara > 0 ? "+" : ""
        }${selisihPerkara}</td>
                    <td>${e(item.catatan) || ""}</td>
                </tr>
            `;
      });
      document.getElementById("detail-opname-item-list").innerHTML = itemsHtml;
      detailModal.show();
    } catch (error) {
      /* error handled by apiCall */
    }
  };

  const saveOpname = async () => {
    const keterangan = document.getElementById("keterangan").value;
    const rows = document.querySelectorAll(".opname-item-row");

    const items = Array.from(rows).map((row) => ({
      id_barang: row.dataset.idBarang,
      stok_sistem_umum: row.dataset.stokSistemUmum,
      stok_sistem_perkara: row.dataset.stokSistemPerkara,
      stok_fisik_umum: row.querySelector(".item-fisik-umum").value,
      stok_fisik_perkara: row.querySelector(".item-fisik-perkara").value,
      catatan: row.querySelector(".item-catatan").value,
    }));

    const confirmed = await showConfirmation({
      text: "Anda yakin ingin menyimpan hasil stock opname ini? Stok barang akan disesuaikan.",
    });
    if (confirmed) {
      try {
        const response = await apiCall("post", "/stockopname/api/save", {
          keterangan,
          items,
        });
        showToast("success", response.message);
        modal.hide();
        loadHistory();
      } catch (error) {
        /* error handled by apiCall */
      }
    }
  };

  const printOpnameReport = () => {
    const { main, items } = detailOpnameData;
    if (!main || !items || items.length === 0) {
      showToast("warning", "Tidak ada data untuk dicetak.");
      return;
    }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: "landscape" });

    doc.setFontSize(16);
    doc.text(`Laporan Hasil Stock Opname - ${main.kode_opname}`, 14, 22);
    doc.setFontSize(10);
    doc.text(
      `Tanggal: ${new Date(main.tanggal_opname).toLocaleDateString(
        "id-ID"
      )} | Penanggung Jawab: ${main.nama_penanggung_jawab}`,
      14,
      30
    );

    doc.autoTable({
      startY: 40,
      head: [
        [
          { content: "Nama Barang", rowSpan: 2, styles: { valign: "middle" } },
          { content: "Stok Sistem", colSpan: 2, styles: { halign: "center" } },
          { content: "Stok Fisik", colSpan: 2, styles: { halign: "center" } },
          { content: "Selisih", colSpan: 2, styles: { halign: "center" } },
          { content: "Catatan", rowSpan: 2, styles: { valign: "middle" } },
        ],
        [
          { content: "Umum", styles: { halign: "center" } },
          { content: "Perkara", styles: { halign: "center" } },
          { content: "Umum", styles: { halign: "center" } },
          { content: "Perkara", styles: { halign: "center" } },
          { content: "Umum", styles: { halign: "center" } },
          { content: "Perkara", styles: { halign: "center" } },
        ],
      ],
      body: items.map((item) => [
        item.nama_barang,
        {
          content: String(item.stok_sistem_umum ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_sistem_perkara ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_fisik_umum ?? 0),
          styles: { halign: "center" },
        },
        {
          content: String(item.stok_fisik_perkara ?? 0),
          styles: { halign: "center" },
        },
        {
          content: (item.selisih_umum > 0 ? "+" : "") + item.selisih_umum,
          styles: { halign: "center" },
        },
        {
          content: (item.selisih_perkara > 0 ? "+" : "") + item.selisih_perkara,
          styles: { halign: "center" },
        },
        item.catatan || "",
      ]),
      headStyles: { fillColor: [41, 128, 185], halign: "center" },
      styles: { fontSize: 8, cellPadding: 2 },
    });
    doc.save(`hasil-stock-opname-${main.kode_opname}.pdf`);
  };

  document
    .getElementById("btn-start-opname")
    ?.addEventListener("click", showOpnameModal);
  document
    .getElementById("btn-save-opname")
    ?.addEventListener("click", saveOpname);
  historyTableBody?.addEventListener("click", (e) => {
    const target = e.target.closest(".btn-detail");
    if (target) {
      showDetailModal(target.dataset.id);
    }
  });
  document
    .getElementById("btn-print-opname")
    ?.addEventListener("click", printOpnameReport);

  loadHistory();
});
