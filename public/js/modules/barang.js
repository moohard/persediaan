document.addEventListener("DOMContentLoaded", () => {
  const modal = initModal("barang-modal");
  if (!modal) return;

  const form = document.getElementById("barang-form");
  const modalLabel = document.getElementById("barang-modal-label");
  const tableBody = document.getElementById("barang-table-body");
  const trashTableBody = document.getElementById("trash-table-body");

  const populateSelect = async (selectId, endpoint, valueField, textField) => {
    const select = document.getElementById(selectId);
    try {
      const response = await apiCall("get", endpoint);
      select.innerHTML = `<option value="">Pilih ${
        selectId.replace("id_", "") + "..."
      } </option>`;
      response.data.forEach((item) => {
        const option = document.createElement("option");
        option.value = item[valueField];
        option.textContent = item[textField];
        select.appendChild(option);
      });
    } catch (error) {
      console.error(`Gagal memuat data untuk ${selectId}:`, error);
    }
  };

  const showModal = async (id = null) => {
    form.reset();
    document.getElementById("id_barang_encrypted").value = "";

    await Promise.all([
      populateSelect(
        "id_kategori",
        "/barang/api/getKategori",
        "id_kategori",
        "nama_kategori"
      ),
      populateSelect(
        "id_satuan",
        "/barang/api/getSatuan",
        "id_satuan",
        "nama_satuan"
      ),
    ]);

    if (id) {
      modalLabel.textContent = "Edit Barang";
      try {
        const response = await apiCall("get", `/barang/api/getById/${id}`);
        const data = response.data;
        document.getElementById("id_barang_encrypted").value = id;
        document.getElementById("kode_barang").value = data.kode_barang;
        document.getElementById("nama_barang").value = data.nama_barang;
        document.getElementById("id_kategori").value = data.id_kategori;
        document.getElementById("id_satuan").value = data.id_satuan;
        document.getElementById("jenis_barang").value = data.jenis_barang;
      } catch (error) {
        return;
      }
    } else {
      modalLabel.textContent = "Tambah Barang";
    }
    modal.show();
  };

  const saveBarang = async () => {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const url = data.id_barang_encrypted
      ? "/barang/api/update"
      : "/barang/api/create";

    try {
      const response = await apiCall("post", url, data);
      showToast("success", response.message);
      modal.hide();
      loadBarang();
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const loadBarang = async () => {
    if (!tableBody) return;
    try {
      const response = await apiCall("get", "/barang/api/getAll");
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((item) => {
          html += `
                        <tr>
                            <td>${e(item.kode_barang)}</td>
                            <td>${e(item.nama_barang)}</td>
                            <td>${e(item.nama_kategori) || "-"}</td>
                            <td>${e(item.nama_satuan) || "-"}</td>
                            <td class="text-center">${e(item.stok_total)}</td>
                            <td>
                                <small class="d-block">Umum: ${e(
                                  item.stok_umum
                                )}</small>
                                <small class="d-block">Perkara: ${e(
                                  item.stok_perkara
                                )}</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${
                                  item.id_barang_encrypted
                                }"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${
                                  item.id_barang_encrypted
                                }" data-nama="${e(
            item.nama_barang
          )}"><i class="bi bi-trash"></i> Hapus</button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="7" class="text-center">Belum ada data barang.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const loadTrash = async () => {
    if (!trashTableBody) return;
    try {
      const response = await apiCall("get", "/barang/api/getTrash");
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((item) => {
          html += `
                        <tr>
                            <td>${e(item.kode_barang)}</td>
                            <td>${e(item.nama_barang)}</td>
                            <td>${e(item.nama_kategori) || "-"}</td>
                            <td>${e(item.nama_satuan) || "-"}</td>
                            <td>${new Date(item.deleted_at).toLocaleString(
                              "id-ID"
                            )}</td>
                            <td>
                                <button class="btn btn-sm btn-info btn-restore" data-id="${
                                  item.id_barang_encrypted
                                }" data-nama="${e(
            item.nama_barang
          )}"><i class="bi bi-arrow-counterclockwise"></i> Pulihkan</button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">Tidak ada data di sampah.</td></tr>';
      }
      trashTableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  document
    .getElementById("btn-add-barang")
    ?.addEventListener("click", () => showModal());
  document
    .getElementById("btn-save-barang")
    ?.addEventListener("click", saveBarang);

  const setupTableEventListeners = (container, actions) => {
    if (!container) return;
    container.addEventListener("click", async (e) => {
      for (const action of actions) {
        const target = e.target.closest(action.selector);
        if (target) {
          const id = target.dataset.id;
          if (action.callback) {
            action.callback(id);
          } else if (action.confirmText) {
            const confirmed = await showConfirmation({
              text: `${action.confirmText} "${target.dataset.nama}"?`,
            });
            if (confirmed) {
              try {
                const response = await apiCall("post", action.apiEndpoint, {
                  id,
                });
                showToast("success", response.message);
                action.successCallback();
              } catch (error) {
                /* Error ditangani apiCall */
              }
            }
          }
          return;
        }
      }
    });
  };

  if (tableBody) {
    setupTableEventListeners(tableBody, [
      { selector: ".btn-edit", callback: showModal },
      {
        selector: ".btn-delete",
        confirmText: "Anda yakin ingin menghapus",
        apiEndpoint: "/barang/api/delete",
        successCallback: loadBarang,
      },
    ]);
    loadBarang();
  }

  if (trashTableBody) {
    setupTableEventListeners(trashTableBody, [
      {
        selector: ".btn-restore",
        confirmText: "Anda yakin ingin memulihkan",
        apiEndpoint: "/barang/api/restore",
        successCallback: loadTrash,
      },
    ]);
    loadTrash();
  }
});
