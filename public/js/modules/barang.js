document.addEventListener("DOMContentLoaded", function () {
  const deleteForms = document.querySelectorAll(".form-delete");

  deleteForms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      Swal.fire({
        title: "Anda yakin?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
