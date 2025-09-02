document.addEventListener("DOMContentLoaded", () => {
  const modal = initModal("opname-modal");
  if (!modal) return;

  const historyTableBody = document.getElementById("history-table-body");
  const opnameItemList = document.getElementById("opname-item-list");

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
                            <td>${e(item.keterangan)}</td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="4" class="text-center">Belum ada riwayat stock opname.</td></tr>';
      }
      historyTableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const showOpnameModal = async () => {
    try {
      const response = await apiCall("get", "/stockopname/api/getLatestStock");
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
                            <td class="text-center align-middle">${
                              item.stok_umum
                            }</td>
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
        /* Error ditangani apiCall */
      }
    }
  };

  document
    .getElementById("btn-start-opname")
    ?.addEventListener("click", showOpnameModal);
  document
    .getElementById("btn-save-opname")
    ?.addEventListener("click", saveOpname);

  loadHistory();
});
