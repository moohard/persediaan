/**
 * Main JavaScript File
 * Berisi konfigurasi global untuk aplikasi.
 */
// Konfigurasi default untuk Axios
// Ini akan secara otomatis menambahkan header CSRF token ke setiap
// permintaan AJAX yang dikirim, yang penting untuk keamanan.
const csrfToken = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute("content");

if (csrfToken) {
  axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
  axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
} else {
  console.error("CSRF token not found. AJAX requests will fail.");
}
