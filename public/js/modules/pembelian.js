document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("pembelian-table-body");

  const loadPurchaseRequests = async () => {
    if (!tableBody) return;
    try {
      const response = await apiCall(
        "get",
        "/pembelian/api/getPurchaseRequests"
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
                            <td><small>${e(item.nama_items)}</small></td>
                            <td><span class="badge bg-warning">${e(
                              item.status_permintaan
                            )}</span></td>
                            <td>
                                <a href="/permintaan" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Detail</a>
                                <button class="btn btn-sm btn-success btn-mark-purchased" data-id="${
                                  item.id_permintaan_encrypted
                                }" data-kode="${e(
            item.kode_permintaan
          )}"><i class="bi bi-check-circle"></i> Tandai Dibeli</button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">Tidak ada permintaan pembelian yang perlu diproses.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  tableBody?.addEventListener("click", async (e) => {
    const target = e.target.closest(".btn-mark-purchased");
    if (target) {
      const confirmed = await showConfirmation({
        text: `Anda yakin barang untuk permintaan ${target.dataset.kode} sudah dibeli?`,
      });
      if (confirmed) {
        try {
          const response = await apiCall(
            "post",
            "/pembelian/api/markAsPurchased",
            { id: target.dataset.id }
          );
          showToast("success", response.message);
          loadPurchaseRequests();
        } catch (error) {
          /* Error ditangani apiCall */
        }
      }
    }
  });

  loadPurchaseRequests();
});
