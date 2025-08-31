document.addEventListener("DOMContentLoaded", () => {
  const formModal = initModal("form-modal");
  const detailModal = initModal("detail-modal");
  if (!formModal || !detailModal) return;

  const tableBody = document.getElementById("permintaan-table-body");
  let availableItems = [];

  // =====================================================================
  // Fungsi Pemuatan Data (Loaders)
  // =====================================================================

  const loadRequests = async () => {
    try {
      const response = await apiCall("get", "/permintaan/api/getAll");
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
                            <td><span class="badge bg-${
                              item.tipe_permintaan === "stok"
                                ? "info"
                                : "warning"
                            }">${e(item.tipe_permintaan)}</span></td>
                            <td><span class="badge bg-${getStatusColor(
                              item.status_permintaan
                            )}">${e(item.status_permintaan)}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary btn-detail" data-id="${
                                  item.id_permintaan_encrypted
                                }"><i class="bi bi-eye"></i> Detail</button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">Belum ada data permintaan.</td></tr>';
      }
      if (tableBody) tableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani oleh apiCall */
    }
  };

  const loadAvailableItems = async () => {
    try {
      const response = await apiCall(
        "get",
        "/permintaan/api/getAvailableItems"
      );
      availableItems = response.data;
    } catch (error) {
      console.error("Gagal memuat item yang tersedia:", error);
    }
  };

  // =====================================================================
  // Fungsi Interaksi Modal & Form
  // =====================================================================

  const createItemRow = () => {
    const tr = document.createElement("tr");
    tr.classList.add("item-row");

    const options = availableItems
      .map(
        (item) =>
          `<option value="${item.id_barang}" data-stok="${item.stok_total}">${e(
            item.nama_barang
          )} (Stok: ${item.stok_total})</option>`
      )
      .join("");

    tr.innerHTML = `
            <td><input class="form-check-input item-is-custom" type="checkbox"></td>
            <td>
                <select class="form-select item-barang" name="id_barang" required>
                    <option value="">Pilih Barang...</option>
                    ${options}
                </select>
                <input type="text" class="form-control item-barang-custom d-none" placeholder="Nama Barang Baru">
            </td>
            <td><input type="number" class="form-control item-jumlah" name="jumlah" required min="1"></td>
            <td><button type="button" class="btn btn-sm btn-danger btn-remove-item"><i class="bi bi-trash"></i></button></td>
        `;
    return tr;
  };

  const showDetailModal = async (id) => {
    try {
      const response = await apiCall("get", `/permintaan/api/getDetail/${id}`);
      const { main, items } = response.data;

      const userRole = document.body.dataset.userRole;
      const canApprove = userRole === "Pimpinan" || userRole === "Developer";
      let itemsHtml = '<ul class="list-group">';
      items.forEach((item) => {
        let approvalInfo = "";
        let statusBadge = item.status_item
          ? `<span class="badge bg-secondary">${e(item.status_item)}</span>`
          : "";

        if (main.status_permintaan === "Diajukan" && canApprove) {
          let maxValue;
          if (main.tipe_permintaan === "stok") {
            maxValue = !isNaN(parseInt(item.stok_total))
              ? item.stok_total
              : item.jumlah_diminta;
          } else {
            maxValue = item.jumlah_diminta;
          }
          approvalInfo = `<input type="number" class="form-control form-control-sm ms-2 item-approval-jumlah" style="width: 80px;" value="${item.jumlah_diminta}" min="0" max="${maxValue}" data-detail-id="${item.id_detail_permintaan}">`;
        } else if (item.jumlah_disetujui !== null) {
          approvalInfo = `<span class="ms-2">Disetujui: <strong>${e(
            item.jumlah_disetujui
          )} ${e(item.nama_satuan) || ""}</strong></span>`;
        }

        itemsHtml += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            ${e(item.nama_barang) || "(Barang tidak ditemukan)"}
                            <small class="d-block text-muted">Diminta: ${e(
                              item.jumlah_diminta
                            )} ${e(item.nama_satuan) || ""}</small>
                        </div>
                        <div class="d-flex align-items-center">
                           ${statusBadge} ${approvalInfo}
                        </div>
                    </li>`;
      });
      itemsHtml += "</ul>";

      let detailHtml = `
                <p><strong>Kode:</strong> ${e(main.kode_permintaan)}</p>
                <p><strong>Pemohon:</strong> ${e(main.nama_pemohon)}</p>
                <p><strong>Tanggal:</strong> ${new Date(
                  main.tanggal_permintaan
                ).toLocaleDateString("id-ID")}</p>
                <p><strong>Tipe:</strong> <span class="badge bg-${
                  main.tipe_permintaan === "stok" ? "info" : "warning"
                }">${e(main.tipe_permintaan)}</span></p>
                <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(
                  main.status_permintaan
                )}">${e(main.status_permintaan)}</span></p>
                <p><strong>Catatan Pemohon:</strong> ${
                  e(main.catatan_pemohon) || "-"
                }</p>
                ${
                  main.nama_penyetuju
                    ? `<p><strong>Disetujui oleh:</strong> ${e(
                        main.nama_penyetuju
                      )} pada ${new Date(main.tanggal_diproses).toLocaleString(
                        "id-ID"
                      )}</p>`
                    : ""
                }
                ${
                  main.catatan_penyetuju
                    ? `<p><strong>Catatan Persetujuan:</strong> ${e(
                        main.catatan_penyetuju
                      )}</p>`
                    : ""
                }
                <h6 class="mt-3">Item yang Diminta:</h6>
                ${itemsHtml}
            `;

      document.getElementById("detail-content").innerHTML = detailHtml;

      const approvalSection = document.getElementById("approval-section");
      const approvalButtons = document.getElementById("approval-buttons");

      if (main.status_permintaan === "Diajukan" && canApprove) {
        approvalSection.classList.remove("d-none");
        approvalButtons.classList.remove("d-none");
        approvalButtons.dataset.id = id;
      } else {
        approvalSection.classList.add("d-none");
        approvalButtons.classList.add("d-none");
      }

      detailModal.show();
    } catch (error) {
      /* Error ditangani oleh apiCall */
    }
  };

  // =====================================================================
  // Event Listeners
  // =====================================================================

  document
    .getElementById("btn-add-permintaan")
    ?.addEventListener("click", () => {
      const form = document.getElementById("permintaan-form");
      form.reset();
      const itemList = document.getElementById("item-list");
      itemList.innerHTML = "";
      itemList.appendChild(createItemRow());
      formModal.show();
    });

  document
    .getElementById("btn-save-permintaan")
    ?.addEventListener("click", async () => {
      const form = document.getElementById("permintaan-form");
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      const items = Array.from(document.querySelectorAll(".item-row")).map(
        (row) => {
          const isCustom = row.querySelector(".item-is-custom").checked;
          return {
            is_custom: isCustom,
            id_barang: !isCustom
              ? row.querySelector(".item-barang").value
              : null,
            nama_barang: isCustom
              ? row.querySelector(".item-barang-custom").value
              : null,
            jumlah: row.querySelector(".item-jumlah").value,
          };
        }
      );

      const data = {
        catatan: document.getElementById("catatan").value,
        items: items,
      };
      const tipe_permintaan = document.getElementById("tipe-permintaan-switch")
        .checked
        ? "pembelian"
        : "stok";

      try {
        const response = await apiCall("post", "/permintaan/api/create", {
          data,
          tipe_permintaan,
        });
        showToast("success", response.message);
        formModal.hide();
        loadRequests();
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    });

  document.getElementById("item-list")?.addEventListener("click", (e) => {
    if (e.target.closest(".btn-remove-item")) {
      if (document.querySelectorAll(".item-row").length > 1) {
        e.target.closest(".item-row").remove();
      }
    } else if (e.target.closest(".btn-add-item")) {
      document.getElementById("item-list").appendChild(createItemRow());
    }
  });

  document.getElementById("item-list")?.addEventListener("change", (e) => {
    if (e.target.classList.contains("item-is-custom")) {
      const row = e.target.closest(".item-row");
      const selectBarang = row.querySelector(".item-barang");
      const inputCustom = row.querySelector(".item-barang-custom");

      selectBarang.classList.toggle("d-none", e.target.checked);
      selectBarang.required = !e.target.checked;
      inputCustom.classList.toggle("d-none", !e.target.checked);
      inputCustom.required = e.target.checked;
    }
  });

  tableBody?.addEventListener("click", (e) => {
    const target = e.target.closest(".btn-detail");
    if (target) {
      showDetailModal(target.dataset.id);
    }
  });

  document
    .getElementById("btn-approve")
    ?.addEventListener("click", async () => {
      const id = document.getElementById("approval-buttons").dataset.id;
      const catatan = document.getElementById("approval-catatan").value;
      const items = Array.from(
        document.querySelectorAll(".item-approval-jumlah")
      ).map((input) => ({
        id: input.dataset.detailId,
        jumlah: input.value,
      }));

      try {
        const response = await apiCall("post", "/permintaan/api/approve", {
          id,
          catatan,
          items,
        });
        showToast("success", response.message);
        detailModal.hide();
        loadRequests();
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    });

  document.getElementById("btn-reject")?.addEventListener("click", async () => {
    const id = document.getElementById("approval-buttons").dataset.id;
    const catatan = document.getElementById("approval-catatan").value;

    if (!catatan) {
      showToast("warning", "Catatan penolakan wajib diisi.");
      return;
    }

    const confirmed = await showConfirmation({
      text: "Anda yakin ingin menolak permintaan ini?",
    });
    if (confirmed) {
      try {
        const response = await apiCall("post", "/permintaan/api/reject", {
          id,
          catatan,
        });
        showToast("success", response.message);
        detailModal.hide();
        loadRequests();
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    }
  });

  // =====================================================================
  // Fungsi Helper Lokal
  // =====================================================================
  const getStatusColor = (status) => {
    const colors = {
      Diajukan: "secondary",
      Disetujui: "success",
      Ditolak: "danger",
      Selesai: "primary",
      "Diproses Pembelian": "warning",
      "Sudah Dibeli": "info",
    };
    return colors[status] || "dark";
  };

  // =====================================================================
  // Inisialisasi Halaman
  // =====================================================================
  loadAvailableItems();
  loadRequests();
});
