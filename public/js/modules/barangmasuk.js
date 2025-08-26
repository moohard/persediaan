document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("barangmasuk-table-body");
  if (!tableBody) return;

  const loadPurchasedRequests = async () => {
    try {
      const response = await axios.get("/barangmasuk/api/getPurchased");
      let html = "";
      if (response.data.data.length > 0) {
        response.data.data.forEach((item) => {
          html += `
                        <tr>
                            <td>${item.kode_permintaan}</td>
                            <td>${new Date(
                              item.tanggal_diproses
                            ).toLocaleString("id-ID")}</td>
                            <td>${item.nama_pemohon}</td>
                            <td>${item.jumlah_item}</td>
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
          '<tr><td colspan="5" class="text-center">Tidak ada barang yang perlu diterima.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      console.error(error);
      Swal.fire("Error", "Gagal memuat data.", "error");
    }
  };

  tableBody.addEventListener("click", (e) => {
    const target = e.target.closest(".btn-process-receipt");
    if (!target) return;

    const id = target.dataset.id;

    Swal.fire({
      title: "Proses Penerimaan Barang?",
      text: "Stok akan diperbarui dan permintaan ini akan ditandai selesai.",
      icon: "info",
      showCancelButton: true,
      confirmButtonColor: "#198754",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, proses!",
      cancelButtonText: "Batal",
    }).then(async (result) => {
      if (result.isConfirmed) {
        try {
          const response = await axios.post("/barangmasuk/api/processReceipt", {
            id: id,
          });
          if (response.data.success) {
            Swal.fire("Berhasil!", response.data.message, "success");
            loadPurchasedRequests();
          }
        } catch (error) {
          Swal.fire(
            "Gagal!",
            error.response?.data?.message || "Terjadi kesalahan.",
            "error"
          );
        }
      }
    });
  });

  loadPurchasedRequests();
});
