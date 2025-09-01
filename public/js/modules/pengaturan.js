document.addEventListener("DOMContentLoaded", () => {
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
        Swal.fire(
          "Berhasil!",
          response.message || "Data transaksi telah dikosongkan.",
          "success"
        ).then(() => {
          // Muat ulang halaman untuk melihat efeknya
          window.location.reload();
        });
      } catch (error) {
        // Pesan error sudah ditangani oleh helper apiCall
      }
    }
  });
});
