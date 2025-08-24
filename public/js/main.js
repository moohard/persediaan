// File ini untuk skrip global yang berjalan di semua halaman
document.addEventListener("DOMContentLoaded", () => {
  // Ambil token CSRF dari meta tag di header
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

  // Konfigurasi default global untuk semua permintaan Axios
  if (typeof axios !== "undefined") {
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    axios.defaults.headers.common["X-CSRF-Token"] = csrfToken;
  }
});
