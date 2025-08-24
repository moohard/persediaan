document.addEventListener("DOMContentLoaded", () => {
  const createModalEl = document.getElementById("permintaan-modal");
  const detailModalEl = document.getElementById("detail-modal");
  if (!createModalEl || !detailModalEl) return;

  const createModal = new bootstrap.Modal(createModalEl);
  const detailModal = new bootstrap.Modal(detailModalEl);
  const form = document.getElementById("form-permintaan");
  const itemList = document.getElementById("item-list");
  const btnAddItem = document.getElementById("btn-add-item");
  const btnCreatePermintaan = document.getElementById("btn-create-permintaan");
  const itemRowTemplate = document.getElementById("item-row-template");
  const tableBody = document.getElementById("permintaan-table-body");

  const loadPermintaan = async () => {
    try {
      const response = await axios.get("/permintaan/api/getAll");
      let html = "";
      if (response.data.data.length > 0) {
        response.data.data.forEach((item) => {
          let statusClass = "bg-secondary";
          if (item.status_permintaan === "Disetujui")
            statusClass = "bg-success";
          if (item.status_permintaan === "Ditolak") statusClass = "bg-danger";
          if (item.status_permintaan === "Diajukan")
            statusClass = "bg-warning text-dark";
          html += `
                        <tr>
                            <td>${item.kode_permintaan}</td>
                            <td>${new Date(
                              item.tanggal_permintaan
                            ).toLocaleDateString("id-ID", {
                              day: "2-digit",
                              month: "short",
                              year: "numeric",
                            })}</td>
                            <td>${item.nama_pemohon}</td>
                            <td>${item.jumlah_item}</td>
                            <td><span class="badge ${statusClass}">${
            item.status_permintaan
          }</span></td>
                            <td>
                                <button class="btn btn-sm btn-info btn-detail" data-id="${
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
      tableBody.innerHTML = html;
    } catch (error) {
      console.error(error);
    }
  };

  const addRow = () => {
    const templateContent = itemRowTemplate.content.cloneNode(true);
    itemList.appendChild(templateContent);
  };

  btnCreatePermintaan.addEventListener("click", () => {
    form.reset();
    itemList.innerHTML = "";
    addRow();
    createModal.show();
  });

  btnAddItem.addEventListener("click", addRow);
  itemList.addEventListener("click", (e) => {
    if (e.target && e.target.closest(".btn-remove-item")) {
      e.target.closest(".item-row").remove();
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const items = [];
    const selectedItems = new Set();
    let hasError = false;

    document.querySelectorAll(".item-row").forEach((row) => {
      const barangSelect = row.querySelector(".item-barang");
      const jumlahInput = row.querySelector(".item-jumlah");
      const selectedOption = barangSelect.options[barangSelect.selectedIndex];
      const stok = parseInt(selectedOption.dataset.stok, 10);
      const jumlah = parseInt(jumlahInput.value, 10);

      if (selectedItems.has(barangSelect.value)) {
        Swal.fire(
          "Error",
          "Barang yang sama tidak boleh dipilih lebih dari sekali.",
          "error"
        );
        hasError = true;
        return;
      }
      if (jumlah > stok) {
        Swal.fire(
          "Error",
          `Stok untuk ${selectedOption.text.split(" (")[0]} tidak mencukupi.`,
          "error"
        );
        hasError = true;
        return;
      }
      selectedItems.add(barangSelect.value);
      items.push({ id_barang: barangSelect.value, jumlah: jumlah });
    });

    if (hasError) return;

    const dataToSend = {
      catatan_pemohon: document.getElementById("catatan_pemohon").value,
      items: items,
    };

    Swal.fire({
      title: "Mengajukan Permintaan...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    try {
      const response = await axios.post("/permintaan/api/store", dataToSend);
      if (response.data.success) {
        createModal.hide();
        Swal.fire("Berhasil!", response.data.message, "success");
        loadPermintaan();
      }
    } catch (error) {
      const errorData = error.response?.data;
      let errorText = errorData?.message || "Terjadi kesalahan.";
      if (errorData?.errors) {
        errorText +=
          '<br><ul class="text-start mt-2">' +
          errorData.errors.map((err) => `<li>${err}</li>`).join("") +
          "</ul>";
      }
      Swal.fire("Oops...", errorText, "error");
    }
  });

  tableBody.addEventListener("click", async (e) => {
    const target = e.target.closest(".btn-detail");
    if (!target) return;

    const id = target.dataset.id;
    try {
      const response = await axios.get(`/permintaan/api/getDetail/${id}`);
      const { header, items } = response.data.data;

      let detailHtml = `
                <p><strong>Kode:</strong> ${header.kode_permintaan}</p>
                <p><strong>Pemohon:</strong> ${header.nama_pemohon}</p>
                <p><strong>Tanggal:</strong> ${new Date(
                  header.tanggal_permintaan
                ).toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "long",
                  year: "numeric",
                })}</p>
                <p><strong>Status:</strong> <span class="badge bg-info">${
                  header.status_permintaan
                }</span></p>
                <p><strong>Catatan Pemohon:</strong><br>${
                  header.catatan_pemohon || "-"
                }</p>
                <hr>
                <h6>Rincian Barang:</h6>
                <ul class="list-group">
            `;
      items.forEach((item) => {
        detailHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    ${item.nama_barang}
                    <span class="badge bg-primary rounded-pill">${item.jumlah_diminta}</span>
                </li>`;
      });
      detailHtml += "</ul>";

      document.getElementById("detail-modal-body").innerHTML = detailHtml;

      let footerHtml =
        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
      if (header.status_permintaan === "Diajukan") {
        footerHtml += `
                    <button type="button" class="btn btn-danger btn-process" data-action="reject" data-id="${id}">Tolak</button>
                    <button type="button" class="btn btn-success btn-process" data-action="approve" data-id="${id}">Setujui</button>
                `;
      }
      document.getElementById("detail-modal-footer").innerHTML = footerHtml;

      detailModal.show();
    } catch (error) {
      Swal.fire("Error", "Gagal memuat detail permintaan.", "error");
    }
  });

  document
    .getElementById("detail-modal-footer")
    .addEventListener("click", async (e) => {
      const target = e.target.closest(".btn-process");
      if (!target) return;

      const id = target.dataset.id;
      const action = target.dataset.action;
      const actionText = action === "approve" ? "menyetujui" : "menolak";

      const { value: catatan } = await Swal.fire({
        title: `Anda yakin ingin ${actionText} permintaan ini?`,
        input: "textarea",
        inputLabel: "Catatan (Opsional)",
        inputPlaceholder: "Masukkan alasan atau catatan Anda di sini...",
        showCancelButton: true,
        confirmButtonText: `Ya, ${actionText}!`,
        cancelButtonText: "Batal",
      });

      if (catatan !== undefined) {
        try {
          const response = await axios.post("/permintaan/api/process", {
            id,
            action,
            catatan_penyetuju: catatan,
          });
          if (response.data.success) {
            detailModal.hide();
            Swal.fire("Berhasil!", response.data.message, "success");
            loadPermintaan();
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

  loadPermintaan();
});
