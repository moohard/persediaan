document.addEventListener("DOMContentLoaded", () => {
  const roleList = document.getElementById("role-list");
  const permissionContainer = document.getElementById("permission-container");
  const selectedRoleName = document.getElementById("selected-role-name");
  const btnSavePermissions = document.getElementById("btn-save-permissions");
  let currentRoleId = null;

  const loadPermissions = async (roleId) => {
    if (!roleId) return;
    currentRoleId = roleId;

    // Tandai role yang aktif
    document
      .querySelectorAll("#role-list a")
      .forEach((el) => el.classList.remove("active"));
    document
      .querySelector(`#role-list a[data-role-id="${roleId}"]`)
      .classList.add("active");
    selectedRoleName.textContent = document
      .querySelector(`#role-list a[data-role-id="${roleId}"]`)
      .textContent.trim();

    try {
      const response = await apiCall(
        "get",
        `/hakakses/api/getPermissions?id_role=${roleId}`
      );
      const permissionsByGroup = response.data;
      let html = "";

      if (Object.keys(permissionsByGroup).length > 0) {
        for (const group in permissionsByGroup) {
          html += `<h6 class="mt-3 text-primary">${e(group)}</h6>`;
          permissionsByGroup[group].forEach((perm) => {
            html += `
                            <div class="form-check form-switch">
                                <input class="form-check-input permission-check" type="checkbox" role="switch" 
                                       id="perm-${perm.id_permission}" value="${
              perm.id_permission
            }" 
                                       ${perm.diizinkan == 1 ? "checked" : ""}>
                                <label class="form-check-label" for="perm-${
                                  perm.id_permission
                                }">
                                    ${e(perm.nama_permission)}
                                    <small class="d-block text-muted">${e(
                                      perm.deskripsi_permission
                                    )}</small>
                                </label>
                            </div>
                        `;
          });
        }
        btnSavePermissions.classList.remove("d-none");
      } else {
        html =
          '<p class="text-muted text-center">Tidak ada izin yang tersedia.</p>';
        btnSavePermissions.classList.add("d-none");
      }
      permissionContainer.innerHTML = html;
    } catch (error) {
      permissionContainer.innerHTML =
        '<p class="text-danger text-center">Gagal memuat data izin.</p>';
    }
  };

  const savePermissions = async () => {
    if (!currentRoleId) {
      showToast("warning", "Pilih peran terlebih dahulu.");
      return;
    }

    const selectedPermissions = Array.from(
      document.querySelectorAll(".permission-check:checked")
    ).map((checkbox) => checkbox.value);

    const confirmed = await showConfirmation({
      text: "Anda yakin ingin menyimpan perubahan hak akses untuk peran ini?",
    });

    if (confirmed) {
      try {
        const response = await apiCall(
          "post",
          "/hakakses/api/updatePermissions",
          {
            id_role: currentRoleId,
            permissions: selectedPermissions,
          }
        );
        showToast("success", response.message);
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    }
  };

  roleList?.addEventListener("click", (e) => {
    e.preventDefault();
    const target = e.target.closest("a");
    if (target) {
      loadPermissions(target.dataset.roleId);
    }
  });

  btnSavePermissions?.addEventListener("click", savePermissions);
});
