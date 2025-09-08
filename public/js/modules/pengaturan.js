document.addEventListener("DOMContentLoaded", () => {
  // === Bagian Konfigurasi Sistem ===
  const settingsContainer = document.getElementById("settings-container");
  const btnSaveSettings = document.getElementById("btn-save-settings");

  const loadSettings = async () => {
    if (!settingsContainer) return;
    try {
      const response = await apiCall("get", "/pengaturan/api/getAll");
      let html = "";
      if (response.data.length > 0) {
        response.data.forEach((setting) => {
          html += `
                        <div class="mb-3">
                            <label for="setting-${e(
                              setting.pengaturan_key
                            )}" class="form-label">${e(
            setting.deskripsi
          )}</label>
                            <input type="text" class="form-control" 
                                   id="setting-${e(setting.pengaturan_key)}" 
                                   name="${e(setting.pengaturan_key)}" 
                                   value="${e(setting.pengaturan_value)}">
                        </div>
                    `;
        });
      } else {
        html =
          '<p class="text-center text-muted">Tidak ada pengaturan yang tersedia.</p>';
      }
      settingsContainer.innerHTML = html;
    } catch (error) {
      settingsContainer.innerHTML =
        '<p class="text-center text-danger">Gagal memuat pengaturan.</p>';
    }
  };

  const saveSettings = async () => {
    const form = document.getElementById("pengaturan-form");
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    const confirmed = await showConfirmation({
      text: "Anda yakin ingin menyimpan perubahan pengaturan ini?",
    });
    if (confirmed) {
      try {
        const response = await apiCall("post", "/pengaturan/api/save", data);
        showToast("success", response.message);
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    }
  };

  btnSaveSettings?.addEventListener("click", saveSettings);
  loadSettings();

  // === Bagian Tindakan Berbahaya ===
  const btnClear = document.getElementById("btn-clear-transactions");

  btnClear?.addEventListener("click", async () => {
    const isConfirmed = await showConfirmation({
      title: "ANDA SANGAT YAKIN?",
      text: "Semua data transaksi akan dihapus permanen dan stok akan di-reset ke nol. Tindakan ini tidak bisa dibatalkan!",
      confirmButtonText: "Ya, Hapus Semua",
    });

    if (isConfirmed) {
      try {
        const response = await apiCall(
          "post",
          "/pengaturan/api/clearTransactions"
        );
        Swal.fire("Berhasil!", response.message, "success").then(() => {
          window.location.reload();
        });
      } catch (error) {
        /* Error ditangani oleh apiCall */
      }
    }
  });
});
