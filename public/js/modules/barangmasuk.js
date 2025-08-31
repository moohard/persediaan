document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("penerimaan-table-body");
  const receiptModal = initModal("receipt-modal");
  if (!receiptModal) return;

  const receiptItemList = document.getElementById("receipt-item-list");
  const btnSaveReceipt = document.getElementById("btn-save-receipt");

  const loadRequests = async () => {
    if (!tableBody) return;
    try {
      const response = await apiCall(
        "get",
        "/barangmasuk/api/getPurchasedRequests"
      );
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((item) => {
          html += `
                        <tr>
                            <td>${e(item.kode_permintaan)}</td>
                            <td>${new Date(
                              item.tanggal_permintaan
                            ).toLocaleDateString("id-ID")}</td>
                            <td>${e(item.nama_pemohon)}</td>
                            <td class="text-center">${e(item.jumlah_item)}</td>
                            <td><span class="badge bg-info">${e(
                              item.status_permintaan
                            )}</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-process-receipt" data-id="${
                                  item.id_permintaan_encrypted
                                }">
                                    <i class="bi bi-box-arrow-in-down"></i> Proses Penerimaan
                                </button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">Tidak ada barang yang perlu diterima.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani oleh apiCall */
    }
  };

  const showReceiptModal = async (id) => {
    try {
      const response = await apiCall("get", `/barangmasuk/api/getDetail/${id}`);
      document.getElementById("id_permintaan_encrypted").value = id;

      let html = "";
      response.data.forEach((item) => {
        const jumlahDiterima = item.jumlah_disetujui || 0;
        html += `
                    <tr class="receipt-item-row" data-diterima="${jumlahDiterima}">
                        <td>
                            ${e(item.nama_barang)}
                            <input type="hidden" class="item-id-detail-permintaan" value="${
                              item.id_detail_permintaan
                            }">
                            <input type="hidden" class="item-id-barang" value="${
                              item.id_barang || ""
                            }">
                            <input type="hidden" class="item-nama-barang-custom" value="${
                              e(item.nama_barang_custom) || ""
                            }">
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge bg-success">${jumlahDiterima}</span>
                        </td>
                        <td>
                            <input type="number" class="form-control item-jumlah-umum" value="0" min="0" max="${jumlahDiterima}">
                        </td>
                        <td>
                             <input type="number" class="form-control item-jumlah-perkara" value="0" min="0" max="${jumlahDiterima}">
                        </td>
                    </tr>
                `;
      });
      receiptItemList.innerHTML = html;
      receiptModal.show();
    } catch (error) {
      /* Error ditangani oleh apiCall */
    }
  };

  receiptItemList?.addEventListener("input", (e) => {
    if (
      e.target.classList.contains("item-jumlah-umum") ||
      e.target.classList.contains("item-jumlah-perkara")
    ) {
      const row = e.target.closest(".receipt-item-row");
      const jumlahDiterima = parseInt(row.dataset.diterima, 10);
      const jumlahUmum =
        parseInt(row.querySelector(".item-jumlah-umum").value, 10) || 0;
      const jumlahPerkara =
        parseInt(row.querySelector(".item-jumlah-perkara").value, 10) || 0;

      if (jumlahUmum + jumlahPerkara > jumlahDiterima) {
        // Kurangi nilai input terakhir agar total tidak melebihi
        e.target.value =
          e.target.value - (jumlahUmum + jumlahPerkara - jumlahDiterima);
        showToast("warning", "Jumlah alokasi melebihi jumlah diterima.");
      }
    }
  });

  btnSaveReceipt?.addEventListener("click", async () => {
    const id_permintaan_encrypted = document.getElementById(
      "id_permintaan_encrypted"
    ).value;
    const rows = document.querySelectorAll(".receipt-item-row");
    let isValid = true;

    const items = Array.from(rows).map((row) => {
      const jumlahDiterima = parseInt(row.dataset.diterima, 10);
      const jumlahUmum =
        parseInt(row.querySelector(".item-jumlah-umum").value, 10) || 0;
      const jumlahPerkara =
        parseInt(row.querySelector(".item-jumlah-perkara").value, 10) || 0;

      // Validasi jika jumlah alokasi tidak sama dengan jumlah diterima
      if (jumlahUmum + jumlahPerkara !== jumlahDiterima) {
        isValid = false;
      }

      return {
        id_detail_permintaan: row.querySelector(".item-id-detail-permintaan")
          .value,
        id_barang: row.querySelector(".item-id-barang").value,
        nama_barang_custom: row.querySelector(".item-nama-barang-custom").value,
        jumlah_diterima: jumlahDiterima,
        jumlah_umum: jumlahUmum,
        jumlah_perkara: jumlahPerkara,
      };
    });

    if (!isValid) {
      showToast(
        "warning",
        "Total alokasi (Umum + Perkara) harus sama dengan jumlah yang diterima untuk setiap item."
      );
      return;
    }

    const confirmed = await showConfirmation({
      text: "Apakah Anda yakin ingin menyimpan data penerimaan ini?",
    });
    if (confirmed) {
      try {
        const response = await apiCall(
          "post",
          "/barangmasuk/api/processReceipt",
          { id_permintaan_encrypted, items }
        );
        showToast("success", response.message);
        receiptModal.hide();
        loadRequests();
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    }
  });

  tableBody?.addEventListener("click", (e) => {
    const target = e.target.closest(".btn-process-receipt");
    if (target) {
      showReceiptModal(target.dataset.id);
    }
  });

  loadRequests();
});
