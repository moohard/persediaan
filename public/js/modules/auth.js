document.addEventListener("DOMContentLoaded", () => {

  const loginForm = document.getElementById("login-form");
  if (!loginForm) return;
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(loginForm);
    const data = Object.fromEntries(formData.entries());
    try {
      const response = await apiCall("post", "/auth/api/process_login", data);
      if (response.success) {
        showToast("success", response.message);
        // Beri sedikit waktu agar toast terlihat sebelum redirect
        setTimeout(() => {
          window.location.href = response.redirect_url;
        }, 1000);
      } else {
        showToast("error", response.message);
      }
    } catch (error) {
      console.log(error);
      // Pesan error sudah ditampilkan oleh helper apiCall
      // Reset captcha jika ada
      const captchaImg = document.getElementById("captcha-img");
      if (captchaImg) {
        captchaImg.src = "/auth/captcha?" + new Date().getTime();
        document.getElementById("captcha").value = "";
      }
    }
  });
});
