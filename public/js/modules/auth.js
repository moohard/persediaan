document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("login-form");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    Swal.fire({
      title: "Memproses...",
      text: "Mohon tunggu sebentar.",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    try {
      // Axios sekarang sudah memiliki header default dari main.js
      const response = await axios.post("/auth/api/process_login", data);

      if (response.data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: response.data.message,
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          window.location.href = response.data.redirect_url;
        });
      }
    } catch (error) {
      const errorData = error.response?.data;
      let errorMessage =
        errorData?.message || "Terjadi kesalahan tidak terduga.";

      if (errorData?.errors) {
        errorMessage += '<br><ul class="text-start mt-2">';
        errorData.errors.forEach((err) => {
          errorMessage += `<li>${err}</li>`;
        });
        errorMessage += "</ul>";
      }

      Swal.fire({
        icon: "error",
        title: "Login Gagal",
        html: errorMessage,
      });
    }
  });
});
