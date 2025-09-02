document.addEventListener("DOMContentLoaded", () => {
  const notificationDropdown = document.getElementById("notificationDropdown");
  const notificationList = document.getElementById("notification-list");
  const notificationBadge = document.getElementById("notification-badge");
  const notificationCount = document.getElementById("notification-count");

  if (!notificationDropdown) return;

  const fetchNotifications = async () => {
    try {
      const response = await apiCall("get", "/notifikasi/api/getUnread");
      const notifications = response.data;

      if (notifications.length > 0) {
        notificationCount.textContent = notifications.length;
        notificationBadge.classList.remove("d-none");

        let html = "";
        notifications.forEach((notif) => {
          html += `
                        <li>
                            <a class="dropdown-item notification-item" href="${
                              notif.tautan
                            }" data-id="${notif.id_notifikasi}">
                                <p class="mb-0 small">${e(notif.pesan)}</p>
                                <small class="text-muted">${new Date(
                                  notif.created_at
                                ).toLocaleString("id-ID")}</small>
                            </a>
                        </li>`;
        });
        html += '<li><hr class="dropdown-divider"></li>';
        html +=
          '<li><a class="dropdown-item text-center" href="#" id="mark-all-as-read">Tandai semua sudah dibaca</a></li>';
        notificationList.innerHTML = html;
      } else {
        notificationBadge.classList.add("d-none");
        // Gunakan <span> dengan kelas .dropdown-item-text untuk teks statis
        notificationList.innerHTML =
          '<li><span class="dropdown-item-text text-center text-muted">Tidak ada notifikasi baru.</span></li>';
      }
    } catch (error) {
      console.error("Gagal mengambil notifikasi:", error);
    }
  };

  const markAsRead = async (id = null) => {
    try {
      await apiCall("post", "/notifikasi/api/markAsRead", { id: id });
      // Muat ulang notifikasi setelah ditandai
      fetchNotifications();
    } catch (error) {
      // Error sudah ditangani oleh apiCall
    }
  };

  // Event listener untuk item notifikasi individual
  notificationList.addEventListener("click", async (e) => {
    // Tambahkan 'async'
    e.preventDefault();
    const target = e.target.closest(".notification-item");

    // Jika item notifikasi yang diklik
    if (target) {
      try {
        // Tunggu sampai proses markAsRead selesai di server
        await markAsRead(target.dataset.id);
        // Setelah berhasil, baru pindah halaman
        window.location.href = target.href;
      } catch (error) {
        // Jika gagal menandai, untuk sementara jangan pindah halaman
        // dan biarkan error tampil di console
        console.error("Gagal menandai notifikasi sebelum redirect:", error);
        // Anda bisa menampilkan pesan error kepada user jika perlu
      }
      return;
    }

    // Jika tombol "Tandai semua sudah dibaca" yang diklik
    const markAllTarget = e.target.closest("#mark-all-as-read");
    if (markAllTarget) {
      markAsRead(null);
    }
  });

  // Panggil pertama kali dan set interval untuk memeriksa notifikasi setiap 1 menit
  fetchNotifications();
  setInterval(fetchNotifications, 30000);
});
