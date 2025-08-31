document.addEventListener("DOMContentLoaded", () => {
  const modal = initModal("pengguna-modal");
  if (!modal) return;

  const form = document.getElementById("pengguna-form");
  const modalLabel = document.getElementById("pengguna-modal-label");
  const tableBody = document.getElementById("pengguna-table-body");
  const canUpdate = document.body.dataset.permissions.includes(
    "user_management_update"
  );
  const canDelete = document.body.dataset.permissions.includes(
    "user_management_delete"
  );
console.log("canUpdate:", canUpdate, "canDelete:", canDelete);
  const populateSelect = async (selectId, endpoint, valueField, textField) => {
    const select = document.getElementById(selectId);
    try {
      const response = await apiCall("get", endpoint);
      select.innerHTML = `<option value="">Pilih...</option>`;
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
    document.getElementById("id_pengguna_encrypted").value = "";
    document.getElementById("is_active").checked = true;

    await Promise.all([
      populateSelect(
        "id_role",
        "/pengguna/api/getRoles",
        "id_role",
        "nama_role"
      ),
      populateSelect(
        "id_bagian",
        "/pengguna/api/getBagian",
        "id_bagian",
        "nama_bagian"
      ),
    ]);

    const passwordInput = document.getElementById("password");

    if (id) {
      modalLabel.textContent = "Edit Pengguna";
      passwordInput.required = false;
      try {
        const response = await apiCall("get", `/pengguna/api/getById/${id}`);
        const data = response.data;
        document.getElementById("id_pengguna_encrypted").value = id;
        document.getElementById("nama_lengkap").value = data.nama_lengkap;
        document.getElementById("username").value = data.username;
        document.getElementById("id_role").value = data.id_role;
        document.getElementById("id_bagian").value = data.id_bagian;
        document.getElementById("is_active").checked = data.is_active == 1;
      } catch (error) {
        return;
      }
    } else {
      modalLabel.textContent = "Tambah Pengguna";
      passwordInput.required = true;
    }
    modal.show();
  };

  const savePengguna = async () => {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    // Handle checkbox
    data.is_active = document.getElementById("is_active").checked;

    const url = data.id_pengguna_encrypted
      ? "/pengguna/api/update"
      : "/pengguna/api/create";

    try {
      const response = await apiCall("post", url, data);
      showToast("success", response.message);
      modal.hide();
      loadUsers();
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  const loadUsers = async () => {
    try {
      const response = await apiCall("get", "/pengguna/api/getAll");
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((user) => {
          let actionButtons = "";
          if (canUpdate) {
            actionButtons += `<button class="btn btn-sm btn-warning btn-edit" data-id="${user.id_pengguna_encrypted}"><i class="bi bi-pencil-square"></i> Edit</button> `;
          }
          if (canDelete && user.username !== "developer") {
            // Mencegah developer menghapus dirinya sendiri
            actionButtons += `<button class="btn btn-sm btn-danger btn-delete" data-id="${
              user.id_pengguna_encrypted
            }" data-nama="${e(
              user.nama_lengkap
            )}"><i class="bi bi-trash"></i> Hapus</button>`;
          }

          html += `
                        <tr>
                            <td>${e(user.nama_lengkap)}</td>
                            <td>${e(user.username)}</td>
                            <td>${e(user.nama_role)}</td>
                            <td>${e(user.nama_bagian)}</td>
                            <td><span class="badge bg-${
                              user.is_active ? "success" : "secondary"
                            }">${
            user.is_active ? "Aktif" : "Non-aktif"
          }</span></td>
                            <td>${actionButtons}</td>
                        </tr>
                    `;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">Belum ada data pengguna.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      /* Error ditangani apiCall */
    }
  };

  document
    .getElementById("btn-add-pengguna")
    ?.addEventListener("click", () => showModal());
  document
    .getElementById("btn-save-pengguna")
    ?.addEventListener("click", savePengguna);

  tableBody?.addEventListener("click", async (e) => {
    const editTarget = e.target.closest(".btn-edit");
    if (editTarget) {
      showModal(editTarget.dataset.id);
      return;
    }

    const deleteTarget = e.target.closest(".btn-delete");
    if (deleteTarget) {
      const confirmed = await showConfirmation({
        text: `Anda yakin ingin menghapus pengguna "${deleteTarget.dataset.nama}"?`,
      });
      if (confirmed) {
        try {
          const response = await apiCall("post", "/pengguna/api/delete", {
            id: deleteTarget.dataset.id,
          });
          showToast("success", response.message);
          loadUsers();
        } catch (error) {
          /* Error ditangani apiCall */
        }
      }
    }
  });

  loadUsers();
});
